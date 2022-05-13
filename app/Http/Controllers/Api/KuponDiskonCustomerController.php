<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\daftar_kupon_customer;

class KuponDiskonCustomerController extends Controller
{
    //create kupon diskon customer
    public function store(Request $request){       

        $data = new daftar_kupon_customer;
        $data->id_customer = $request->id_customer;
        $data->id_kupon_diskon = $request->id_kupon_diskon;

        if($data->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tambah data kupon diskon (customer)',
                'OUT_DATA' => $data
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tambah data kupon diskon (customer)',
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
        $kupon = DB::table('daftar_kupon_customers')        
        ->join('daftar_kupon_diskons','daftar_kupon_customers.id_kupon_diskon','daftar_kupon_diskons.id_kupon_diskon')
        ->join('customers','daftar_kupon_customers.id_customer','customers.id_customer')
        ->join('status_kupons','daftar_kupon_customers.id_status_kupon','status_kupons.id_status_kupon')
        ->select('daftar_kupon_customers.created_at','customers.*','daftar_kupon_diskons.*','status_kupons.*')
        ->where('daftar_kupon_customers.id_customer','=', $id)
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
