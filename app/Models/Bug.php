<?php

namespace App\Models;

use App\Events\BugCreated;
use App\Events\BugDeleted;
use App\Events\BugUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bug extends Model
{
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => BugCreated::class,
        'updated' => BugUpdated::class,
        'deleted' => BugDeleted::class,
    ];

    protected $fillable = ['title', 'description', 'priority', 'criticality', 'user_id', 'responsible_user_id', 'status', 'steps'];

    public function files(): HasMany
    {
        return $this->hasMany(BugFile::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc')->with(['user', 'files']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
}
