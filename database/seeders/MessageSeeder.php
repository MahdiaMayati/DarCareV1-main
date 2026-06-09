<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Chat\Models\Message; 
use App\Modules\Providers\Models\Provider; 
use App\Modules\Categories\Models\Category; 
use App\Modules\ServiceRequests\Models\ServiceRequest; 

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $requests = ServiceRequest::all();

        foreach ($requests as $request) {
            Message::create([
                'service_request_id' => $request->id,
                'sender_id' => $request->user_id,
                'sender_type' => User::class,
                'body' => 'Hello, I need help!',
            ]);
        }
    }
}
