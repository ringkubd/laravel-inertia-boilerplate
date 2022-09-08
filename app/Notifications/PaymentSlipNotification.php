<?php

namespace App\Notifications;

use App\Models\PaymentSlip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Relative\LaravelExpoPushNotifications\ExpoPushNotifications;
use Relative\LaravelExpoPushNotifications\PushNotification;

class PaymentSlipNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private PaymentSlip $paymentSlip;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($paymentSlip)
    {
        $this->paymentSlip = $paymentSlip;
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
     * @return PushNotification
     */
    public function toExpoPushNotification($notifiable)
    {
        $status = match ($this->paymentSlip->status) {
            1 => "Approved",
            2 => "Rejected",
            default => "Pending",
        };

        $semester = match ($this->paymentSlip->semester){
            1 => "First semester",
            2 => "Second semester",
            3 => "Third semester",
            4 => "Fourth semester",
            5 => "Fifth semester",
            6 => "Sixth semester",
            7 => "Seventh semester",
            8 => "Eighth semester",
        };
        Log::info($this->paymentSlip->student);
        $feeType = str_replace("_", " ",$this->paymentSlip->fee_type);
        $message = "Dear {$this->paymentSlip?->student->name},
        Your {$semester} {$feeType} payment slip status is {$status}.
        ";
        return (new PushNotification())
            ->title(config('app.name').' - '. $semester." ".$feeType . " status.")
            ->body($message);
    }

}
