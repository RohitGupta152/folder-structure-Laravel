<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail
{
    public function handle(UserRegistered $event)
    {
        Log::info('UserRegistered event received.', ['user_id' => $event->user->id]);

        Mail::to($event->user->email)->send(new WelcomeMail($event->user));
    }
}
