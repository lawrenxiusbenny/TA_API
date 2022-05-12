<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\status_menu;

class StatusMenuController extends Controller
{
    //get All status Name
    public function getAllName(){
        $data = status_menu::all();

        $names = [];

        if(count($data)>0){
            foreach($data as $name){
                array_push($names,$name->nama_status);
            }

            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil daftar status menu',
                'OUT_DATA' => $names,
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil daftar status menu',
            'OUT_DATA' => null
        ]);
    }

    public function getIdByName($name){
        $matchThese = ['nama_status' => $name];
        $data = status_menu::where($matchThese)->first();
        if($data){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil id status menu',
                'OUT_DATA' => $data->id_status_menu,
            ]);
        }

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Gagal tampil id status menu',
            'OUT_DATA' => null,
        ]);
    }

    public function getNameById($id){
        $matchThese = ['id_status_menu' => $id];
        $data = status_menu::where($matchThese)->first();
        if($data){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil nama status',
                'OUT_DATA' => $data->nama_status,
            ]);
        }

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Gagal tampil nama role',
            'OUT_DATA' => null,
        ]);
    }
}
