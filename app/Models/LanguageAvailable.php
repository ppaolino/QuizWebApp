<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageAvailable extends Model
{
    protected $table = 'languages_available';
    public $timestamps = false;

    protected $fillable = ['name', 'code'];

    /**
     * Get the quizzes associated with the language.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'language_id');
    }
}
