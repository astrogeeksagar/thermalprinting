<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    protected $fillable = ['name', 'type', 'connection_details', 'is_active'];

    public function getConnectionDetailsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setConnectionDetailsAttribute($value)
    {
        $this->attributes['connection_details'] = json_encode($value);
    }
}
