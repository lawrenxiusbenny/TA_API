<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class menu extends Model
{
    protected $primaryKey = "id_menu";
    protected $fillable = [
        'id_status_menu', 'id_jenis_menu', 'nama_menu','harga_menu', 'deskripsi_menu', 'gambar_menu', 'status_hapus'
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
