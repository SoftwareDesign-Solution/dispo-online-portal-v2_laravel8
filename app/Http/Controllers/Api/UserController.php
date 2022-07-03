<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index() {

        $users = User::all()->sortBy('nachname');//->except(1);

        return new UserCollection($users);

    }

    public function saveUser(Request $request) {

        $requestUser = $request->input('user');

        $user = User::find($requestUser['id']);

        if ($user == null)
        {
            $user = new User();
        }

        $user->knr = $requestUser['knr'];
        $user->idnr = $requestUser['idnr'];
        $user->anrede = $requestUser['anrede'];
        $user->vorname = $requestUser['vorname'];
        $user->nachname = $requestUser['nachname'];
        $user->email = $requestUser['email'];

        if ($requestUser['freigabe_ind'] == 1)
            $user->freigabe_ind = 1;
        else
            $user->freigabe_ind = 0;

        if ($requestUser['verified'] == 1)
            $user->Verified = 1;
        else
            $user->Verified = 0;

        if ($requestUser['Admin'] == 1)
            $user->Admin = 1;
        else
            $user->Admin = 0;

        if ($requestUser['password'] !== '')
            $user->password = bcrypt($requestUser['password']);

        $user->save();

    }

    public function Delete(Request $request)
    {

        $iKnr = 0;
        $oUser = null;


        $iKnr = intval($request->input('Knr'));

        if ($iKnr == -1)
            return;

        $oUser = User::where('knr', '=', $iKnr)
            ->first();

        if ($oUser != null)
        {
            $oUser->delete();
        }

    }

    public function Get()
    {

        $oUsers = null;


        $oUsers = User::where('Admin', '=', 0)
            ->where('Api', '=', 0)
            ->select('knr')
            ->get();

        //var_dump($oUsers->pluck('knr'));

        return response()->json($oUsers->pluck('knr'));

    }

    public function Save(Request $request)
    {

        $oUser = null;
        $oUsers = null;
        $oValues = null;
        $sValueString = '';


        $sValueString = $request->input('Value');

        $oValues = json_decode($sValueString);

        foreach ($oValues as $oValue) {

            $oUser = User::where('knr', '=', $oValue->Knr)
                ->first();

            if ($oUser == null) {

                $oUser = new User();
                $oUser->knr = $oValue->Knr;
                $oUser->idnr = $oValue->IdNr;
                $oUser->password = bcrypt($oValue->Nachname);
                $oUser->freigabe_ind = 1;
                $oUser->FirstLogin = 1;

            }

            $oUser->anrede = $oValue->Anrede;
            $oUser->nachname = $oValue->Nachname;
            $oUser->vorname = $oValue->Vorname;
            $oUser->v_ende = $oValue->V_Ende;
            $oUser->email = $oValue->Email;
            $oUser->email_ind = $oValue->Email_ind;
            $oUser->zaehlung_rank = $oValue->Zaehlung_rank;
            $oUser->befragung_rank = $oValue->Befragung_rank;
            //$oUser->freigabe_ind = $oValue->Freigabe_ind;

            // Testuser freischalten
            //if (($oValue->IdNr == '141074') || ($oValue->IdNr == '161074'))
            if ($oValue->Knr == 811) {

                $oUser->freigabe_ind = 1;
                $oUser->FirstLogin = 0;
                $oUser->verified = 1;

            }

            $oUser->save();

        }

    }

}
