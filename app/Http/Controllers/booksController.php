<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\book;

class booksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $get_books = book::all();
        $response = [
            'message' => 'Get all data from books',
            'data' => $get_books
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_buku' => 'required|min:8|max:8|unique:books,kode_buku',
            'buku' => 'required',
            'jumlah_buku' => 'required|numeric',

        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $storeBook = book::create(
            //     [
            //     'kode_buku' => $request['kode_buku'],
            //     'buku' => $request['buku'],
            //     'jumlah_buku' => $request['jumlah_buku'],
            // ]
            $request->all()
        );
            $response = [
                $message = 'Succesfully Storing Book',
                $data = $storeBook,
            ];
            
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $info) {
            return response()->json([
                'message' => 'Failed' . $info->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $showBook = book::findOrFail($id);
        
        $response = [
            $message = 'Succesfully Get Specific Data From Book',
            $data = $showBook,
        ];
        
        return response()->json($response, Response::HTTP_OK);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $updateBook = book::findOrFail($id);
        // $updateBook = DB::table('books')
        // ->where('buku', $request->buku)
        // ->update($request->all);

        $validator = Validator::make($request->all(), [
            'kode_buku' => 'required|min:8|max:8|unique:books,kode_buku',
            'buku' => 'required',
            'jumlah_buku' => 'required',

        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $updateBook->update($request->all());
            $response = [
                $message = 'Succesfully Updating Book',
                $data = $updateBook,
            ];
            
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $info) {
            return response()->json([
                'message' => 'Failed' . $info->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $destroyBook = book::findOrFail($id);

        try {
            $destroyBook->delete();
            $response = [
                $message = 'Succesfull Deleting Book',
                $data = $destroyBook,
            ];
            
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $info) {
            return response()->json([
                'message' => 'Failed' . $info->errorInfo
            ]);
        }
    }
}
