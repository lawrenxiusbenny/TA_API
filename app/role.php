<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    protected $primaryKey = "id_role";
    protected $fillable = [
        'nama_role'
    ];
}
