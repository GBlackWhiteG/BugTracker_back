<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification
{
    use Queueable;

    private $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
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
    public function toMail(object $notifiable): MailMessage
    {
        $clearComment = strip_tags($this->comment->comment);

        return (new MailMessage)
                    ->subject('Вас упомянули в сообщении')
                    ->line("Пользователь упомянул вас в комментарии: $clearComment")
                    ->action('Перейти к комментарию', env('FRONTEND_URL') . "/bugs/{$this->comment->bug_id}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Вас упомянули в комментарии",
            'link' => url("/post/{$this->comment->post_id}")
        ];
    }
}
