<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class status_menu extends Model
{
    protected $primaryKey = "id_status_menu";
    protected $fillable = [
        'nama_status'
    ];
}
