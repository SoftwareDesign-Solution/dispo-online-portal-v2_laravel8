<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProject;
use Illuminate\Http\Request;

class UserProjectController extends Controller
{

    public function Delete()
    {

        $oUserProjects = null;


        $oUserProjects = UserProject::all();

        foreach ($oUserProjects as $oUserProject)
        {
            $oUserProject->delete();
        }

    }

    public function Get(Request $request)
    {

        $oUserProjects = null;


        $oUserProjects = UserProject::all();

        return response()->json($oUserProjects);

    }

    public function Save(Request $request)
    {

        $oUserProject = null;
        $oValues = null;
        $sValueString = '';


        $sValueString = $request->input('Value');

        $oValues = json_decode($sValueString);

        foreach ($oValues as $oValue)
        {

            $oUserProject = new UserProject();
            $oUserProject->Knr = $oValue->Knr;
            $oUserProject->Ma_Knr = $oValue->Ma_knr;
            $oUserProject->Projekt_Nr = $oValue->Projekt_knr;
            $oUserProject->Tarif = $oValue->Tarif;
            $oUserProject->Freigabe_ind = $oValue->Freigabe_ind;
            $oUserProject->Bs1_ind = $oValue->Bs1_ind;
            $oUserProject->Bs2_ind = $oValue->Bs2_ind;
            $oUserProject->Bs5_ind = $oValue->Bs5_ind;
            $oUserProject->Bs6_ind = $oValue->Bs6_ind;
            $oUserProject->Bs7_ind = $oValue->Bs7_ind;
            $oUserProject->Bs8_ind = $oValue->Bs8_ind;
            $oUserProject->Bs9_ind = $oValue->Bs9_ind;
            $oUserProject->Startorte = $oValue->Startorte;
            $oUserProject->Heimatorte = $oValue->heimatorte;

            $oUserProject->save();

        }

    }

}
