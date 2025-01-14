<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['event_id', 'email'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
