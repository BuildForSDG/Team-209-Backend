<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ReportAttachmentIdentifierResource
 * @package App\Http\Resources
 * @mixin \App\ReportsAttachment
 */
class ReportAttachmentIdentifierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'type' => 'reports_attachments',
        ];
    }
}
