<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Models\User;

class userController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $get_perpustakaan = User::all();
        $response = [
            'message' => 'Get all data from Perpustakaan',
            'data' => $get_perpustakaan
        ];

        return response()->json($response, Response::HTTP_OK);
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
            'perpustakaan' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:16',

        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $storePerpustakaan = User::create([
                'perpustakaan' => $request->perpustakaan,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
    
            $token = $storePerpustakaan->createToken('myAppToken');
    
            return (new UserResource($storePerpustakaan))->additional([
                'token' => $token->plainTextToken,
            ]);
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
        $showPerpustakaan = User::findOrFail($id);
        
        $response = [
            $message = 'Succesfull get specific data from Perpustakaan',
            $data = $showPerpustakaan,
        ];
        
        return response()->json($response, Response::HTTP_OK);

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
        $updatePerpustakaan = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'perpustakaan' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:16',

        ]);


        if ($validator->fails()){
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $updatePerpustakaan->update($request->all());
            $response = [
                $message = 'Succesfull updating Book',
                $data = $updatePerpustakaan,
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
        $destroyPerpustakaan = User::findOrFail($id);

        try {
            $destroyPerpustakaan->delete();
            $response = [
                $message = 'Succesfull deleting Book',
                $data = $destroyPerpustakaan,
            ];
            
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $info) {
            return response()->json([
                'message' => 'Failed' . $info->errorInfo
            ]);
        }
    }

}
