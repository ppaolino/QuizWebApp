<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quiz';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'prompt_text',
        'max_errors',
        'created_by',
        'approved_by',
        'is_published',
        'created_at',
        'published_at',
        'language_id'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'quiz_id');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
    public function language()
    {
        return $this->belongsTo(LanguageAvailable::class, 'language_id');
    }
}
