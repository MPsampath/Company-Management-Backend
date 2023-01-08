<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceFaults extends Model
{
    use HasFactory;
    protected $table = 'attendance_faults';
    protected $primaryKey = 'fult_id';
    public $timestamps = false;
}
