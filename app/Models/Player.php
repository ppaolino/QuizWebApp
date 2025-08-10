<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'height',
        'birthday',
        'birthplace',
        'position'
    ];

    public function quizAnswers() {
        return $this->hasMany(QuizAnswer::class, 'player_id');
    }

    public function userAnswer() {
        return $this->hasMany(UserAnswer::class, 'player_id');
    }
}

