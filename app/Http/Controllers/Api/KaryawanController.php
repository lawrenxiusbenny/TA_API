<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;

class KaryawanController extends Controller
{
    //create data
    public function store(Request $request){
        $registrationData = $request->all();
        $registrationData['password'] = bcrypt($request->password); //enkripsi password

        $karyawan = User::create($registrationData);
        if($karyawan){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tambah data karyawan',
                'OUT_DATA' => $karyawan
            ]);    
        }
        
        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tambah data karyawan',
            'OUT_DATA' => null
        ]);
    }

    //Login
    public function login(Request $request){

        $email = $request->email_karyawan;
        $karyawan = User::where('email_karyawan','=',$email)->first();
        
        if(is_null($karyawan)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Email tidak terdaftar',
                'OUT_DATA' => null
            ]);
        }

        if($karyawan->id_status_karyawan === 2){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Gagal login, karyawan dalam status non-aktif',
                'OUT_DATA' => null
            ]);
        }

        $loginData = $request->all();
        if(!Auth::attempt($loginData))
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Email atau Password anda salah',
                'OUT_DATA' => null
            ]); 

        $karyawan = Auth::user();
        $token = $karyawan->createToken('Authentication Token')->accessToken; //generate token

        return response([
            'OUT_STAT' => "T",
            'OUT_MESSAGE' => 'Berhasil Login Ke dalam Sistem',
            'OUT_DATA' => $karyawan,
            'token_type'=>'Bearer',
            'access_token'=>$token,
        ]);
    }

    //get all data
    public function index(){
        $karyawan = DB::table('users')
        ->join('roles','users.id_role','roles.id_role')
        ->get();

        if(count($karyawan) > 0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data karyawan',
                'OUT_DATA' => $karyawan
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil data karyawan',
            'OUT_DATA' => null
        ]);
    }

    //get data by id
    public function search($id){

    //    $karyawan = DB::table('users')
    //     ->join('roles','users.id_role','roles.id_role')
    //     ->where('users.id_karyawan','=', $id)
    //     ->get();
        $karyawan = User::where('id_karyawan','=',$id)->first();

        if($karyawan != null){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data karyawan',
                'OUT_DATA' => $karyawan
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil data karyawan',
            'OUT_DATA' => null
        ]);
    }

    //get data by id status
    public function searchStatus($id){

        $karyawan = DB::table('users')
         ->join('roles','users.id_role','roles.id_role')
         ->join('status_karyawans','users.id_status_karyawan','status_karyawans.id_status_karyawan')
         ->where('users.id_status_karyawan','=', $id)
         ->get();
         // $karyawan = User::where('id_karyawan','=',$id)->first();
 
         if(count($karyawan) > 0){
             return response([
                 'OUT_STAT' => "T",
                 'OUT_MESSAGE' => 'Berhasil tampil data karyawan',
                 'OUT_DATA' => $karyawan
             ]);
         }
 
         return response([
             'OUT_STAT' => "F",
             'OUT_MESSAGE' => 'Gagal tampil data karyawan',
             'OUT_DATA' => null
         ]);
    }

    //update data
    public function update(Request $request, $id){
        $Karyawan = User::where('id_karyawan','=', $id)->first();

        if(is_null($Karyawan)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data karyawan tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $updateData = $request->all();
        $Karyawan->id_role = $updateData['id_role'];
        $Karyawan->nama_karyawan = $updateData['nama_karyawan'];
        $Karyawan->jenis_kelamin_karyawan = $updateData['jenis_kelamin_karyawan'];
        $Karyawan->telepon_karyawan = $updateData['telepon_karyawan'];
        $Karyawan->tanggal_bergabung = $updateData['tanggal_bergabung'];

        if($Karyawan->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil update data karyawan',
                'OUT_DATA' => $Karyawan
            ]);
        } 
        
        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal update data karyawan',
            'OUT_DATA' => null
        ]);
    }

    //change-status
    public function changeStatus($id){
        $karyawan = User::where('id_karyawan','=', $id)->first();
        if(is_null($karyawan)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data karyawan tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        if($karyawan->id_status_karyawan == 1){
            $karyawan->id_status_karyawan = 2;
        }
        else if($karyawan->id_status_karyawan == 2){
            $karyawan->id_status_karyawan = 1;
        }
        
        if($karyawan->save()){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Status karyawan berhasil diubah',
                'OUT_DATA' => $karyawan
            ]);
        } 
        
        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Status karyawan gagal diubah',
            'OUT_DATA' => null
        ]);
    }
}
