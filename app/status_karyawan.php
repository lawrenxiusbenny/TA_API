<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class status_karyawan extends Model
{
    protected $primaryKey = "id_status_karyawan";
    protected $fillable = [
        'nama_status'
    ];
}
