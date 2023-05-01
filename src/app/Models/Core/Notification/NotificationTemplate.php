<?php

namespace App\Models\Core\Notification;

use App\Models\Core\BaseModel;
use App\Models\Core\Notification\Traits\NotificationTemplateRelationship;
use App\Models\Core\Setting\Traits\NotificationTemplateRules;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use App\Notifications\Traits\Tag;


class NotificationTemplate extends BaseModel
{
    use NotificationTemplateRules, NotificationTemplateRelationship, DescriptionGeneratorTrait, Tag;

    protected $fillable = [
        'subject', 'custom_content', 'type'
    ];

    protected static $logAttributes = [
        'subject', 'custom_content', 'type'
    ];

    public function parse(array $vars = [], $subject = false)
    {
        $string = $this->attributes['custom_content'] ?? $this->attributes['default_content'];
        if ($subject) {
            $string = $this->attributes['subject'];
        }

        if ($this->attributes['type'] == 'database') {
            $vars = $this->systemTemplateModifier(
                array_merge(
                    $vars,
                    $this->commonTagForSystem()
                )
            );
        }

        return strtr($string, $vars);
    }

    public function parseSubject(array $vars)
    {
        return $this->parse(array_merge($vars, $this->commonForSubject()), true);
    }

}
