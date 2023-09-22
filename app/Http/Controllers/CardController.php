<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Crypt;
use Str;
use Faker\Factory as Faker;

class CardController extends Controller
{
    public function index()
    {
        // Get all data from Cards
        $cards = Card::get()->map(function ($item) {
            // Decrypt data
            $item->card_id = Crypt::decrypt($item->card_id);
            $item->capture_country = Crypt::decrypt($item->capture_country);
            $item->policy_version = Crypt::decrypt($item->policy_version);
            $item->language = Crypt::decrypt($item->language);
            return $item;
        });

        return response()->json($cards);
    }

    public function create(Request $request)
    {
        $response = ['status' => 0, 'message' => null, 'cardId' => null];

        $faker = Faker::create();

        $idUser = $request->idUser;
        $cardId = $faker->numerify('################');
        $captureCountry = $faker->randomElement(['United States','Mexico','Canada']);
        $policyVersion = '1.0';
        $presentedLanguage = $faker->randomElement(['EN/US','ES/MX']);

        if(multipleEmpty($idUser,$cardId,$captureCountry,$policyVersion,$presentedLanguage)){
            $response['status'] = 1;
            $response['message'] = 'Data not found or not valid';
        }

        if($response['status'] == 0){
            try {
                $newCard = Card::create([
                    'card_id' => Crypt::encrypt($cardId),
                    'capture_country' => Crypt::encrypt($captureCountry),
                    'policy_version' => Crypt::encrypt($policyVersion),
                    'language' => Crypt::encrypt($presentedLanguage),
                ]);

                $response['message'] = 'Card created successfully';
            } catch (QueryException $e) {
                $response['status'] = 1;
                $response['message'] = 'Database error '.$e->getMessage();
            }

            if($response['status'] == 0){
                $consentId1 = (new ConsentController)->create(new Request(['consentType' => 1,'accepted' => true]));
                (new HistoryController)->createFromCard($idUser,$newCard->id,$consentId1->getData()->consentId);
            }
        }

        return response()->json($response);
    }
}
