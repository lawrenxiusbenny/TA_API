<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login',"Api\KaryawanController@login"); // login-karyawan
Route::post('/login-customer',"Api\CustomerController@login"); // login-customer

Route::group(['middleware' => 'auth:api'], function(){
    Route::get('role', 'Api\RoleController@getAllName'); //get all role
    Route::get('role-name/{name}', 'Api\RoleController@getIdByName'); //get id role by role name
    Route::get('role-id/{id}', 'Api\RoleController@getNameById'); //get role name by id role

    Route::get('status-menu', 'Api\StatusMenuController@getAllName'); //get all data
    Route::get('status-menu/{name}', 'Api\StatusMenuController@getIdByName'); //get id  by name
    Route::get('status-menu-id/{id}', 'Api\StatusMenuController@getNameById'); //get name by id

    Route::get('jenis-menu', 'Api\JenisMenuController@getAllName'); //get all data
    Route::get('jenis-menu/{name}', 'Api\JenisMenuController@getIdByName'); //get id  by name
    Route::get('jenis-menu-id/{id}', 'Api\JenisMenuController@getNameById'); //get name by id

    Route::post('karyawan', 'Api\KaryawanController@store'); // insert new karyawan    
    Route::get('karyawan', 'Api\KaryawanController@index'); // get all karyawan
    Route::get('karyawan/{id}', 'Api\KaryawanController@search'); //search karyawan by id
    Route::get('karyawan-status/{id}', 'Api\KaryawanController@searchStatus'); //search karyawan by status
    Route::put('karyawan/{id}', 'Api\KaryawanController@update'); // update data karyawan
    Route::put('karyawan-status/{id}', 'Api\KaryawanController@changeStatus'); // change status karyawan
    
    Route::post('customer', 'Api\CustomerController@store'); //insert new customer
    Route::get('customer', 'Api\CustomerController@index'); //get all customer
    Route::get('customer/{id}', 'Api\CustomerController@search'); //search customer by id
    Route::put('customer/{id}', 'Api\CustomerController@update'); //update data customer
    Route::put('customer-soft/{id}', 'Api\CustomerController@softDelete'); //soft-delete customer
    Route::get('customer-name', 'Api\CustomerController@getAllName'); //get all data customer name
    Route::get('customer-name-2', 'Api\CustomerController@getAllNameWithNullRoyaltyPoint'); //get all data customer name

    Route::post('pesanan', 'Api\PesananController@store'); //insert data pesanan
    Route::get('pesanan', 'Api\PesananController@index'); // get all pesanan
    Route::get('pesanan/{id_customer}', 'Api\PesananController@searchPesanan'); // get all pesanan BY ID CUSTOMER and STATUS_SELESAI = 0
    Route::put('pesanan/{id}', 'Api\PesananController@update'); //update data pesanan (jumlah)
    Route::delete('pesanan/{id}', 'Api\PesananController@delete'); //delete data pesanan (jumlah)
    Route::put('pesanan-penyajian/{id}', 'Api\PesananController@editPenyajian'); // edit penyajian pesanan
    Route::put('pesanan-checkout/{id_customer}', 'Api\PesananController@checkOut'); //checkout mobile
    Route::get('pesanan-struk/{id_transaksi}', 'Api\PesananController@searchPesananByTransaksi'); // get all pesanan BY ID transaksi
    

    Route::get('menu', 'Api\MenuController@index'); // get all menu 
    Route::get('menu/{id}', 'Api\MenuController@searchMenu'); // get menu by id menu
    Route::get('menu-jenis/{id}', 'Api\MenuController@searchByJenis'); // get menu by jenis menu
    Route::post('menu', 'Api\MenuController@store'); //insert data menu
    Route::put('menu/{id}', 'Api\MenuController@update'); //update data menu
    Route::put('menu-delete/{id}', 'Api\MenuController@deleteSoft'); //Delete soft data menu
    Route::post('menu/upload-image/{id}', 'Api\MenuController@uploadImage'); // upload image
    Route::get('menu-name', 'Api\MenuController@getAllName'); //get all data menu name
    
    Route::post('transaksi', 'Api\TransaksiController@store'); //insert data transaksi
    Route::get('transaksi', 'Api\TransaksiController@index'); //get all data transaksi
    Route::get('transaksi/{id_customer}', 'Api\TransaksiController@searchByIdCustomer'); //get all data transaksi by id customer
    Route::put('transaksi/{id_transaksi}', 'Api\TransaksiController@update'); //Update data transaksi
    Route::put('transaksi-status/{id_transaksi}', 'Api\TransaksiController@updateStatusPembayaran'); //Update status transaksi
    Route::delete('transaksi/{id_transaksi}', 'Api\TransaksiController@deleteTransaction'); //Delete data transaksi
    

    Route::post('point', 'Api\RoyaltyPointController@store'); //insert data royalty point
    Route::get('point', 'Api\RoyaltyPointController@index'); //get all data royalty point
    Route::get('point/{id}', 'Api\RoyaltyPointController@search'); //get data royalty point by id
    Route::get('point-struk/{id}', 'Api\RoyaltyPointController@searchStruk'); //get data royalty point by id
    Route::put('point/{id}', 'Api\RoyaltyPointController@update'); //update data royalty point
    Route::delete('point/{id}', 'Api\RoyaltyPointController@deletePoint'); //Delete data transaksi

    Route::post('kupon', 'Api\KuponDiskonController@store'); //insert data kupon diskon
    Route::get('kupon', 'Api\KuponDiskonController@index'); //get all data kupon diskon
    Route::get('kupon/{id}', 'Api\KuponDiskonController@search'); //get data kupon diskon by id
    Route::put('kupon/{id}', 'Api\KuponDiskonController@update'); //update data kupon diskon
    Route::put('kupon-delete/{id}', 'Api\KuponDiskonController@deleteKupon'); //soft delete data kupon diskon

    Route::post('kupon-customer', 'Api\KuponDiskonCustomerController@store'); //insert data kupon diskon
    Route::get('kupon-customer', 'Api\KuponDiskonCustomerController@index'); //get all data kupon diskon
    Route::get('kupon-by-id/{id}', 'Api\KuponDiskonCustomerController@search'); //get data kupon diskon by id
    Route::get('kupon-struk/{id}', 'Api\KuponDiskonCustomerController@searchStruk'); //get data kupon diskon by id
    Route::get('kupon-by-id-customer/{id}', 'Api\KuponDiskonCustomerController@searchByIdCustomer'); //get data kupon diskon by id customer
    Route::delete('kupon-customer-delete/{id}', 'Api\KuponDiskonCustomerController@deleteKupon'); //soft delete data kupon diskon

    Route::get('laporan-harian/{id_menu}/{bulan}/{tahun}', 'Api\PesananController@laporanHarian'); // get Laporan Harian
    Route::get('laporan-bulanan/{id_menu}/{tahun}', 'Api\PesananController@laporanBulanan'); // get Laporan Bulanan
    Route::get('laporan-tahunan/{id_menu}', 'Api\PesananController@laporanTahunan'); // get Laporan Tahunan
    
});