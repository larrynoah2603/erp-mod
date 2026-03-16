<?php

namespace App\Modules\Core\Traits;

trait HasAttachments
{
    public function attachments()
    {
        return $this->morphMany(\App\Modules\Core\Models\Attachment::class, 'attachable');
    }
}
