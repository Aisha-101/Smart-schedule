<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Appointment $appointment,
        private string $confirmationUrl
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirm your appointment')
            ->line('Please confirm your appointment by clicking the button below.')
            ->line('Appointment time: '.$this->appointment->start_time)
            ->action('Confirm appointment', $this->confirmationUrl)
            ->line('This confirmation is valid only one day before your appointment.');
    }
}