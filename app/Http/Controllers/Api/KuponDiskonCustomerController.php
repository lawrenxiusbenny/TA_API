<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\daftar_kupon_customer;
use App\customer;
use App\royalty_point;

class KuponDiskonCustomerController extends Controller
{
    //create kupon diskon customer
    public function store(Request $request){       

        $data = new daftar_kupon_customer;
        $data->id_customer = $request->id_customer;
        $data->id_kupon_diskon = $request->id_kupon_diskon;
        
        $jumlah_point_tukar = $request->jumlah_point_tukar;
        
        $matchThese = ['id_customer' => $request->id_customer];
        
        $customer = customer::where($matchThese)->first();
        
        if($customer == null){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal klaim kupon, data customer tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }
        
        $point = royalty_point::where('id_royalty_point','=',$customer->id_royalty_point)->first();
        if($point == null){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal klaim kupon, data royalty point tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }
        
        $point->jumlah_point = $point->jumlah_point - $jumlah_point_tukar;

        if($data->save() && $point->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil klaim kupon',
                'OUT_DATA' => $data
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal klaim kupon',
            'OUT_DATA' => null
        ]);
    }

    //Get All data
    public function index(){
        $kupons = DB::table('daftar_kupon_customers')        
        ->join('daftar_kupon_diskons','daftar_kupon_customers.id_kupon_diskon','daftar_kupon_diskons.id_kupon_diskon')
        ->join('customers','daftar_kupon_customers.id_customer','customers.id_customer')
        ->join('status_kupons','daftar_kupon_customers.id_status_kupon','status_kupons.id_status_kupon')
        ->select('daftar_kupon_customers.*','customers.id_customer','customers.nama_customer',
                'daftar_kupon_diskons.id_kupon_diskon','daftar_kupon_diskons.nama_kupon','status_kupons.*')
        ->get();

        if(count($kupons) != 0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data kupon diskon (customer)',
                'OUT_DATA' => $kupons
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data kupon diskon (customer)',
            'OUT_DATA' => 'test '
        ]);
    }

    //get data by id kupon customer
    public function search($id){
        $kupon = DB::table('daftar_kupon_customers')        
        ->join('daftar_kupon_diskons','daftar_kupon_customers.id_kupon_diskon','daftar_kupon_diskons.id_kupon_diskon')
        ->join('customers','daftar_kupon_customers.id_customer','customers.id_customer')
        ->join('status_kupons','daftar_kupon_customers.id_status_kupon','status_kupons.id_status_kupon')
        ->select('daftar_kupon_customers.created_at','customers.*','daftar_kupon_diskons.*','status_kupons.*')
        ->where('id_kupon_customer','=', $id)
        ->first();
        
        if($kupon != null){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data kupon (customer)',
                'OUT_DATA' => $kupon
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data kupon diskon (customer)',
            'OUT_DATA' => null
        ]);
    }

    public function searchStruk($id){
        $kupon = DB::table('daftar_kupon_customers')        
        ->join('daftar_kupon_diskons','daftar_kupon_customers.id_kupon_diskon','daftar_kupon_diskons.id_kupon_diskon')
        ->select('daftar_kupon_diskons.persentase_potongan')
        ->where('daftar_kupon_customers.id_kupon_customer','=', $id)
        ->first();
        
        if($kupon != null){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data kupon (customer)',
                'OUT_DATA' => $kupon
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data kupon diskon (customer)',
            'OUT_DATA' => null
        ]);
    }
    //get data by id kupon customer
    public function searchByIdCustomer($id){
        $matchThese=["daftar_kupon_customers.id_customer"=>$id,"daftar_kupon_customers.id_status_kupon"=>1];
        $kupon = DB::table('daftar_kupon_customers')        
        ->join('daftar_kupon_diskons','daftar_kupon_customers.id_kupon_diskon','daftar_kupon_diskons.id_kupon_diskon')
        ->join('status_kupons','daftar_kupon_customers.id_status_kupon','status_kupons.id_status_kupon')
        ->select('daftar_kupon_customers.created_at','daftar_kupon_customers.id_kupon_customer','daftar_kupon_diskons.*','status_kupons.*')
        ->where($matchThese)
        ->get();
        
        if(count($kupon) != 0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data kupon (customer)',
                'OUT_DATA' => $kupon
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data kupon diskon (customer)',
            'OUT_DATA' => null
        ]);
    }

    //DELETE DATA
    public function deleteKupon($id){
        $matchThese = ['id_kupon_customer' => $id];
        $kupon = daftar_kupon_customer::where($matchThese)->first();

        if($kupon == null){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Tidak ada data kupon diskon (customer)',
                'OUT_DATA' => null
            ]);
        }

        if($kupon->delete()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil hapus data kupon diskon (customer)',
                'OUT_DATA' => $kupon
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal hapus data kupon diskon (customer)',
            'OUT_DATA' => null
        ]);
        
    }
}
