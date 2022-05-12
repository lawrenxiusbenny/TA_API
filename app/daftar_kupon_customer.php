<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class daftar_kupon_customer extends Model
{
    protected $primaryKey = "id_kupon_customer";
    protected $fillable = [
        'id_customer','id_status_kupon','id_kupon_diskon'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }
}
