<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'quiz_id',
        'player_id',
        'context_info',
        'team_id',
        'league_id'
    ];

    public function quiz() {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function player() {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function team() {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function league() {
        return $this->belongsTo(League::class, 'league_id');
    }
}
