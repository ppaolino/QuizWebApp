<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public function answers() {
        return $this->hasMany(QuizAnswer::class, 'team_id');
    }

    public function teams() {
        return $this->hasMany(Team::class, 'league_id');
    }
}
