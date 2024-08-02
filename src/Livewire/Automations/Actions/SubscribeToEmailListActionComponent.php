<?php

namespace Spatie\Mailcoach\Livewire\Automations\Actions;

use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionComponent;
use Spatie\ValidationRules\Rules\Delimited;

class SubscribeToEmailListActionComponent extends AutomationActionComponent
{
    use UsesMailcoachModels;

    public int|string $email_list_id;

    public bool $skip_confirmation = false;

    public bool $forward_tags = false;

    public string $new_tags = '';

    public array $emailListOptions;

    public function mount()
    {
        $this->emailListOptions = self::getEmailListClass()::query()
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getData(): array
    {
        return [
            'email_list_id' => $this->email_list_id,
            'skip_confirmation' => $this->skip_confirmation,
            'forward_tags' => $this->forward_tags,
            'new_tags' => $this->new_tags,
        ];
    }

    public function rules(): array
    {
        return [
            'email_list_id' => [
                'required',
                Rule::exists(self::getEmailListClass(), 'id'),
            ],
            'skip_confirmation' => 'boolean',
            'forward_tags' => 'boolean',
            'new_tags' => ['nullable', new Delimited('string')],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.subscribeToEmailListAction');
    }
}
