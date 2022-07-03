<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function Delete()
    {

        $oProjects = null;


        $oProjects = Project::all();

        foreach ($oProjects as $oProject)
        {
            $oProject->delete();
        }

    }

    public function Get(Request $request)
    {

        $oProjects = null;


        $oProjects = Project::all();

        return response()->json($oProjects);

    }

    public function Save(Request $request)
    {

        $oProject = null;
        $oValues = null;
        $sValueString = '';


        $sValueString = $request->input('Value');

        $oValues = json_decode($sValueString);

        foreach ($oValues as $oValue)
        {

            $oProject = new Project();
            $oProject->Projekt_Nr = $oValue->knr;
            $oProject->Projekt = $oValue->Kurzname;
            $oProject->Delta = $oValue->Delta_online;
            $oProject->Delta_Vorlauf = $oValue->Delta_Vorlauf_online;

            if ($oValue->Datumsvorschlag_online == null)
            {
                $oProject->Datumsvorschlag = 0;
            }
            else
            {
                $oProject->Datumsvorschlag = $oValue->Datumsvorschlag_online;
            }

            $oProject->ObergrenzeAuslagen = $oValue->ObergrenzeAuslagen_online;
            $oProject->ObergrenzeAuslagenEP = $oValue->ObergrenzeAuslagenEP_online;
            $oProject->Haltestellenquelle = '';
            $oProject->ProjektlaufzeitVon = $oValue->ProjektlaufzeitVon_online;
            $oProject->ProjektlaufzeitBis = $oValue->ProjektlaufzeitBis_online;

            $oProject->save();

        }

    }

}
