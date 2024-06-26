<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReplyReceived extends Notification
{
    use Queueable;

    private User $user;
    private string $task_slug;
    private int $reply_id;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, $task_slug, $reply_id)
    {
        $this->user = $user;
        $this->task_slug = $task_slug;
        $this->reply_id = $reply_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, string>
     */
    public function toDatabase(): array
    {
        return [
            'reply_id' => $this->reply_id,
            'task_slug' => $this->task_slug,
            'user' => $this->user->username,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
