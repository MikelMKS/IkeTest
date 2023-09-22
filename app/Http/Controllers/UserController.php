<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Consent;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function index(){
        $users = User::get()->map(function ($item) {
            $item->user = Crypt::decrypt($item->user);
            $item->name = Crypt::decrypt($item->name);
            $item->phone = Crypt::decrypt($item->phone);
            return $item;
        });

        return response()->json($users);
    }

    public function create(Request $request){
        $response = ['response' => null, 'message' => null, 'id_user' => null];

        try{
            $validated = $request->validate([
                'user' => 'required|max:30|unique_user',
                'name' => 'required|max:255',
                'phone' => 'required|numeric|digits:10',
                'password' => 'required|min:6|regex:/^[a-zA-Z0-9]*$/',
                'consent_id1' => 'required|true_if',
                'consent_id2' => 'required|boolean',
                'consent_id3' => 'required|boolean'
            ]);
        }catch(\Illuminate\Validation\ValidationException $e){
            // return $e->validator->errors();
            $response['response'] = false;
            $response['message'] = "Error validating data: ".$e->validator->errors();
        }

        if($response['response'] !== false) {
            $user = $request->user;
            $name = $request->name;
            $phone = $request->phone;
            $password = $request->password;
            $consentId2 = $request->consent_id2;
            $consentId3 = $request->consent_id3;

            try{
                $consentId2 = (new ConsentController)->create(new Request(['consentType' => 2,'accepted' => $request->consent_id2]));
                $consentId3 = (new ConsentController)->create(new Request(['consentType' => 3,'accepted' => $request->consent_id3]));

                $newUser = User::create([
                    'user' => Crypt::encrypt($user),
                    'name' => Crypt::encrypt($name),
                    'phone' => Crypt::encrypt($phone),
                    'password' => Crypt::encrypt($password),
                    'id_consent2' => $consentId2->getData()->consentId,
                    'id_consent3' => $consentId3->getData()->consentId,
                ]);

                $newCard = (new CardController)->create(new Request([
                                                                    'idUser' => $newUser->id,
                                                                    'cardId' => null,
                                                                    'captureCountry' => null,
                                                                    'policyVersion' => null,
                                                                    'presentedLanguage' => null,
                                                                ]));

                $response['response'] = true;
                $response['message'] = 'User created successfully';
                $response['id_user'] = $newUser->id;
            }catch(QueryException $e){
                $response['response'] = false;
                $response['message'] = 'Database error: '.$e->getMessage();
            }
        }
        return response()->json($response);
    }

    public function update(Request $request){
        $response = ['response' => null, 'message' => null];

        try{
            $validated = $request->validate([
                'id_user' => 'required|numeric',
                'user' => 'required|max:30',
                'name' => 'required|max:255',
                'phone' => 'required|numeric|digits:10',
                'password' => 'required|min:6|regex:/^[a-zA-Z0-9]*$/',
                'consent_id2' => 'required|boolean',
                'consent_id3' => 'required|boolean'
            ]);
        }catch(\Illuminate\Validation\ValidationException $e){
            $response['response'] = false;
            $response['message'] = "Error validating data: ".$e->validator->errors();
        }

        if($response['response'] !== false) {
            $id = $request->id_user;
            $user = $request->user;
            $name = $request->name;
            $phone = $request->phone;
            $password = $request->password;
            $consentId2 = $request->consent_id2;
            $consentId3 = $request->consent_id3;

            $userData = User::find($id);

            if(empty($userData)){
                $response['response'] = false;
                $response['message'] = "User not found";
            }else{
                $userVal = User::get()->map(function ($item) {
                    $item->user = Crypt::decrypt($item->user);
                    return $item;
                });

                $userVal = $userVal->filter(function ($item) use ($user,$id) {
                    return $item->user === $user && $item->id != $id;
                });

                if($userVal->isEmpty()){
                    $consentid2Actual = Consent::find($userData->id_consent2);
                    if(($consentid2Actual->account_level == null && $request->consent_id2 === true) || ($consentid2Actual->account_level != null && $request->consent_id2 === false)){
                        $consentId2 = (new ConsentController)->create(new Request(['consentType' => 2,'accepted' => $request->consent_id2]));
                        $newConsent2 = $consentId2->getData()->consentId;
                    }else{
                        $newConsent2 = $userData->id_consent2;
                    }
                    $consentid3Actual = Consent::find($userData->id_consent3);
                    if(($consentid3Actual->account_level == null && $request->consent_id3 === true) || ($consentid3Actual->account_level != null && $request->consent_id3 === false)){
                        $consentId3 = (new ConsentController)->create(new Request(['consentType' => 3,'accepted' => $request->consent_id3]));
                        $newConsent3 = $consentId3->getData()->consentId;
                    }else{
                        $newConsent3 = $userData->id_consent3;
                    }

                    if(($consentid2Actual->account_level == null && $request->consent_id2 === true) || ($consentid2Actual->account_level != null && $request->consent_id2 === false) || ($consentid3Actual->account_level == null && $request->consent_id3 === true) || ($consentid3Actual->account_level != null && $request->consent_id3 === false)){
                        (new HistoryController)->createFromConsent($id,$newConsent2,$newConsent3);
                    }

                    $updateUser = User::where('id',$id)->update([
                        'user' => Crypt::encrypt($user),
                        'name' => Crypt::encrypt($name),
                        'phone' => Crypt::encrypt($phone),
                        'password' => Crypt::encrypt($password),
                        'id_consent2' => $newConsent2,
                        'id_consent3' => $newConsent3,
                    ]);

                    $response['response'] = true;
                    $response['message'] = 'User updated successfully';
                }else{
                    $response['response'] = false;
                    $response['message'] = 'This username is already in use.';
                }
            }

        }

        return response()->json($response);
    }

    public function delete(Request $request){
        $response = ['response' => null, 'message' => null];

        try{
            $validated = $request->validate([
                'id_user' => 'required|numeric'
            ]);
        }catch(\Illuminate\Validation\ValidationException $e){
            $response['response'] = false;
            $response['message'] = "Error validating data: ".$e->validator->errors();
        }

        if($response['response'] !== false) {
            $id = $request->id_user;

            $userData = User::find($id);

            if(empty($userData)){
                $response['response'] = false;
                $response['message'] = "User not found";
            }else{
                $userData->delete();

                $response['response'] = true;
                $response['message'] = "User deleted successfully";
            }
        }

        return response()->json($response);
    }
}
