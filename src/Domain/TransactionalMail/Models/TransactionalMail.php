<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Database\Factories\TransactionalMailFactory;
use Spatie\Mailcoach\Domain\Content\Models\Concerns\HasContentItems;
use Spatie\Mailcoach\Domain\Content\Models\Concerns\InteractsWithContentItems;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\RenderTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\InvalidTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer;
use Spatie\Mailcoach\Mailcoach;

class TransactionalMail extends Model implements HasContentItems
{
    public $table = 'mailcoach_transactional_mails';

    use HasFactory;
    use HasUuid;
    use InteractsWithContentItems;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $guarded = [];

    public $casts = [
        'store_mail' => 'boolean',
        'from' => 'string',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'replacers' => 'array',
    ];

    public function isValid(): bool
    {
        try {
            $this->validate();
        } catch (Exception) {
            return false;
        }

        return true;
    }

    public function validate(): void
    {
        $mailable = $this->getMailable();

        $mailable->render();
    }

    public function getMailable(): Mailable
    {
        $mailableClass = $this->test_using_mailable;

        if (! class_exists($mailableClass)) {
            throw InvalidTransactionalMail::mailableClassNotFound($this);
        }

        $traits = class_uses_recursive($mailableClass);

        if (! in_array(UsesMailcoachTemplate::class, $traits)) {
            throw InvalidTransactionalMail::mailableClassNotValid($this);
        }

        return $mailableClass::testInstance();
    }

    public function replacers(): Collection
    {
        return collect($this->replacers ?? [])
            ->map(function (string $replacerName): TransactionalMailReplacer {
                $replacerClass = config("mailcoach.transactional.replacers.{$replacerName}");

                if (is_null($replacerClass)) {
                    throw InvalidTransactionalMail::replacerNotFound($this, $replacerName);
                }

                if (! is_a($replacerClass, TransactionalMailReplacer::class, true)) {
                    throw InvalidTransactionalMail::invalidReplacer($this, $replacerName, $replacerClass);
                }

                return resolve($replacerClass);
            });
    }

    public function render(
        Mailable $mailable,
        array $replacements = [],
    ): string {
        /** @var RenderTemplateAction $action */
        $action = Mailcoach::getTransactionalActionClass('render_template', RenderTemplateAction::class);

        return $action->execute($this, $mailable, $replacements);
    }

    public function toString(): string
    {
        if (is_string($this->to)) {
            return $this->to;
        }

        return implode(',', $this->to ?? []);
    }

    public function ccString(): string
    {
        if (is_string($this->cc)) {
            return $this->cc;
        }

        return implode(',', $this->cc ?? []);
    }

    public function bccString(): string
    {
        if (is_string($this->bcc)) {
            return $this->bcc;
        }

        return implode(',', $this->bcc ?? []);
    }

    protected static function newFactory(): TransactionalMailFactory
    {
        return new TransactionalMailFactory();
    }
}
