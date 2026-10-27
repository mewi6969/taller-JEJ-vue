<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class RestablecerContrasenaNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $minutos = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Recupera tu contraseña - Taller JEJ')
            ->greeting('Hola, '.$notifiable->name)
            ->line('Recibimos una solicitud para cambiar la contraseña de tu cuenta en Taller JEJ.')
            ->action('Cambiar contraseña', $url)
            ->line("Este enlace vence en {$minutos} minutos.")
            ->line('Si no hiciste esta solicitud, ignora este mensaje: tu contraseña no cambiará.')
            ->salutation("Saludos,\nTaller JEJ");
    }
}
