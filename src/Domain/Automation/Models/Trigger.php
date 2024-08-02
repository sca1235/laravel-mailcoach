<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\TriggerFactory;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Trigger extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesDatabaseConnection;
    use UsesMailcoachModels;

    public $table = 'mailcoach_automation_triggers';

    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function () {
            cache()->forget('automation-triggers');
        });
    }

    public function setTriggerAttribute(AutomationTrigger $value)
    {
        $this->attributes['trigger'] = base64_encode(serialize($value));
    }

    public function getAutomationTrigger(): AutomationTrigger
    {
        return $this->trigger;
    }

    public function getTriggerAttribute(string $value): AutomationTrigger
    {
        if ($value === base64_encode(base64_decode($value, true))) {
            /** @var AutomationTrigger $trigger */
            $trigger = unserialize(base64_decode($value));
        } else {
            /** @var AutomationTrigger $trigger */
            $trigger = unserialize($value);
        }

        $trigger->uuid = $this->uuid;
        $trigger->setAutomation($this->automation);

        return $trigger;
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(self::getAutomationClass());
    }

    protected static function newFactory(): TriggerFactory
    {
        return new TriggerFactory();
    }
}
