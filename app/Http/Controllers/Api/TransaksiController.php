<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\transaksi;
use App\pesanan;

class TransaksiController extends Controller
{
    //Create Transaksi
    public function store(Request $request){
        $transaksi = new transaksi;
        $transaksi->id_customer = $request->id_customer;
        $transaksi->id_karyawan = $request->id_karyawan;
        $transaksi->id_kupon_customer = $request->id_kupon_customer;
        $transaksi->total_harga = $request->total_harga;
        $transaksi->metode_pembayaran = $request->metode_pembayaran;
        $transaksi->nama_metode = $request->nama_metode;

        $date = new Carbon($request->date);

        $cekTransaksi = transaksi::whereDate('created_at',$date)->get();
        $urutan = $cekTransaksi->count()+1;

        if(count($cekTransaksi)<10){
            $id = 'ROESO-'.$date->format('Y').$date->format('m').$date->format('d').'-'."00".$urutan;    
        }else{
            $id = 'ROESO-'.$date->format('Y').$date->format('m').$date->format('d').'-'."0".$urutan;        
        }
        $transaksi->id_transaksi = $id;

        if($transaksi->save()){
            $matchThese=["id_customer"=> $request->id_customer, "status_selesai"=>0];

            $pesanan = pesanan::where($matchThese)->get();
            if($pesanan!=null){
                foreach($pesanan as $pes){
                    $pes->status_selesai = 1;
                    $pes->id_transaksi = $id;
                    $pes->save();
                }
            }

            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tambah data transaksi',
                'OUT_DATA' => $transaksi,
            ]);
        }else{
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal tambah data transaksi',
                'OUT_DATA' => null
            ]); 
        }
    }

    //Get All transaksi
    public function index(){
        $transaksi = DB::table('transaksis')        
        ->join('customers','transaksis.id_customer','customers.id_customer')
        ->join('users','users.id_karyawan','transaksis.id_karyawan')
        ->get();
        
        if(count($transaksi)){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data transaksi',
                'OUT_DATA' => $transaksi
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data transaksi',
            'OUT_DATA' => null
        ]);
    }

    //Get Transaksi by ID Customer
    public function searchByIdCustomer($id_customer){
        $matchThese=["transaksis.id_customer"=>$id_customer];

        $transaksi = DB::table('transaksis')        
        ->join('customers','transaksis.id_customer','customers.id_customer')
        ->join('users','users.id_karyawan','transaksis.id_karyawan')
        ->where($matchThese)
        ->get();

        if(count($transaksi)<1){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Tidak ada data transaksi',
                'OUT_DATA' => null
            ]);
        }

        return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data transaksi',
                'OUT_DATA' => $transaksi
        ]);
    }

    //ubah status pembayaran
    public function updateStatusPembayaran($id_transaksi){
        $matchThese = ['id_transaksi' => $id_transaksi];
        $transaksi = transaksi::where($matchThese)->first();

        if($transaksi == null){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Tidak ada data transaksi',
                'OUT_DATA' => null
            ]);
        }

        $transaksi->status_transaksi = "Lunas";

        if($transaksi->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil ubah status transaksi',
                'OUT_DATA' => $transaksi
            ]);
        }
    }

    //Delete Transaksi
    public function deleteTransaction($id_transaksi){
        $matchThese = ['id_transaksi' => $id_transaksi];
        $transaksi = transaksi::where($matchThese)->first();

        if($transaksi == null){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Tidak ada data transaksi',
                'OUT_DATA' => null
            ]);
        }

        if($transaksi->delete()){
            $pesanan = pesanan::where('id_transaksi','=',$id_transaksi)->get();
            if($pesanan!=null){
                foreach($pesanan as $pes){
                    $pes->status_selesai = 0;
                    $pes->id_transaksi = null;
                    $pes->save();
                }
            }

            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil hapus data transaksi',
                'OUT_DATA' => $transaksi
            ]);
        }
        
    }

    //Update Data Transaksi
    public function update(Request $request, $id){
        $matchThese=["id_transaksi"=>$id];
        $transaksi = transaksi::where($matchThese)->first();
        if(is_null($transaksi)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data transaksi tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }
        $transaksi->metode_pembayaran = $request->metode_pembayaran;
        $transaksi->nama_metode = $request->nama_metode;
        if($transaksi->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil ubah data transaksi',
                'OUT_DATA' => $transaksi
            ]);
        }else{
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal ubah data transaksi',
                'OUT_DATA' => null
            ]);
        }
    }
}
