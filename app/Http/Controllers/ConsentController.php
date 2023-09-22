<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consent;
use Illuminate\Support\Facades\Crypt;
use Str;

class ConsentController extends Controller
{

    public function index()
    {
        // Get all data from Consents
        $consents = Consent::get()->map(function ($item) {
            // Decrypt account_level
            $item->account_level = !empty($item->account_level) ? Crypt::decrypt($item->account_level) : 'NA';
            return $item;
        });

        return response()->json($consents);
    }

    public function create(Request $request)
    {
        $response = ['status' => 0, 'message' => null, 'consentId' => null];
        $types = ['1','2','3'];

        $consentType = $request->consentType;
        $accepted = $request->accepted;

        if(empty($consentType) || !in_array($consentType,$types)){
            $response['status'] = 1;
            $response['message'] = 'Consent type not found or not valid';
        }

        if ($accepted === null || $accepted === ''){
            $response['status'] = 1;
            $response['message'] = 'A true or false value is required';
        }

        if($response['status'] == 0){

            if($accepted === true){
                $accountLevel = Str::random(30);
                $encryptedAccountLevel = Crypt::encrypt($accountLevel);
            }else{
                $encryptedAccountLevel = null;
            }

            $newConsent = Consent::create([
                'consent_type' => $consentType,
                'account_level' => $encryptedAccountLevel,
            ]);

            if($newConsent){
                $response['message'] = 'Consent created successfully';
                $response['consentId'] = $newConsent->id;
            }else{
                $response['status'] = 1;
                $response['message'] = 'Database error creating';
            }
        }

        return response()->json($response);
    }
}
