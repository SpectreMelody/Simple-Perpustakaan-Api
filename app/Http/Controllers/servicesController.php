<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\book;
use App\Models\data_service;

class servicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPeminjaman()
    {
        //get all data peminjam
        $getPinjam = DB::table('data_services')
        ->where('status', "Meminjam")
        ->orWhere('status', "Masih Ada Pinjaman")
        ->get();
        
        $response = [
            $message = 'Succesfully Get All Data with status Meminjam or Masih Ada Pinjaman',
            $data = $getPinjam
        ];
        
        return response()->json($response, Response::HTTP_OK);
    }

    public function indexSudahMengembalikan()
    {
        //get all data peminjam
        $getPinjam = DB::table('data_services')->where('status', "Tidak Ada Pinjaman")->get();
        
        $response = [
            $message = 'Succesfully Get All Data with status Tidak Ada Pinjaman',
            $data = $getPinjam
        ];
        
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     *  
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // json format , action only allowed "Pinjam,Kembali" 
    // {
    //     "nama" : "",
    //     "alamat" : "",
    //     "telepon" : "",
    //     "kd_buku" : "",
    //     "judul_buku" : "",
    //     "jumlah" : "",
    //     "action" : ""
    // }
    public function pinjamBuku(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'alamat' => 'required',
            'telepon' => 'required|min:12|max:12',
            'kd_buku' => 'required',
            'judul_buku' => 'required',
            'jumlah' => 'required|numeric',
            'action' => 'required|in:Pinjam,Kembali',
        ]);
        
        if ($validator->fails()){
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $kode = DB::table('books')->where('kode_buku', $request->kd_buku)->get();
        $jdl_buku = DB::table('books')->where('buku', $request->judul_buku)->get();

        if(count($kode) != 1 && count($jdl_buku) != 1){
            return response()->json(['message' => 'kode_buku or buku does not match'], Response::HTTP_NOT_FOUND);
        }
        
        $nama = DB::table('data_services')->where('nama', $request->nama)->value('nama');
        $kd_buku = DB::table('data_services')->where('kd_buku', $request->kd_buku)->value('kd_buku');
    
        if($request->action == "Pinjam" && $nama == $request->nama && $kd_buku == $request->kd_buku){
            try {
                $services = DB::table('data_services')
                    ->where('nama', $request->nama)
                    ->where('kd_buku', $request->kd_buku)
                    ->where('judul_buku', $request->judul_buku)
                    ->update([
                            'nama' => $request->nama,
                            'alamat' => $request->alamat,
                            'telepon' => $request->telepon,
                            'kd_buku' => $request->kd_buku,
                            'judul_buku' => $request->judul_buku,
                            'jumlah' => $request->jumlah,
                            'action' => $request->action,
                            'status' => "Meminjam"
                    ]);
                $response = [
                    $message = 'Succesfuly Make Another Pinjaman',
                    $data = $services,
                ];
                
                return response()->json($response, Response::HTTP_OK);
            } catch (QueryException $info) {
                return response()->json([
                    'message' => 'Failed' . $info->errorInfo
                ]);
            }
        }
        //pengembalian condition
        if($request->action == "Kembali"){
            $getPinjam = DB::table('data_services')->where('action', "Meminjam")->where('nama', $request->nama)->get();
            if(count($kode) != 1 && count($jdl_buku) != 1 && count($getPinjam) != 1){
                return response()->json(['message' => 'kode_buku or buku does not match'], Response::HTTP_NOT_FOUND);
            }
            
            $peminjaman = DB::table('data_services')
                ->where('nama', $request->nama)
                ->where('kd_buku', $request->kd_buku)
                ->where('judul_buku', $request->judul_buku)
                ->where(function($query) {
                    $query
                    ->where('status',"Meminjam")
                    ->orWhere('status',"Masih Ada Pinjaman");
                })
                ->value('jumlah');

                // return response()->json($peminjaman,200);

            if($peminjaman != $request->jumlah && $peminjaman >= $request->jumlah){
                $jumlahPeminjaman = $peminjaman - $request->jumlah;
                try {
                    $services = DB::table('data_services')
                        ->where('nama', $request->nama)
                        ->where('kd_buku', $request->kd_buku)
                        ->where('judul_buku', $request->judul_buku)
                        ->update([
                                'nama' => $request->nama,
                                'alamat' => $request->alamat,
                                'telepon' => $request->telepon,
                                'kd_buku' => $request->kd_buku,
                                'judul_buku' => $request->judul_buku,
                                'jumlah' => $jumlahPeminjaman,
                                'action' => $request->action,
                                'status' => "Masih Ada Pinjaman"
                        ]);
                    $response = [
                        $message = 'Succesfuly Make Pengembalian but still have Pinjaman',
                        $data = $services,
                    ];
                    
                    return response()->json($response, Response::HTTP_OK);
                } catch (QueryException $info) {
                    return response()->json([
                        'message' => 'Failed' . $info->errorInfo
                    ]);
                }
            }
            
            try {
                $services = DB::table('data_services')
                        ->where('nama', $request->nama)
                        ->where('kd_buku', $request->kd_buku)
                        ->where('judul_buku', $request->judul_buku)
                        ->update([
                            'nama' => $request->nama,
                            'alamat' => $request->alamat,
                            'telepon' => $request->telepon,
                            'kd_buku' => $request->kd_buku,
                            'judul_buku' => $request->judul_buku,
                            'jumlah' => $request->jumlah,
                            'action' => $request->action,
                            'status' => "Tidak Ada Pinjaman",
                        ]);
                $response = [
                    $message = 'Succesfuly Make Pengembalian and no more Pinjaman',
                    $data = $services,
                ];
                
                return response()->json($response, Response::HTTP_OK);
            } catch (QueryException $info) {
                return response()->json([
                    'message' => 'Failed' . $info->errorInfo
                ]);
            }
        }

        $services = data_service::create($request->all());
        $response = [
            $message = 'Succesfuly Make Pinjaman',
            $data = $services
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showPeminjamanByNama($nama)
    {
        $getPinjam = DB::table('data_services')
        ->where('nama', $nama)
        ->where(function($query) {
            $query
            ->where('status',"Meminjam")
            ->orWhere('status',"Masih Ada Pinjaman");
        })
        ->get();
        
        $response = [
            $message = 'Succesfully Get Specific Data by nama',
            $data = $getPinjam
        ];
        
        return response()->json($response, Response::HTTP_OK);
    }

    public function showPengembalianByNama($nama)
    {
        $getLunas = DB::table('data_services')->where('status', "Tidak Ada Pinjaman")->where('nama', $nama)->get();
        
        $response = [
            $message = 'Succesfully Get Specific Data by nama',
            $data = $getLunas
        ];
        
        return response()->json($response, Response::HTTP_OK);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        // $search=$request->kd_buku;
        // $kode = DB::table('books')->where('kode_buku', $request->kd_buku)->get();
        $kode = DB::table('books')->pluck('jumlah_buku');
        $i = 0;
        foreach ($kode as $data) {
            $i = $i + $data;
        }
        // dd($kode);
        // return response()->json($kode, 200);
        // if($kode == $request->nominal){
        //     return response()->json($kode, 200);
        // }
        return response()->json($i, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
