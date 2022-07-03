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
        ->where('pesanans.status_selesai','=',1)
        ->orderBy('pesanans.id_status_pesanan','ASC')
        ->orderBy('pesanans.created_at','ASC')
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
    
    //get all pesanan pending
    public function indexPending(){
        $pesanan = DB::table('pesanans')
        ->join('menus','menus.id_menu','pesanans.id_menu')
        ->join('customers','customers.id_customer','pesanans.id_customer')
        ->join('status_pesanans','status_pesanans.id_status_pesanan','pesanans.id_status_pesanan')
        ->where('pesanans.status_selesai','=',0)
        ->orderBy('pesanans.id_status_pesanan','ASC')
        ->orderBy('pesanans.created_at','ASC')
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

        if($pesanan->id_status_pesanan == 1){
            $pesanan->id_status_pesanan = 2;    
        }else{
            $pesanan->id_status_pesanan = 1;
        }
        

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

    public function laporanHarian($id_menu,$bulan,$tahun){

        $tanggal=[];
        $dataHarian=[];
        $total=0;

        $matchThese = ['id_menu' => $id_menu];
        $menu = menu::where($matchThese)->first();

        if($bulan == 2){
            if($tahun % 4 == 0){
                $jumlahHari = 28;
            }else{
                $jumlahHari = 29;
            }
        } else if($bulan==0 || $bulan == 1 || $bulan == 3 || $bulan == 5 || $bulan == 7 || $bulan == 8 || $bulan == 10 || $bulan == 12){
            $jumlahHari = 31;
        }else {
            $jumlahHari = 30;
        }
        
        for($i=1; $i<=$jumlahHari;$i++){
            if($bulan == 1){
                $bulanString = "Januari";
            }else if($bulan == 2){
                $bulanString = "Februari";
            }else if($bulan == 3){
                $bulanString = "Maret";
            }else if($bulan == 4){
                $bulanString = "April";
            }else if($bulan == 5){
                $bulanString = "Mei";
            }else if($bulan == 6){
                $bulanString = "Juni";
            }else if($bulan == 7){
                $bulanString = "Juli";
            }else if($bulan == 8){
                $bulanString = "Agustus";
            }else if($bulan == 9){
                $bulanString = "September";
            }else if($bulan == 10){
                $bulanString = "Oktober";
            }else if($bulan == 11){
                $bulanString = "November";
            }else if($bulan == 12){
                $bulanString = "Desember";
            }
            $date = $tahun."-".$bulan."-".$i;
            $dateString = $i." ".$bulanString." ".$tahun;

            $jumlah= DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->selectRaw('ifnull(sum(pesanans.jumlah_pesanan), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('menus.id_menu','=',$id_menu)
            ->whereDay('pesanans.created_at','=',$i)
            ->whereMonth('pesanans.created_at','=',$bulan)
            ->whereYear('pesanans.created_at','=',$tahun)
            ->first();
            
            $jumlahTerjual= $jumlah->totalJumlah;

            array_push($tanggal,$dateString);
            array_push($dataHarian,$jumlahTerjual);

            $total = $total + ($jumlahTerjual*$menu->harga_menu);
            $tableHarian[$i]= array(
                "nomor"=>$i,
                "tanggal"=>$dateString,
                "jumlah"=>$jumlahTerjual,
                "pendapatan"=> ($jumlahTerjual*$menu->harga_menu)
            );
        }
        return response([
            'OUT_STAT'=> 'T',
            'OUT_MESSAGE'=> "Berhasil tampil data laporan",
            "OUT_DATA"=>$tableHarian,
            "OUT_JUMLAH"=>$dataHarian,
            "OUT_TANGGAL"=>$tanggal,
            'MENU_NAME'=>$menu->nama_menu,
            'MONTH_NAME'=>$bulanString,
            'TOTAL'=>$total,
        ]);
    }

    public function laporanBulanan($id_menu,$tahun){

        $data = [];
        $date=[];
        $total=0;

        $matchThese = ['id_menu' => $id_menu];
        $menu = menu::where($matchThese)->first();

        for($i=1;$i<=12;$i++){

            if($i == 1){
                $dateString = 'Januari '.$tahun;
            }else if($i == 2){
                $dateString = 'Februari '.$tahun;
            }else if($i == 3){
                $dateString = 'Maret '.$tahun;
            }else if($i == 4){
                $dateString = 'April '.$tahun;
            }else if($i == 5){
                $dateString = 'Mei '.$tahun;
            }else if($i == 6){
                $dateString = 'Juni '.$tahun;
            }else if($i == 7){
                $dateString = 'Juli '.$tahun;
            }else if($i == 8){
                $dateString = 'Agustus '.$tahun;
            }else if($i == 9){
                $dateString = 'September '.$tahun;
            }else if($i == 10){
                $dateString = 'Oktober '.$tahun;
            }else if($i == 11){
                $dateString = 'November '.$tahun;
            }else if($i == 12){
                $dateString = 'Desember '.$tahun;
            }

            $jumlah= DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->selectRaw('ifnull(sum(pesanans.jumlah_pesanan), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('menus.id_menu','=',$id_menu)
            ->whereMonth('pesanans.created_at','=',$i)
            ->whereYear('pesanans.created_at','=',$tahun)
            ->get();
            
            

            foreach($jumlah as $lap){
                array_push($data,$lap->totalJumlah);
                $jumlahTerjual= $lap->totalJumlah;
            }

            array_push($date,$dateString);

            $total = $total + ($jumlahTerjual*$menu->harga_menu);
            $tableBulanan[$i]= array(
                "nomor"=>$i,
                "tanggal"=>$dateString,
                "jumlah"=>$jumlahTerjual,
                "pendapatan"=> ($jumlahTerjual*$menu->harga_menu)
            );
        } 

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Berhasil tampil data laporan',
            'OUT_DATA' => $data,
            'OUT_TANGGAL'=> $date,
            'OUT_TABLE'=>$tableBulanan,
            'MENU_NAME'=>$menu->nama_menu,
            'TOTAL'=>$total,
        ]);
    }

    public function laporanTahunan($id_menu){

        $data = [];
        $tahun = [];
        $total = 0;
        $nomor = 0;

        $matchThese = ['id_menu' => $id_menu];
        $menu = menu::where($matchThese)->first();

        for($i=2020;$i<=date('Y');$i++){

            $yearString = 'Tahun '.$i;

            $jumlah = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->selectRaw('ifnull(sum(pesanans.jumlah_pesanan), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('menus.id_menu','=',$id_menu)
            ->whereYear('pesanans.created_at','=',$i)
            ->get();
            
            foreach($jumlah as $lap){
                array_push($data,$lap->totalJumlah);
                $jumlahTerjual= $lap->totalJumlah;
            }

            array_push($tahun,$yearString);
            $nomor++;

            $total = $total + ($jumlahTerjual*$menu->harga_menu);
            $tableTahunan[$i]= array(
                "nomor"=>$nomor,
                "tanggal"=>$yearString,
                "jumlah"=>$jumlahTerjual,
                "pendapatan"=> ($jumlahTerjual*$menu->harga_menu)
            );
        } 

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Berhasil tampil data laporan',
            'OUT_DATA' => $data,
            'OUT_TANGGAL'=> $tahun,
            'OUT_TABLE'=>$tableTahunan,
            'MENU_NAME'=>$menu->nama_menu,
            'TOTAL'=>$total,
        ]);
    }
    
    public function laporanSemua($tipe,$bulan,$tahun){
        //dapetin nama menu
        $matchThese=["status_hapus"=>0,"id_jenis_menu"=>$tipe];
        $menu = menu::where($matchThese)->get();
        $jumlahMenu = count($menu);
        
        if($bulan == 2){
            if($tahun % 4 == 0){
                $jumlahHari = 28;
            }else{
                $jumlahHari = 29;
            }
        } else if($bulan==0 || $bulan == 1 || $bulan == 3 || $bulan == 5 || $bulan == 7 || $bulan == 8 || $bulan == 10 || $bulan == 12){
            $jumlahHari = 31;
        }else {
            $jumlahHari = 30;
        }
        
        // for pertama untuk hitung setiap menunya
        for($i=0;$i<$jumlahMenu;$i++){
            
            $totalPenjualan[$i]=0;
            $penjualanPerHari=[];
            
            for($j=0;$j<$jumlahHari;$j++){
                //hitung penjualan menu di bulan dan tahun tertentu
                if($j < 10){
                    $date = $tahun."-".$bulan."-0".$j;    
                }else{
                    $date = $tahun."-".$bulan."-".$j;    
                }
                
                if($bulan==13){
                    $penjualanPerHari[$j] = DB::table('menus')
                    ->join('pesanans','menus.id_menu','pesanans.id_menu')
                    ->selectRaw('ifnull(sum(pesanans.jumlah_pesanan), 0) as totalJumlah')
                    ->where('menus.id_menu',"=",$menu[$i]->id_menu)
                    ->where('menus.status_hapus',"=",0)
                    ->where('pesanans.status_selesai','=',1)
                    ->where('pesanans.id_transaksi','!=',null)
                    ->where(function($query){
                              $query->whereMonth('pesanans.created_at', 1)
                                    ->orWhereMonth('pesanans.created_at', 2)
                                    ->orWhereMonth('pesanans.created_at', 3)
                                    ->orWhereMonth('pesanans.created_at', 4)
                                    ->orWhereMonth('pesanans.created_at', 5)
                                    ->orWhereMonth('pesanans.created_at', 6)
                                    ->orWhereMonth('pesanans.created_at', 7)
                                    ->orWhereMonth('pesanans.created_at', 8)
                                    ->orWhereMonth('pesanans.created_at', 9)
                                    ->orWhereMonth('pesanans.created_at', 10)
                                    ->orWhereMonth('pesanans.created_at', 11)
                                    ->orWhereMonth('pesanans.created_at', 12);
                            })
                    ->whereDay('pesanans.created_at',$j)
                    ->whereYear('pesanans.created_at','=',$tahun)
                    ->first();  
                }else{
                    $penjualanPerHari[$j] = DB::table('menus')
                    ->join('pesanans','menus.id_menu','pesanans.id_menu')
                    ->selectRaw('ifnull(sum(pesanans.jumlah_pesanan), 0) as totalJumlah')
                    ->where('menus.id_menu',"=",$menu[$i]->id_menu)
                    ->where('menus.status_hapus',"=",0)
                    ->where('pesanans.status_selesai','=',1)
                    ->where('pesanans.id_transaksi','!=',null)
                    ->whereDate('pesanans.created_at','=',$date)
                    ->first();  
                }
                $totalPenjualan[$i] = $totalPenjualan[$i] + $penjualanPerHari[$j]->totalJumlah;
            }   
            $jumlahTertinggi = max($penjualanPerHari);
            
            $penjualan[$i] = array(
                    "nomor"=>$i+1,
                    "nama_menu"=>$menu[$i]->nama_menu,
                    "penjualanTertinggi"=> $jumlahTertinggi,
                    "totalPenjualan"=>$totalPenjualan[$i],
                    "jumlahHari"=>$jumlahHari,
            );
        }
        
        return response([
            'message'=> "tampil data penjualan",
            "data"=>$penjualan,
        ]);
    }
}
