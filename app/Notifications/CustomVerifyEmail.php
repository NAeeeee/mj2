<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    protected $notifiable;

    /**
     * Create a new notification instance.
     */
    public function __construct($notifiable)
    {
        $this->notifiable = $notifiable;
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
        $verifyUrl = $this->verificationUrl($this->notifiable);


        return (new MailMessage)
                    ->subject('이메일 인증 (테스트용)')
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', $verifyUrl)
                    ->line('Thank you for using our application!');
    }

    protected function verificationUrl($notifiable)
    {
        $temporarySignedRoute = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
        return config('app.url') . parse_url($temporarySignedRoute, PHP_URL_PATH) . '?' . parse_url($temporarySignedRoute, PHP_URL_QUERY);
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
