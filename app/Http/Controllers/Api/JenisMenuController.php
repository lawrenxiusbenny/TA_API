<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\jenis_menu;

class JenisMenuController extends Controller
{
    //get All jenis Name
    public function getAllName(){
        $data = jenis_menu::all();

        $names = [];

        if(count($data)>0){
            foreach($data as $name){
                array_push($names,$name->jenis_menu);
            }

            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil daftar jenis menu',
                'OUT_DATA' => $names,
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil daftar jenis menu',
            'OUT_DATA' => null
        ]);
    }

    public function getIdByName($name){
        $matchThese = ['jenis_menu' => $name];
        $data = jenis_menu::where($matchThese)->first();
        if($data){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil id jenis menu',
                'OUT_DATA' => $data->id_jenis_menu,
            ]);
        }

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Gagal tampil id jenis menu',
            'OUT_DATA' => null,
        ]);
    }

    public function getNameById($id){
        $matchThese = ['id_jenis_menu' => $id];
        $data = jenis_menu::where($matchThese)->first();
        if($data){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil nama jenis menu',
                'OUT_DATA' => $data->nama_status,
            ]);
        }

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Gagal tampil nama jenis menu',
            'OUT_DATA' => null,
        ]);
    }
}
