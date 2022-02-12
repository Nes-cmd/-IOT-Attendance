<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendant extends Model
{
    protected $fillable = ['id', 'user_id','name', 'identifier', 'finger_id', 'attendance'];
}
