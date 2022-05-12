<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jenis_menu extends Model
{
    protected $primaryKey = "id_jenis_menu";
    protected $fillable = [
        'jenis_menu'
    ];
}
