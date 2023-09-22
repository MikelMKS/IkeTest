<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\History;
use Illuminate\Support\Facades\Crypt;

class HistoryController extends Controller
{
    public function index(){
        $history = History::join('users', 'users.id', '=', 'history.id_user')
        ->leftJoin('cards', 'history.id_card', '=', 'cards.id')
        ->leftJoin('consents AS consents1', 'history.id_consent1', '=', 'consents1.id')
        ->leftJoin('consents AS consents2', 'history.id_consent2', '=', 'consents2.id')
        ->leftJoin('consents AS consents3', 'history.id_consent3', '=', 'consents3.id')
        ->select(
            'history.id_user AS EUID','users.user AS User'
            ,'cards.card_id AS CardID'
            ,'consents1.account_level AS CardLevel'
            ,'cards.created_at AS CardTimestamp'
            ,'cards.capture_country AS CardCountry'
            ,'cards.policy_version AS PolicyVersion'
            ,'cards.language AS Language'
            ,'consents2.account_level AS AccountLevelConsentID2'
            ,'consents2.created_at AS TimeStampConsentID2'
            ,'consents3.account_level AS AccountLevelConsentID3'
            ,'consents3.created_at AS TimeStampConsentID3'
        )
        ->get()
        ->map(function ($item) {
            $item->User = !empty($item->User) ? Crypt::decrypt($item->User) : 'NA';
            $item->CardID = !empty($item->CardID) ? Crypt::decrypt($item->CardID) : 'NA';
            $item->CardLevel = !empty($item->CardLevel) ? Crypt::decrypt($item->CardLevel) : 'NA';
            $item->CardTimestamp = !empty($item->CardTimestamp) ? $item->CardTimestamp : 'NA';
            $item->CardCountry = !empty($item->CardCountry) ? Crypt::decrypt($item->CardCountry) : 'NA';
            $item->PolicyVersion = !empty($item->PolicyVersion) ? Crypt::decrypt($item->PolicyVersion) : 'NA';
            $item->Language = !empty($item->Language) ? Crypt::decrypt($item->Language) : 'NA';
            $item->AccountLevelConsentID2 = !empty($item->AccountLevelConsentID2) ? Crypt::decrypt($item->AccountLevelConsentID2) : 'NA';
            $item->TimeStampConsentID2 = $item->AccountLevelConsentID2 != 'NA' ? $item->TimeStampConsentID2 : 'NA';
            $item->AccountLevelConsentID3 = !empty($item->AccountLevelConsentID3) ? Crypt::decrypt($item->AccountLevelConsentID3) : 'NA';
            $item->TimeStampConsentID3 = $item->AccountLevelConsentID3 != 'NA' ? $item->TimeStampConsentID3 : 'NA';
            return $item;
        });

        return response()->json($history);
    }

    public function createFromCard($idUsuario,$idCard,$consentId1){
        $response = ['status' => 0, 'message' => null];
        $consents = User::getUserConsents($idUsuario);

        try {
            $newHistory = History::create([
                'id_user' => $idUsuario,
                'id_card' => $idCard,
                'id_consent1' => $consentId1,
                'id_consent2' => $consents->id_consent2,
                'id_consent3' => $consents->id_consent3
            ]);

            $response['message'] = 'History created successfully';
        } catch (QueryException $e) {
            $response['status'] = 1;
            $response['message'] = 'Database error '.$e->getMessage();
        }

        return response()->json($response);
    }

    public function createFromConsent($idUsuario,$idConsent2,$idConsent3){
        $response = ['status' => 0, 'message' => null];

        try {
            $newHistory = History::create([
                'id_user' => $idUsuario,
                'id_consent2' => $idConsent2,
                'id_consent3' => $idConsent3
            ]);

            $response['message'] = 'History created successfully';
        } catch (QueryException $e) {
            $response['status'] = 1;
            $response['message'] = 'Database error '.$e->getMessage();
        }

        return response()->json($response);
    }
}
