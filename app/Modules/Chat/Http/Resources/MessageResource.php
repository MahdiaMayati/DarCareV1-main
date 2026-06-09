<?php
// app/Modules/Chat/Http/Resources/MessageResource.php

namespace App\Modules\Chat\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'service_request_id' => $this->service_request_id,
            'sender'             => [
                'id'   => $this->sender_id,
                'type' => class_basename($this->sender_type),
            ],
            'body'       => $this->body,
            'read_at'    => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}
