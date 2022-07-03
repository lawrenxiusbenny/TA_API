<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class transaksi extends Model
{
    protected $primaryKey = "id_transaksi";

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_customer','id_karyawan','id_kupon_customer','total_harga','metode_pembayaran','nama_metode','status_transaksi','va_number_or_link_payment'
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
