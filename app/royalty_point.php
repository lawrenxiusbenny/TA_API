<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; 

class royalty_point extends Model
{
    protected $primaryKey = "id_royalty_point";
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'jumlah_point'
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
