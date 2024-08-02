<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $table = 'rental';
    protected $primaryKey = 'rental_id';
    public $timestamps = false; 
    protected $fillable = [
        'student_id', 'Araling_Panlipunan', 'English', 'Filipino', 'MAPEH', 'Mathematics', 'Science', 'TLE', 'release_date', 'return_date', 'AP_code', 'E_code','F_code','MA_code','M_code', 'S_code', 'T_code'
    ];
}
