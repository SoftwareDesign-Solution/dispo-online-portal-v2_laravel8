<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{

    public function Delete()
    {

        $oCommissions = null;


        $oCommissions = Commission::all();

        foreach ($oCommissions as $oCommission)
        {
            $oCommission->delete();
        }

    }

    public function Get(Request $request)
    {

        $oCommissions = null;


        $oCommissions = Commission::all();

        return response()->json($oCommissions);

    }

    public function Save(Request $request)
    {

        $oCommission = null;
        $oValues = null;
        $sValueString = '';


        $sValueString = $request->input('Value');

        $oValues = json_decode($sValueString);

        foreach ($oValues as $oValue)
        {

            $oCommission = new Commission();
            $oCommission->Projekt_Knr = $oValue->Projekt_Knr;
            $oCommission->Ma_Knr_Von = $oValue->Ma_Knr_Von;
            $oCommission->Ma_Knr_Bis = $oValue->Ma_Knr_Bis;
            $oCommission->TeilnetzNr = $oValue->TeilnetzNr;
            $oCommission->Bs = $oValue->Bs;
            $oCommission->Schichttag = $oValue->Schichttag;
            $oCommission->Tarif = $oValue->Tarif;
            $oCommission->Zeit_von = $oValue->Zeit_von;
            $oCommission->Zeit_bis = $oValue->Zeit_bis;
            $oCommission->PreisEinzel = $oValue->PreisEinzel;
            $oCommission->PreisKette = $oValue->PreisKette;

            $oCommission->save();

        }

    }

}
