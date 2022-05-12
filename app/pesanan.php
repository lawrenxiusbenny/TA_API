<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class pesanan extends Model
{
    protected $primaryKey = "id_pesanan";
    protected $fillable = [
        'id_status_pesanan','id_transaksi','id_menu','id_customer','jumlah_pesanan', 'sub_total','catatan','status_selesai'
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
