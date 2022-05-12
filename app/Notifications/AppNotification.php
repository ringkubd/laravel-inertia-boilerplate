<?php

namespace App\Notifications;

use App\Models\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Relative\LaravelExpoPushNotifications\ExpoPushNotifications;
use Relative\LaravelExpoPushNotifications\PushNotification;


class AppNotification extends Notification  implements ShouldQueue
{
    use Queueable;

    public $notice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [ExpoPushNotifications::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toExpoPushNotification($notifiable)
    {
        Log::info($this->notice->title);
        return (new PushNotification)
            ->title($this->notice->title)
//            ->data($notifiable)
            ->body($this->notice->body);
    }

}
