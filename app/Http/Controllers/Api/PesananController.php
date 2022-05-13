<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\pesanan;
use App\menu;

class PesananController extends Controller
{
    //create pesanan
    public function store(Request $request){
        $matchThese=["id_menu"=>$request->id_menu, "id_customer"=> $request->id_customer, "status_selesai"=>0];
        $cekPesanan = pesanan::where($matchThese)->first();

        if($cekPesanan == null){
            $pesanan = new pesanan;
            $pesanan->id_menu = $request->id_menu;
            $pesanan->id_customer = $request->id_customer;
            $pesanan->jumlah_pesanan = $request->jumlah_pesanan;
            $pesanan->catatan = $request->catatan;
            
            $menu = menu::where('id_menu','=', $request->id_menu)->first();
            if($menu!=null){
                $pesanan->sub_total = $menu->harga_menu * $request->jumlah_pesanan;
            }else{
                $pesanan->sub_total = 0;
            }

            if($pesanan->save()){
                return response([
                    'OUT_STAT' => "T",
                    'OUT_MESSAGE' => 'Berhasil tambah data pesanan',
                    'OUT_DATA' => $pesanan
                ]);
            }else{
                return response([
                    'OUT_STAT' => "F",
                    'OUT_MESSAGE' => 'Gagal tambah data pesanan',
                    'OUT_DATA' => null
                ]);
            }
        }else{
            $jumlah_sebelum = $cekPesanan->jumlah_pesanan;
            $jumlah_sesudah = $jumlah_sebelum + $request->jumlah_pesanan;
            $cekPesanan->jumlah_pesanan = $jumlah_sesudah;

            $menu = menu::where('id_menu','=', $cekPesanan->id_menu)->first();
            if($menu!=null){
                $cekPesanan->sub_total = $menu->harga_menu * $cekPesanan->jumlah_pesanan;
            }
            $cekPesanan->id_status_pesanan = 1;

            if($cekPesanan->save()){
                return response([
                    'OUT_STAT' => "T",
                    'OUT_MESSAGE' => 'Berhasil ubah data pesanan',
                    'OUT_DATA' => $cekPesanan
                ]);
            }else{
                return response([
                    'OUT_STAT' => "F",
                    'OUT_MESSAGE' => 'Gagal ubah data pesanan',
                    'OUT_DATA' => null
                ]);
            }            
        }
    }

    //get all pesanan
    public function index(){
        $pesanan = DB::table('pesanans')
        ->join('menus','menus.id_menu','pesanans.id_menu')
        ->join('customers','customers.id_customer','pesanans.id_customer')
        ->join('status_pesanans','status_pesanans.id_status_pesanan','pesanans.id_status_pesanan')
        ->where('pesanans.status_selesai','=',0)
        ->orderBy('pesanans.created_at','ASC')
        ->orderBy('pesanans.id_status_pesanan','ASC')
        ->get();
        
        if(count($pesanan)){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data pesanan',
                'OUT_DATA' => $pesanan
            ]);
        }

        if(count($pesanan)<1){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Belum ada pesanan yang dilakukan',
                'OUT_DATA' => null
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil data pesanan',
            'OUT_DATA' => null
        ]);
    }

    //get pesanan by ID Customer and status_selesai = 0
    public function searchPesanan($id_customer){
        $matchThese = ["pesanans.status_selesai"=>0,"pesanans.id_customer"=>$id_customer];
        
        $pesanan = DB::table('pesanans')
        ->join('menus','menus.id_menu','pesanans.id_menu')
        ->join('customers','customers.id_customer','pesanans.id_customer')
        ->join('status_pesanans','status_pesanans.id_status_pesanan','pesanans.id_status_pesanan')
        ->where($matchThese)
        ->orderBy('pesanans.created_at','ASC')
        ->orderBy('pesanans.id_status_pesanan','ASC')
        ->get();
        
        $totalHarga = 0;
        foreach($pesanan as $pes){
            $totalHarga = $totalHarga + $pes->sub_total;
        }

        if(count($pesanan)){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data pesanan',
                'OUT_DATA' => $pesanan,
                'TOTAL_HARGA'=>$totalHarga
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Daftar pesanan masih kosong, silahkan tambah pesanan',
            'OUT_DATA' => null
        ]);
    }

    //get pesanan by ID Customer and status_selesai = 0
    public function searchPesananByTransaksi($id_transaksi){
        $matchThese = ["pesanans.id_transaksi"=>$id_transaksi];
        
        $pesanan = DB::table('pesanans')
        ->join('transaksis','transaksis.id_transaksi','pesanans.id_transaksi')
        ->join('menus','menus.id_menu','pesanans.id_menu')
        ->where($matchThese)
        ->orderBy('pesanans.created_at','ASC')
        ->get();
        
        $totalHarga = 0;
        foreach($pesanan as $pes){
            $totalHarga = $totalHarga + $pes->sub_total;
        }

        if(count($pesanan)){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data pesanan',
                'OUT_DATA' => $pesanan,
                'total_harga'=>$totalHarga,
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Daftar pesanan masih kosong, silahkan tambah pesanan',
            'OUT_DATA' => null,
            'total_harga'=>null,
        ]);
    }

    //edit pesanan by ID pesanan
    public function update(Request $request, $id){
        $matchThese=["id_pesanan"=>$id];
        $pesanan = pesanan::where($matchThese)->first();
        if(is_null($pesanan)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data pesanan tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $jumlah_sesudah = $request->jumlah_pesanan;
        $pesanan->jumlah_pesanan = $jumlah_sesudah;
        $pesanan->catatan = $request->catatan;

        $menu = menu::where('id_menu','=', $pesanan->id_menu)->first();
        if($menu!=null){
            $pesanan->sub_total = $menu->harga_menu * $jumlah_sesudah;
        }

        if($pesanan->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil ubah data pesanan',
                'OUT_DATA' => $pesanan
            ]);
        }else{
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal ubah data pesanan',
                'OUT_DATA' => null
            ]);
        }
    }

    //delete pesanan by ID pesanan
    public function delete($id){
        $matchThese=["id_pesanan"=>$id];
        $pesanan = pesanan::where($matchThese)->first();

        if($pesanan->delete()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Data pesanan berhasil dihapus',
                'OUT_DATA' => $pesanan
            ]);
        }else{
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data pesanan gagal dihapus',
                'OUT_DATA' => $pesanan
            ]);
        }
    }

    //ubah status penyajian
    public function editPenyajian($id){
        $pesanan = pesanan::where('id_pesanan','=', $id)->first();

        if(is_null($pesanan)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data pesanan tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $pesanan->id_status_pesanan = 2;

        if($pesanan->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil ubah status penyajian pesanan',
                'OUT_DATA' => $pesanan
            ]);
        }else{
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal ubah status penyajian pesanan',
                'OUT_DATA' => null
            ]);
        }
    }

    //checkout
    public function checkOut($id_customer){
        $matchThese=["id_customer"=>$id_customer,"status_selesai"=>0];
        $pesanan = pesanan::where($matchThese)->get();

        if(count($pesanan)<1){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data pesanan tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        foreach($pesanan as $pes){
            $pes->status_selesai = 1;
            $pes->save();
        }
        
        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Berhasil checkout',
            'OUT_DATA' => $pesanan
        ]);
    }
}
