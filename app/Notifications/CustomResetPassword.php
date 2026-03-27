<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CustomResetPassword extends Notification
{
    use Queueable;


    public $token;
    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {

        $token = $this->token;
        $email = $notifiable->getEmailForPasswordReset();

        $frontendUrl = config('app.frontend_url') . '/reset-password?' . http_build_query([
            'token' => $token,
            'email' => $email
        ]);

        // Option 2: If you want a signed URL for extra security , only works for Laravel's internal routes, not external frontend URLs. 
        // OR You have to manually create the signed parameters for frontend URLs.


        // $frontendUrl = URL::temporarySignedRoute(
        //     'password.reset', 
        //     now()->addMinutes(60),
        //     ['token' => $token, 'email' => $email]
        // );


        return (new MailMessage)
        ->subject('Reset Your Password - ' . config('app.name'))
        ->line('You are receiving this email because we received a password reset request for your account.')
        ->action('Reset Password', $frontendUrl)
        ->line('This password reset link will expire in 60 minutes.')
        ->line('If you did not request a password reset, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
