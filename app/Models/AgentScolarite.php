<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentScolarite extends Model
{
    //
    protected $table = 'agent-scolarites';

    protected $fillable = [
        'user_id',
        'numero_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }

}
