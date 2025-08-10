<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'league_id'
    ];

    public function answers() {
        return $this->hasMany(QuizAnswer::class, 'team_id');
    }

    public function league() {
        return $this->belongsTo(League::class, 'league_id');
    }
}
