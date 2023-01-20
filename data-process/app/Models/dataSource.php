<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSource extends Model
{
    protected $table = 'data_source_x';
    public $timestamps = false;
    use HasFactory;
}
