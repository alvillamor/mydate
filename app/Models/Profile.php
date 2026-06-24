<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    protected $fillable = ['user_id', 'age', 'bio', 'gender', 'looking_for', 'photo_path', 'photo_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->photo_path) {
            return Storage::url($this->photo_path);
        }

        if ($this->photo_url) {
            return $this->photo_url;
        }

        return asset($this->gender === 'Female' ? 'images/placeholders/female.png' : 'images/placeholders/male.png');
    }
}
