<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table='notifications';

    protected $fillable = [
        'type',
        'message',
        'date_envoi',
        'user_id',
        'pointage_id',
    ];

    protected $casts = [
        'date_envoi' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pointage()
    {
        return $this->belongsTo(Pointage::class);
    }
}
