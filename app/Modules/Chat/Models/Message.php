<?php
// app/Modules/Chat/Models/Message.php

namespace App\Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    protected $fillable = [
        'service_request_id', 'sender_type', 'sender_id', 'body', 'read_at',
    ];

    protected $casts = ['read_at' => 'datetime'];

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }
}
