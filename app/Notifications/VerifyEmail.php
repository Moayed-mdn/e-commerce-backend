<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        return (new MailMessage)
        ->subject('Verify Your Email Address - ' . config('app.name'))
        ->markdown('emails.verify-email', [ // Use your custom template
            'user' => $notifiable,
            'verificationUrl' => $verificationUrl,
            'logo' => asset('images/logo.png'), // Optional: custom logo
        ]);
    }



    
    protected function verificationUrl($notifiable)
    {
        $backendUrl = URL::temporarySignedRoute( 
            'v1.users.auth.verification.verify', 
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // 2. Get the frontend URL from .env (fallback to localhost:3000)
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');

        // 3. Extract the query parameters (expires, signature) from the backend URL
        $query = parse_url($backendUrl, PHP_URL_QUERY);
        Log::info('Backend URL: ' . $query);
        // 4. Construct the final Frontend URL
        return $frontendUrl . '/verify-email/' . $notifiable->getKey() . '/' . sha1($notifiable->getEmailForVerification()) . '?' . $query;


        return URL::temporarySignedRoute( 
            'v1.users.auth.verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function failed(\Exception $exception)
    {
        Log::error('VerifyEmail notification failed: ' . $exception->getMessage());
        Log::error('Stack trace: ' . $exception->getTraceAsString());
    }
}