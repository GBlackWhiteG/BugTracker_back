<?php

namespace App\Models;

use App\Events\BugHistoryCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugHistory extends Model
{
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => BugHistoryCreated::class,
    ];

    protected $fillable = ['bug_id', 'user_id', 'field', 'old_value', 'new_value'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
