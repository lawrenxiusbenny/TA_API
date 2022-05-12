<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class status_pesanan extends Model
{
    protected $primaryKey = "id_status_pesanan";
    protected $fillable = [
        'nama_status'
    ];
}
