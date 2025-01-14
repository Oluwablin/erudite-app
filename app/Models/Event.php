<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'start_time', 'end_time', 'max_participants'];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
