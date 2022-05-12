<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\role;

class RoleController extends Controller
{
    //get All Role Name
    public function getAllName(){
        $data = DB::table('roles')
        ->select('id_role','nama_role')
        ->get();

        if(count($data)>0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data role',
                'OUT_DATA' => $data,
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil data role',
            'OUT_DATA' => null
        ]);
    }

    public function getIdByName($name){
        $matchThese = ['nama_role' => $name];
        $role = role::where($matchThese)->first();
        if($role){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil id role',
                'OUT_DATA' => $role->id_role,
            ]);
        }

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Gagal tampil id role',
            'OUT_DATA' => null,
        ]);
    }

    public function getNameById($id){
        $matchThese = ['id_role' => $id];
        $role = role::where($matchThese)->first();
        if($role){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil nama role',
                'OUT_DATA' => $role->nama_role,
            ]);
        }

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Gagal tampil nama role',
            'OUT_DATA' => null,
        ]);
    }
}
