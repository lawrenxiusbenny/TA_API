<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\menu;

class MenuController extends Controller
{
    //create data
    public function store(Request $request){
        $storeData = $request->all();
        
        if($request->hasfile('gambar_menu'))
        {
            $file = $request->file('gambar_menu');
            $ekstensi = $file->extension();
            $filename = 'IMG_'.time().'.'.$ekstensi;
            // $path = '/home/atmabbqx/public_html/be.atmabbq.xyz/menus/';
            $path = './Gambar_menu';
            $file->move($path,$filename);

            $menu = new menu;
            $menu->id_status_menu = $request->id_status_menu;
            $menu->id_jenis_menu = $request->id_jenis_menu;
            $menu->nama_menu = $request->nama_menu;
            $menu->harga_menu = $request->harga_menu;
            $menu->deskripsi_menu = $request->deskripsi_menu;
            $menu->gambar_menu = $filename;
            
            if($menu->save()){
                    return response([
                        'OUT_STAT' => "T",
                        'OUT_MESSAGE' => 'Berhasil tambah data menu',
                        'OUT_DATA' => $menu
                    ]);
            }else{
                    return response([
                        'OUT_STAT' => "F",
                        'OUT_MESSAGE' => 'Gagal tambah data menu',
                        'OUT_DATA' => null
                    ]);
            }   
        }
    }

    //get all data
    public function index(){
        $matchThese=["menus.status_hapus"=>0];
        $menu = DB::table('menus')
        ->join('status_menus','menus.id_status_menu','status_menus.id_status_menu')
        ->join('jenis_menus','menus.id_jenis_menu','jenis_menus.id_jenis_menu')
        ->where($matchThese)
        ->orderBy('jenis_menus.jenis_menu','ASC')
        ->orderBy('menus.nama_menu','ASC')
        ->get();

        if(count($menu) > 0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data menu',
                'OUT_DATA' => $menu
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data menu',
            'OUT_DATA' => null
        ]);
    }

    //get data random
    public function getRandom(){
        $matchThese=["menus.status_hapus"=>0,"menus.id_jenis_menu"=>1]; 
        $data = menu::inRandomOrder()->where($matchThese)->limit(3)->get();
        
        return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data menu',
                'OUT_DATA' => $data
            ]);
    }
    
    //get data by id menu
    public function searchMenu($id){
        $matchThese=["menus.status_hapus"=>0,"menus.id_menu"=>$id];
        $menu = DB::table('menus')
        ->join('status_menus','menus.id_status_menu','status_menus.id_status_menu')
        ->join('jenis_menus','menus.id_jenis_menu','jenis_menus.id_jenis_menu')
        ->where($matchThese)
        ->get();

        if(count($menu) > 0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data menu',
                'OUT_DATA' => $menu
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data menu',
            'OUT_DATA' => null
        ]);
    }

    //get data by jenis menu
    public function searchByJenis($id){
        $matchThese=["menus.status_hapus"=>0,"menus.id_jenis_menu"=>$id];
        $menu = DB::table('menus')
        ->join('status_menus','menus.id_status_menu','status_menus.id_status_menu')
        ->join('jenis_menus','menus.id_jenis_menu','jenis_menus.id_jenis_menu')
        ->where($matchThese)
        ->orderBy('menus.nama_menu','ASC')
        ->get();

        if(count($menu) > 0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data menu',
                'OUT_DATA' => $menu
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Tidak ada data menu',
            'OUT_DATA' => null
        ]);
    }

    //edit data menu
    public function update(Request $request, $id){
        $menu = menu::where('id_menu','=', $id)->first();
        if(is_null($menu)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data menu tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $updateData = $request->all();

        $menu->id_status_menu = $updateData['id_status_menu'];
        $menu->id_jenis_menu = $updateData['id_jenis_menu'];
        $menu->nama_menu = $updateData['nama_menu'];
        $menu->harga_menu = $updateData['harga_menu'];
        $menu->deskripsi_menu = $updateData['deskripsi_menu'];

        if($menu->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil ubah data menu',
                'OUT_DATA' => $menu
            ]);
        } 
        
        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal ubah data menu',
            'OUT_DATA' => null
        ]);
    }

    //upload image for edit data menu
    public function uploadImage(Request $request , $id){
        if($request->hasFile('gambar_menu')){
            $menu = menu::find($id);
            if(is_null($menu)){
                return response([
                    'OUT_STAT' => "F",
                    'OUT_MESSAGE' => 'Data menu tidak ditemukan',
                    'OUT_DATA' => null
                ]);
            }

            $updateData = $request->all();

            $file = $request->file('gambar_menu');
            $ekstensi = $file->extension();
            $filename = 'IMG_'.time().'.'.$ekstensi;
            // $path = '/home/atmabbqx/public_html/be.atmabbq.xyz/menus/';
            $path = './Gambar_menu';
            $file->move($path,$filename);

            $menu->gambar_menu = $filename;

            if($menu->save()){
                return response([
                    'OUT_STAT' => "T",
                    'OUT_MESSAGE' => 'Berhasil unggah foto menu',
                    'OUT_DATA' => $menu
                ]);
            }else{
                return response([
                    'OUT_STAT' => "F",
                    'OUT_MESSAGE' => 'Gagal unggah foto menu',
                    'OUT_DATA' => null
                ]);
            }
        }
    }

    //detele menu
    public function deleteSoft($id){
        $menu = menu::where('id_menu','=', $id)->first();

        if(is_null($menu)){
            return response([
                'OUT_STAT' => "F",
                'OUT_MESSAGE' => 'Data menu tidak ditemukan',
                'OUT_DATA' => null
            ]);
        }

        $menu->status_hapus = 1;
        if($menu->save()){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil hapus data menu',
                'OUT_DATA' => $menu
            ]);
        } 
        
        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal hapus data menu',
            'OUT_DATA' => null
        ]);
    }

    public function getAllName(){
        $data = DB::table('menus')
        ->select('id_menu','nama_menu')
        ->where('status_hapus','=','0')
        ->get();

        if(count($data)>0){
            return response([
                'OUT_STAT' => "T",
                'OUT_MESSAGE' => 'Berhasil tampil data menu',
                'OUT_DATA' => $data,
            ]);
        }

        return response([
            'OUT_STAT' => "F",
            'OUT_MESSAGE' => 'Gagal tampil data menu',
            'OUT_DATA' => null
        ]);
    }
}
