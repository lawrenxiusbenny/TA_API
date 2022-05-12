<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\customer;
use Validator;

class CustomerController extends Controller
{
    //create data
    public function store(Request $request){
        // $customer = new customer;
        $registrationData = $request->all();
        $registrationData['password_customer'] = Hash::make($request->password_customer); //enkripsi password
        // $customer->nama_customer = $request->nama_customer;
        // $customer->email_customer = $request->email_customer;
        // $customer->telepon_customer = $request->telepon_customer;
        // $customer->password_customer = bcrypt($request->password_customer); //enkripsi password
        // $customer->tanggal_lahir_customer = $request->tanggal_lahir_customer;

        $customer = customer::create($registrationData);
        if($customer){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tambah data customer',
                'OUT_DATA' => $customer
            ]);
       }else{
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal tambah data customer',
                'OUT_DATA' => null
            ]);
       }
    }

    //getAllData
    public function index(){
        $customer = Customer::where('status_hapus','=', '0')->get();

        if(count($customer) > 0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data customer',
                'OUT_DATA' => $customer
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil data customer',
            'OUT_DATA' => null
        ]);
    }

    //get data by id
    public function search($id){
        $matchThese = ['id_customer' => $id];
        $customer = Customer::where($matchThese)->get();

        if(count($customer)>0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data customer',
                'OUT_DATA' => $customer
            ]);
        }

        return response([
            'OUT_STAT' => 'F',
            'OUT_MESSAGE' => 'Gagal tampil data customer',
            'OUT_DATA' => null
        ]);
    }

    //update data
    public function update(Request $request, $id){
        $customer = Customer::where('id_customer','=', $id)->first();
        if(is_null($customer)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data customer tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $updateData = $request->all();
        $customer->nama_customer = $updateData['nama_customer'];
        $customer->telepon_customer = $updateData['telepon_customer'];
        $customer->tanggal_lahir_customer = $updateData['tanggal_lahir_customer'];

        if($customer->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil update data customer',
                'OUT_DATA' => $customer
            ]);
        } 
        
        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal update data customer',
            'OUT_DATA' => null
        ]);
    }

    //Login
    public function login(Request $request){
        $email = $request->email_customer;
        $customer = customer::where('email_customer','=',$email)->first();
        
        if(is_null($customer)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Email tidak terdaftar',
                'OUT_DATA' => null
            ]);
        }

        if($customer->status_hapus == 1){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal login, data customer telah dihapus',
                'OUT_DATA' => null
            ]);
        }

        $checkPassword = Hash::check($request->password_customer, $customer->password_customer);
        if($checkPassword){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil Login Ke dalam Sistem',
                'OUT_DATA' => $customer,
                
            ]); 
        }else{
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Password anda salah',
                'OUT_DATA' => null
            ]);
        }
    }

    //soft delete data
    public function softDelete($id){
        $customer = customer::where('id_customer','=', $id)->first();
        if(is_null($customer)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data customer tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $customer->status_hapus = 1;
        if($customer->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Data customer berhasil dihapus',
                'OUT_DATA' => $customer
            ]);
        }else{
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data customer gagal dihapus',
                'OUT_DATA' => $customer
            ]);
        }
    }

    public function getAllName(){
        // $data = customer::all();
        $data = DB::table('customers')
        ->select('id_customer','nama_customer')
        ->where('status_hapus','=',0)
        ->get();

        if(count($data)>0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data customer',
                'OUT_DATA' => $data,
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil data customer',
            'OUT_DATA' => null
        ]);
    }

    public function getAllNameWithNullRoyaltyPoint(){
        // $data = customer::all();
        $matchThese = ['id_royalty_point' => null,'status_hapus'=>0];

        $data = DB::table('customers')
        ->select('id_customer','nama_customer')
        ->where($matchThese)
        ->get();

        if(count($data)>0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data customer',
                'OUT_DATA' => $data,
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil data customer',
            'OUT_DATA' => null
        ]);
    }
}
