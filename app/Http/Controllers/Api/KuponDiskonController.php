<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\daftar_kupon_diskon;
use App\daftar_kupon_customer;

class KuponDiskonController extends Controller
{
    //create kupon diskon
    public function store(Request $request){       

        $data = new daftar_kupon_diskon;
        $data->nama_kupon = $request->nama_kupon;
        $data->persentase_potongan = $request->persentase_potongan;
        $data->jumlah_point_tukar = $request->jumlah_point_tukar;
        $data->deskripsi_kupon = $request->deskripsi_kupon;

        if($data->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tambah data kupon diskon',
                'OUT_DATA' => $data
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tambah data kupon diskon',
            'OUT_DATA' => null
        ]);
    }

    //Get All data
    public function index(){
        $kupons = daftar_kupon_diskon::where('status_hapus','=', 0)->get();

        if($kupons != null){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data kupon diskon',
                'OUT_DATA' => $kupons
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data kupon diskon',
            'OUT_DATA' => null
        ]);
    }

    //get data by id
    public function search($id){
        $kupon = daftar_kupon_diskon::where('id_kupon_diskon','=', $id)->first();
        
        if($kupon != null){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data kupon diskon',
                'OUT_DATA' => $kupon
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data kupon diskon',
            'OUT_DATA' => null
        ]);
    }

    //update data
    public function update(Request $request, $id){
        $kupon = daftar_kupon_diskon::where('id_kupon_diskon','=', $id)->first();
        if(is_null($kupon)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data kupon diskon tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $updateData = $request->all();

        $kupon->nama_kupon = $updateData['nama_kupon'];
        $kupon->persentase_potongan = $updateData['persentase_potongan'];
        $kupon->jumlah_point_tukar = $updateData['jumlah_point_tukar'];
        $kupon->deskripsi_kupon = $updateData['deskripsi_kupon'];


        if($kupon->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil ubah data kupon diskon',
                'OUT_DATA' => $kupon
            ]);
        } 
        
        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal ubah data kupon diskon',
            'OUT_DATA' => null
        ]);
    }

    //DELETE DATA
    public function deleteKupon($id){
        $matchThese = ['id_kupon_diskon' => $id];
        $kupon = daftar_kupon_diskon::where($matchThese)->first();

        if($kupon == null){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Tidak ada data kupon diskon',
                'OUT_DATA' => null
            ]);
        }

        $kupon->status_hapus = 1;

        if($kupon->save()){
            // ubah kupon customer yang belum dipakai, tapi sudah dihapus/ditiadakan oleh admin
            $matchThese2 = ['id_kupon_diskon' => $id,'id_status_kupon'=> 1];
            $kuponCustomer = daftar_kupon_customer::where($matchThese2)->get();
            if($kuponCustomer!=null){
                foreach($kuponCustomer as $kc){
                    $kc->id_status_kupon = 3;
                    $kc->status_hapus = 1;
                    $kc->save();
                }
            }

            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil hapus data kupon diskon',
                'OUT_DATA' => $kupon
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal hapus data kupon diskon (customer)',
            'OUT_DATA' => $kupon
        ]);
        
    }
}
