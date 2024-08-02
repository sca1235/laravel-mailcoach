<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Exception;
use Illuminate\Foundation\Auth\User;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Jobs\CompleteSubscriberImportJob;
use Spatie\Mailcoach\Domain\Audience\Jobs\ImportSubscriberJob;
use Spatie\Mailcoach\Domain\Audience\Jobs\UnsubscribeMissingFromImportJob;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImportSubscribersAction
{
    use UsesMailcoachModels;

    protected ?User $user;

    protected bool $sendNotification = true;

    protected string $dateTime;

    protected ?Media $importFile;

    protected SubscriberImport $subscriberImport;

    protected ?TemporaryDirectory $temporaryDirectory;

    public function __construct(
        protected CreateSimpleExcelReaderAction $createSimpleExcelReaderAction,
        protected ImportHasEmailHeaderAction $importHasEmailHeaderAction,
    ) {
    }

    public function execute(SubscriberImport $subscriberImport, ?User $user = null, bool $sendNotification = true)
    {
        $this
            ->initialize($subscriberImport, $user, $sendNotification)
            ->importSubscribers()
            ->unsubscribeMissing();
    }

    protected function initialize(SubscriberImport $subscriberImport, ?User $user, bool $sendNotification = true): self
    {
        $this->subscriberImport = $subscriberImport;
        $this->user = $user;
        $this->dateTime = $subscriberImport->created_at->format('Y-m-d H:i:s');
        $this->importFile = $subscriberImport->getFirstMedia('importFile');
        $this->sendNotification = $sendNotification;

        return $this;
    }

    protected function importSubscribers(): self
    {
        try {
            $this->subscriberImport->update([
                'status' => SubscriberImportStatus::Importing,
                'imported_subscribers_count' => 0,
            ]);
            $this->subscriberImport->clearErrors();

            $reader = $this->createSimpleExcelReaderAction->execute($this->subscriberImport);

            if (! $this->importHasEmailHeaderAction->execute($reader->getHeaders() ?? [])) {
                $this->subscriberImport->addError(__mc('No header row found. Make sure your first row has at least 1 column with "email"'));
                $this->subscriberImport->update([
                    'status' => SubscriberImportStatus::Failed,
                ]);

                return $this;
            }

            $reader
                ->getRows()
                ->each(function (array $values) use (&$totalRows) {
                    $totalRows++;
                    dispatch(new ImportSubscriberJob($this->subscriberImport, $values));
                });

            if (is_null($totalRows)) {
                $this->subscriberImport->addError(__mc('Could not import subscribers. Check the formatting of the uploaded file.'));
                $this->subscriberImport->update([
                    'status' => SubscriberImportStatus::Failed,
                ]);
                $this->subscriberImport->saveErrorReport();

                return $this;
            }

            $this->subscriberImport->update(['status' => SubscriberImportStatus::Importing]);

            dispatch(new CompleteSubscriberImportJob($this->subscriberImport, $totalRows, $this->user, $this->sendNotification));
        } catch (Exception $exception) {
            report($exception);

            $this->subscriberImport->addError(
                __(
                    "Couldn't finish importing subscribers. This error occurred: :error",
                    ['error' => $exception->getMessage()]
                ),
            );

            $this->subscriberImport->update(['status' => SubscriberImportStatus::Failed]);
            $this->subscriberImport->saveErrorReport();
        }

        return $this;
    }

    protected function unsubscribeMissing(): self
    {
        if ($this->subscriberImport->fresh()->errorCount() || ! $this->subscriberImport['unsubscribe_others']) {
            return $this;
        }

        dispatch(new UnsubscribeMissingFromImportJob($this->subscriberImport));

        return $this;
    }
}
