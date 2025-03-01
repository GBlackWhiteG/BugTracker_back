<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bug extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'priority', 'criticality', 'user_id', 'responsible_user_id', 'status', 'steps'];

    public function files(): HasMany
    {
        return $this->hasMany(BugFile::class);
    }
}
