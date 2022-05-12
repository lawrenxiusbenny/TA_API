<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class status_kupon extends Model
{
    protected $primaryKey = "id_status_kupon";
    protected $fillable = [
        'nama_status'
    ];
}
