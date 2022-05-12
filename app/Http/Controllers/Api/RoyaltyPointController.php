<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\royalty_point;
use App\customer;
class RoyaltyPointController extends Controller
{
    //create data
    public function store(Request $request){
        $royalty_point = royalty_point::all();
        $urutan = $royalty_point->count()+1;

        if(count($royalty_point)<10){
            $id = $id = 'ROYPOI-00'.$urutan;
        }else{
            $id = $id = 'ROYPOI-0'.$urutan;
        }
       
        $data = new royalty_point;
        $data->id_royalty_point = $id;
        $data->jumlah_point = $request->jumlah_point;

        $customer = customer::where('id_customer','=', $request->id_customer)->first();
        $customer->id_royalty_point = $id;
        if($data->save() && $customer->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tambah data royalty point',
                'OUT_DATA' => $data
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tambah data royalty point',
            'OUT_DATA' => null
        ]);
    }

    //Get All royalty point
    public function index(){
        $points = DB::table('royalty_points')        
        ->join('customers','customers.id_royalty_point','royalty_points.id_royalty_point')
        ->select('royalty_points.id_royalty_point','customers.nama_customer','customers.created_at','royalty_points.jumlah_point')
        ->where('customers.status_hapus','=',0)
        ->get();
        
        if(count($points)){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data royalty point',
                'OUT_DATA' => $points
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data royalty point',
            'OUT_DATA' => null
        ]);
    }

    //get data by id
    public function search($id){
        $points = royalty_point::where('id_royalty_point','=', $id)->first();
        
        if($points != null){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data royalty point',
                'OUT_DATA' => $points
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data royalty point',
            'OUT_DATA' => null
        ]);
    }

    //update data
    public function update(Request $request, $id){
        $point = royalty_point::where('id_royalty_point','=', $id)->first();
        if(is_null($point)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data royalty point tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $updateData = $request->all();

        $point->jumlah_point = $updateData['jumlah_point'];

        if($point->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil ubah data royalty point',
                'OUT_DATA' => $point
            ]);
        } 
        
        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal ubah data royalty point',
            'OUT_DATA' => null
        ]);
    }

    //DELETE DATA
    public function deletePoint($id){
        $matchThese = ['id_royalty_point' => $id];
        $point = royalty_point::where($matchThese)->first();

        if($point == null){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Tidak ada data royalty point',
                'OUT_DATA' => null
            ]);
        }

        if($point->delete()){
            $customer = customer::where('id_royalty_point','=',$id)->get();
            if($customer!=null){
                foreach($customer as $cus){
                    $cus->id_royalty_point = null;
                    $cus->save();
                }
            }

            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil hapus data royalty point',
                'OUT_DATA' => $point
            ]);
        }
        
    }
}
