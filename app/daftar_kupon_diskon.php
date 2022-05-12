<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class daftar_kupon_diskon extends Model
{
    protected $primaryKey = "id_kupon_diskon";
    protected $fillable = [
        'nama_kupon','persentase_potongan','jumlah_point_tukar','deskripsi_kupon', 'status_hapus'
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
