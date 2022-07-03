<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShiftDay;
use Illuminate\Http\Request;

class ShiftDayController extends Controller
{

    public function Delete()
    {

        $oShiftDays = null;


        $oShiftDays = ShiftDay::all();

        foreach ($oShiftDays as $oShiftDay)
        {
            $oShiftDay->delete();
        }

    }

    public function Get(Request $request)
    {

        $oShiftDays = null;


        $oShiftDays = ShiftDay::all();

        return response()->json($oShiftDays);

    }

    public function Save(Request $request)
    {

        $oShiftDay = null;
        $oValues = null;
        $sValueString = '';


        $sValueString = $request->input('Value');

        $oValues = json_decode($sValueString);

        foreach ($oValues as $oValue)
        {

            $oShiftDay = new ShiftDay();
            $oShiftDay->Projekt_Nr = $oValue->Projekt_Knr;
            $oShiftDay->Projekt = $oValue->Projekt;
            $oShiftDay->TeilnetzNr = $oValue->TeilnetzNr;
            $oShiftDay->Schichttag = $oValue->Schichttag;
            $oShiftDay->Datum = $oValue->Datum;

            $oShiftDay->save();

        }

    }

}
