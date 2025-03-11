<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'bug_id', 'comment'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            $mentionedUsers = self::extractMentions($comment->comment);
            if (!empty($mentionedUsers)) {
                self::notifyMentionedUsers($mentionedUsers, $comment);
            }
        });
    }

    public static function extractMentions($content): array
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $content, $matches);
        return $matches[1] ?? [];
    }

    public static function notifyMentionedUsers($usernames, $comment): void
    {
        $users = User::whereIn('nickname', $usernames)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'message' => 'Вы были упомянуты в комментарии',
                'link' => "/bugs/{$comment->bug_id}#comment-{$comment->id}",
            ]);
        }
    }

    public function files(): HasMany
    {
        return $this->hasMany(CommentFile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
