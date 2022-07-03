<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\transaksi;
use App\pesanan;
use App\daftar_kupon_customer;
use App\royalty_point;
use App\customer;

use \DateTime;

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
        $transaksi->status_transaksi = $request->status_transaksi;
        $transaksi->va_number_or_link_payment = $request->va_number_or_link_payment;
        
        
        $tz = 'Asia/Bangkok';
        $tz_obj = new \DateTimeZone($tz);
        $today = new DateTime("now", $tz_obj);
        $created_at = date_format($today,'Y-m-d H:i:s');
        $transaksi->created_at = $created_at;
        
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
            if($request->device != "web"){
                
                if($request->status_transaksi == 'Lunas'){
                    $customer = customer::where('id_customer','=',$request->id_customer)->first();
                    
                    $jumlahPoint = 0;
                    
                    if($request->total_harga >= 10000){
                        $jumlahPoint = floor($request->total_harga/10000);
                    }
                    
                    if($customer->id_royalty_point == null){
                        $royalty_point = royalty_point::all();
                        $urutan = $royalty_point->count()+1;
                
                        $id = 'ROYPOI-'.mt_rand(100000,999999);
                       
                        $data = new royalty_point;
                        $data->id_royalty_point = $id;
                        $data->jumlah_point = $jumlahPoint;
                
                        $customer = customer::where('id_customer','=', $request->id_customer)->first();
                        $customer->id_royalty_point = $id;
                        
                        if(!$data->save() || !$customer->save()){
                            return response([
                                'OUT_STAT' => "F",
                                'OUT_MESSAGE' => 'Checkout berhasil, tapi penambahan royalty point gagal, silahkan hubungi pihak rumah makan',
                                'OUT_DATA' => $data
                            ]);
                        }
                    }else{
                        $point = royalty_point::where('id_royalty_point','=',$customer->id_royalty_point)->first();
                        
                        $point->jumlah_point = $point->jumlah_point + $jumlahPoint;
                        
                        if(!$point->save()){
                            return response([
                                'OUT_STAT' => "F",
                                'OUT_MESSAGE' => 'Checkout berhasil, tapi penambahan royalty point gagal, silahkan hubungi pihak rumah makan',
                                'OUT_DATA' => $data
                            ]);
                        }
                    }  
                }
                
                $matchThese=["id_customer"=> $request->id_customer, "status_selesai"=>0];
                $pesanan = pesanan::where($matchThese)->get();
                if($pesanan!=null){
                    foreach($pesanan as $pes){
                        $pes->status_selesai = 1;
                        $pes->id_transaksi = $id;
                        $pes->nomor_meja = $request->nomor_meja;
                        $pes->save();
                    }
                }
                
                if($request->id_kupon_customer != 0){
                    $kupon = daftar_kupon_customer::where('id_kupon_customer','=',$request->id_kupon_customer)->first();
                    
                    if($kupon == null){
                        return response([
                            'OUT_STAT' => "F",
                            'OUT_MESSAGE' => 'Checkout berhasil, tetapi perubahan status kupon diskon bermasalah. Silahkan hubungi pihak rumah makan',
                            'OUT_DATA' => $transaksi,
                        ]);
                    }
                    
                    $kupon->id_status_kupon = 2;
                    if($kupon->save()){
                        if($request->status_transaksi == "Belum Lunas"){
                            return response([
                                'OUT_STAT' => "T",
                                'OUT_MESSAGE' => 'Checkout berhasil, silahkan bayar di kasir atau pada sistem pembayaran online yang telah dipilih',
                                'OUT_DATA' => $transaksi,
                            ]);            
                        }else{
                            return response([
                                'OUT_STAT' => "T",
                                'OUT_MESSAGE' => 'Checkout berhasil, terima kasih atas pesanannya',
                                'OUT_DATA' => $transaksi,
                            ]);            
                        }
                        
                    }
                }
                
                if($request->status_transaksi == "Belum Lunas"){
                    return response([
                        'OUT_STAT' => "T",
                        'OUT_MESSAGE' => 'Checkout berhasil, silahkan bayar di kasir atau pada sistem pembayaran online yang telah dipilih',
                        'OUT_DATA' => $transaksi,
                    ]);            
                }else{
                    return response([
                        'OUT_STAT' => "T",
                        'OUT_MESSAGE' => 'Checkout berhasil, terima kasih atas pesanannya',
                        'OUT_DATA' => $transaksi,
                    ]);            
                }
            }else{
                $count = count($request->list_id_pesanan);
                for( $i = 0; $i < $count; $i++){
                    $idPesanan = $request->list_id_pesanan[$i];
                    $matchThese=["id_pesanan"=>$idPesanan];
                    $pesanan = pesanan::where($matchThese)->get();
                    if($pesanan!=null){
                        foreach($pesanan as $pes){
                            $pes->status_selesai = 1;
                            $pes->id_transaksi = $id;
                            $pes->nomor_meja = $request->nomor_meja;
                            $pes->save();
                        }
                    }
                }
                
                if($request->total_harga >= 10000){
            
                    $customer = customer::where('id_customer','=',$request->id_customer)->first();
                    if($customer == null){
                        return response([
                            'OUT_STAT' => "T",
                            'OUT_MESSAGE' => 'Berhasil tambah data transaksi, tapi terjadi kesalahan dalam perubahan royalty point',
                            'OUT_DATA' => null
                        ]);
                    }
                    
                    $jumlahPoint = 0;
                            
                    $jumlahPoint = floor($request->total_harga/10000);
                    
                    if($customer->id_royalty_point == null){
                        $royalty_point = royalty_point::all();
                        $urutan = $royalty_point->count()+1;
                
                        $id = 'ROYPOI-'.mt_rand(100000,999999);
                       
                        $data = new royalty_point;
                        $data->id_royalty_point = $id;
                        $data->jumlah_point = $jumlahPoint;
                
                        $customer2 = customer::where('id_customer','=', $transaksi->id_customer)->first();
                        $customer2->id_royalty_point = $id;
                        
                        if(!$data->save() || !$customer2->save()){
                            return response([
                               'OUT_STAT' => "T",
                                'OUT_MESSAGE' => 'Berhasil tambah data transaksi, tapi terjadi kesalahan dalam perubahan royalty point',
                                'OUT_DATA' => null
                            ]);
                        }
                    }else{
                        $point = royalty_point::where('id_royalty_point','=',$customer->id_royalty_point)->first();
                        
                        $point->jumlah_point = $point->jumlah_point + $jumlahPoint;
                        
                        if(!$point->save()){
                            return response([
                                'OUT_STAT' => "T",
                                'OUT_MESSAGE' => 'Berhasil tambah data transaksi, tapi terjadi kesalahan dalam perubahan royalty point',
                                'OUT_DATA' => null
                            ]);
                        }
                    }  
                }
                
                return response([
                    'OUT_STAT' => "T",
                    'OUT_MESSAGE' => 'Berhasil tambah data transaksi',
                    'OUT_DATA' => $transaksi
                ]);
            }
            
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
        ->select('transaksis.*','customers.id_customer','customers.nama_customer',
                'users.id_karyawan','users.nama_karyawan')
        ->orderBy('transaksis.status_transaksi','ASC')
        ->orderBy('transaksis.created_at','ASC')
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
        ->select('transaksis.*','customers.id_royalty_point','customers.nama_customer','customers.email_customer','customers.telepon_customer','customers.password_customer','customers.tanggal_lahir_customer','customers.status_hapus')
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
    
    //Get Transaksi by ID Transaksi
    public function searchByIdTransaksi($id_transaksi){
        $transaksi = transaksi::where('id_transaksi','=',$id_transaksi)->first();

        if($transaksi == null){
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
        
        if($transaksi->status_transaksi === "Lunas"){
            $transaksi->status_transaksi = "Belum Lunas";
        }else{
            $transaksi->status_transaksi = "Lunas";
        }
        
        if($transaksi->total_harga >= 10000){
            
            $customer = customer::where('id_customer','=',$transaksi->id_customer)->first();
            if($customer == null){
                return response([
                    'OUT_STAT' => "F",
                    'OUT_MESSAGE' => 'Gagal ubah status transaksi',
                    'OUT_DATA' => null
                ]);
            }
            
            $jumlahPoint = 0;
                    
            $jumlahPoint = floor($transaksi->total_harga/10000);
            
            if($customer->id_royalty_point == null){
                $royalty_point = royalty_point::all();
                $urutan = $royalty_point->count()+1;
        
                $id = 'ROYPOI-'.mt_rand(100000,999999);
               
                $data = new royalty_point;
                $data->id_royalty_point = $id;
                $data->jumlah_point = $jumlahPoint;
        
                $customer2 = customer::where('id_customer','=', $transaksi->id_customer)->first();
                $customer2->id_royalty_point = $id;
                
                if(!$data->save() || !$customer2->save()){
                    return response([
                        'OUT_STAT' => "F",
                        'OUT_MESSAGE' => 'Gagal ubah status transaksi',
                        'OUT_DATA' => $data
                    ]);
                }
            }else{
                $point = royalty_point::where('id_royalty_point','=',$customer->id_royalty_point)->first();
                
                $point->jumlah_point = $point->jumlah_point + $jumlahPoint;
                
                if(!$point->save()){
                    return response([
                        'OUT_STAT' => "F",
                        'OUT_MESSAGE' => 'Gagal ubah status transaksi',
                        'OUT_DATA' => $data
                    ]);
                }
            }  
        }

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
        
        $id_kupon_customer = 0;
        if($transaksi->id_kupon_customer != null){
            $id_kupon_customer = $transaksi->id_kupon_customer;    
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
            
            
            
            if($id_kupon_customer != 0){
                $kuponCustomer = daftar_kupon_customer::where('id_kupon_customer','=',$id_kupon_customer)->first();
                
                $kuponCustomer->id_status_kupon = 1;
                $kuponCustomer->save();
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
    
    public function laporanHarianPendapatan($bulan,$tahun){

        $tanggal=[];
        $dataHarian=[];
        $total=0;
        $jumlahDiskon=0;
        $jumlahKotor = 0;

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

            $jumlah= DB::table('transaksis')
            ->selectRaw('ifnull(sum(transaksis.total_harga), 0) as totalPendapatan')
            ->where('transaksis.status_transaksi','=',"Lunas")
            ->whereDay('transaksis.created_at','=',$i)
            ->whereMonth('transaksis.created_at','=',$bulan)
            ->whereYear('transaksis.created_at','=',$tahun)
            ->first();
            
            $jumlahMakanan = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',1)
            ->whereDay('pesanans.created_at','=',$i)
            ->whereMonth('pesanans.created_at','=',$bulan)
            ->whereYear('pesanans.created_at','=',$tahun)
            ->first();
            
            $jumlahMinuman = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',2)
            ->whereDay('pesanans.created_at','=',$i)
            ->whereMonth('pesanans.created_at','=',$bulan)
            ->whereYear('pesanans.created_at','=',$tahun)
            ->first();
            
            $jumlahLain = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',3)
            ->whereDay('pesanans.created_at','=',$i)
            ->whereMonth('pesanans.created_at','=',$bulan)
            ->whereYear('pesanans.created_at','=',$tahun)
            ->first();
            
            $jumlahTerjual= $jumlah->totalPendapatan;

            array_push($tanggal,$dateString);
            array_push($dataHarian,$jumlahTerjual);

            $totalPerBaris = $jumlahMakanan->totalJumlah+$jumlahMinuman->totalJumlah+$jumlahLain->totalJumlah;
            $jumlahKotor = $jumlahKotor + $totalPerBaris;
            
            $total = $total + ($jumlahTerjual);
            $tableHarian[$i]= array(
                "nomor"=>$i,
                "tanggal"=>$dateString,
                "jumlah"=>$jumlahTerjual,
                "jumlahTotalBaris"=>$totalPerBaris,
                "jumlah_makanan"=>$jumlahMakanan->totalJumlah,
                "jumlah_minuman"=>$jumlahMinuman->totalJumlah,
                "jumlah_lain"=>$jumlahLain->totalJumlah,
            );
        }
        return response([
            'OUT_STAT'=> 'T',
            'OUT_MESSAGE'=> "Berhasil tampil data laporan",
            "OUT_DATA"=>$tableHarian,
            "OUT_JUMLAH"=>$dataHarian,
            "OUT_TANGGAL"=>$tanggal,
            'MONTH_NAME'=>$bulanString,
            'TOTAL'=>$total,
            'JUMLAH_KOTOR'=>$jumlahKotor,
            'DISKON'=>$jumlahKotor-$total,
        ]);
    }
    
    public function laporanBulananPendapatan($tahun){

        $data = [];
        $date=[];
        $total=0;
        $jumlahDiskon=0;
        $jumlahKotor = 0;

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

            $jumlah= DB::table('transaksis')
            ->selectRaw('ifnull(sum(transaksis.total_harga), 0) as totalPendapatan')
            ->where('transaksis.status_transaksi','=',"Lunas")
            ->whereMonth('transaksis.created_at','=',$i)
            ->whereYear('transaksis.created_at','=',$tahun)
            ->first();
            
            $jumlahMakanan = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',1)
            ->whereMonth('pesanans.created_at','=',$i)
            ->whereYear('pesanans.created_at','=',$tahun)
            ->first();
            
            $jumlahMinuman = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',2)
            ->whereMonth('pesanans.created_at','=',$i)
            ->whereYear('pesanans.created_at','=',$tahun)
            ->first();
            
            $jumlahLain = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',3)
            ->whereMonth('pesanans.created_at','=',$i)
            ->whereYear('pesanans.created_at','=',$tahun)
            ->first();
            
            array_push($data,$jumlah->totalPendapatan);
            array_push($date,$dateString);

            $totalPerBaris = $jumlahMakanan->totalJumlah+$jumlahMinuman->totalJumlah+$jumlahLain->totalJumlah;
            $jumlahKotor = $jumlahKotor + $totalPerBaris;
            
            $total = $total + $jumlah->totalPendapatan;
            $tableBulanan[$i]= array(
                "nomor"=>$i,
                "tanggal"=>$dateString,
                "jumlah"=>$jumlah->totalPendapatan,
                "jumlahTotalBaris"=>$totalPerBaris,
                "jumlah_makanan"=>$jumlahMakanan->totalJumlah,
                "jumlah_minuman"=>$jumlahMinuman->totalJumlah,
                "jumlah_lain"=>$jumlahLain->totalJumlah,
            );
        } 

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Berhasil tampil data laporan',
            'OUT_DATA' => $data,
            'OUT_TANGGAL'=> $date,
            'OUT_TABLE'=>$tableBulanan,
            'TOTAL'=>$total,
            'JUMLAH_KOTOR'=>$jumlahKotor,
            'DISKON'=>$jumlahKotor-$total,
        ]);
    }
    
    public function laporanTahunanPendapatan(){

        $data = [];
        $tahun = [];
        $total = 0;
        $jumlahDiskon=0;
        $jumlahKotor = 0;
        $nomor = 0;

        for($i=2020;$i<=date('Y');$i++){

            $yearString = 'Tahun '.$i;

            $jumlah = DB::table('transaksis')
            ->selectRaw('ifnull(sum(transaksis.total_harga), 0) as totalPendapatan')
            ->where('transaksis.status_transaksi','=',"Lunas")
            ->whereYear('transaksis.created_at','=',$i)
            ->first();
            
             $jumlahMakanan = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',1)
            ->whereYear('pesanans.created_at','=',$i)
            ->first();
            
            $jumlahMinuman = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',2)
            ->whereYear('pesanans.created_at','=',$i)
            ->first();
            
            $jumlahLain = DB::table('pesanans')
            ->join('menus','menus.id_menu','pesanans.id_menu')
            ->join('transaksis','pesanans.id_transaksi','transaksis.id_transaksi')
            ->selectRaw('ifnull(sum(pesanans.sub_total), 0) as totalJumlah')
            ->where('pesanans.status_selesai','=',1)
            ->where('transaksis.status_transaksi','=','Lunas')
            ->where('menus.id_jenis_menu','=',3)
            ->whereYear('pesanans.created_at','=',$i)
            ->first();

            array_push($data,$jumlah->totalPendapatan);
            array_push($tahun,$yearString);
            
            $nomor++;
            $total = $total + $jumlah->totalPendapatan;
            
            $totalPerBaris = $jumlahMakanan->totalJumlah+$jumlahMinuman->totalJumlah+$jumlahLain->totalJumlah;
            $jumlahKotor = $jumlahKotor + $totalPerBaris;
            
            $tableTahunan[$i]= array(
                "nomor"=>$nomor,
                "tanggal"=>$yearString,
                "jumlah"=>$jumlah->totalPendapatan,
                "jumlahTotalBaris"=>$totalPerBaris,
                "jumlah_makanan"=>$jumlahMakanan->totalJumlah,
                "jumlah_minuman"=>$jumlahMinuman->totalJumlah,
                "jumlah_lain"=>$jumlahLain->totalJumlah,
            );
        } 
        
        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Berhasil tampil data laporan',
            'OUT_DATA' => $data,
            'OUT_TANGGAL'=> $tahun,
            'OUT_TABLE'=>$tableTahunan,
            'TOTAL'=>$total,
            'JUMLAH_KOTOR'=>$jumlahKotor,
            'DISKON'=>$jumlahKotor-$total,
        ]);
    }
}
