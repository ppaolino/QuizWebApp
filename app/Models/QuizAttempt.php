<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'started_at',
        'submitted_at'
    ];

    public function quiz() {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers() {
        return $this->hasMany(UserAnswer::class, 'attempt_id');
    }
}
