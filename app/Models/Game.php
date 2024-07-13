<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "owner_id",
        "entry_code",
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, "owner_id");
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, "game_question");
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "game_user");
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            $game->entry_code = Str::uuid();
        });
    }


    public function isValidEntryCode($code)
    {
        return $this->entry_code === $code;
    }
}
