<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(){
        return "Login View";
    }

    public function getToken(Request $request){
        $response = ['status' => 0, 'message' => null,'token' => null,'date_finish' => null];


        try{
            $validated = $request->validate([
                'user' => 'required|max:30',
                'password' => 'required|min:6|regex:/^[a-zA-Z0-9]*$/',
            ]);

            $user = $request->user;
            $password = $request->password;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $response['status'] = 1;
            $response['message'] = "Error validating data: ".$e->validator->errors();
        }

        $findUser = User::get()->map(function ($item) {
            $item->user = Crypt::decrypt($item->user);
            $item->password = Crypt::decrypt($item->password);
            return $item;
        });
        $findUser = $findUser->filter(function ($item) use ($user,$password) {
            return $item->user == $user && $item->password == $password;
        });

        if($findUser->isEmpty()){
            $response['status'] = 1;
            $response['message'] = 'Invalid username or password';
        }else{
            $token = $findUser->first()->createToken('token_get')->plainTextToken;

            $response['token'] = $token;
            $response['date_finish'] = Date('Y-m-d H:i:s',strtotime(Date('Y-m-d H:i:s').'+ 5 minutes'));
        }

        return response()->json($response);
    }
}
