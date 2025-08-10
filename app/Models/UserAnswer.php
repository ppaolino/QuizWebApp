<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'attempt_id',
        'player_id',
        'is_correct',
        'submitted_at'
    ];

    public function attempt() {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function player() {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
