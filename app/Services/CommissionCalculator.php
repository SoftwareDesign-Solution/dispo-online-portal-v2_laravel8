<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Order;
use App\Models\ShiftDay;
use App\Models\User;
use App\Models\UserProject;
use Carbon\Carbon;

class CommissionCalculator
{

    public function __construct()
    {
        //
    }

    public function CalculateOrder($pOrder, $pUser, $pSingleOrder, $pOrderDate = null)
    {

        $bNightShift = false;
        $dTotal = 0.0;
        $oCommissions = null;
        $oOrder = null;
        $oOrderDate = null;
        $oShiftDay = null;
        $oTimeDiff = null;
        $oTimeFrom = null;
        $oTimeFrom2 = null;
        $oTimeTo = null;
        $oTimeTo2 = null;
        $oUser = null;
        $oUserProject = null;
        $sShiftDay = '';
        $sTarif = '';


        // 1. Parameter ermitteln
        //$oOrderDate = Carbon::createFromFormat('Y-m-d', $pOrder->Auftragsdatum);
        $oTimeFrom = strtotime($pOrder->Ab_Zeit);
        $oTimeTo = strtotime($pOrder->An_Zeit);
        $sShiftDay = $pOrder->Schichttag;

        // Auftragsdatum ermitteln
        if ($pOrderDate != null)
        {
            $oOrderDate = Carbon::createFromFormat('Y-m-d', $pOrderDate);
        }
        else
        {
            $oOrderDate = Carbon::createFromFormat('Y-m-d', $pOrder->Auftragsdatum);
        }

        // 2. Mitarbeiter ermitteln


        // 3. Tarif des Mitarbeiters ermitteln
        $oUserProject = UserProject::where('Ma_Knr', '=', $pUser->knr)
            ->where('Projekt_Nr', '=', $pOrder->Projekt_Knr)
            ->first();

        if ($oUserProject != '')
        {
            $sTarif = $oUserProject->Tarif;
        }


        // 4. Prüfen auf Nachtsprung
        $oTimeDiff = (($oTimeTo - $oTimeFrom) / 60);
        $bNightShift = ($oTimeDiff < 0);


        if ($bNightShift)
        {

            // 4.1 Zeiten neu aufteilen
            $oTimeFrom2 = strtotime('00:00:00');
            $oTimeTo2 = $oTimeTo;
            $oTimeTo = strtotime('24:00:00');

        }


        // 5. Commissions ermitteln
        $oCommissions = Commission::where('Projekt_Knr', '=', $pOrder->Projekt_Knr)
            ->where('TeilnetzNr', '=', $pOrder->TeilnetzNr)
            ->where('Schichttag', 'LIKE', '%|' . $pOrder->Schichttag . '|%')
            ->where('Bs', 'LIKE', '%|' . $pOrder->Bs . '|%')
            ->where('Ma_Knr_Von', '<=', $pUser->idnr)
            ->where('Ma_Knr_Bis', '>=', $pUser->idnr)
            ->where('Tarif', '=', $sTarif)
            ->get();

        //var_dump($oCommissions);


        // 3. Basistag berechnen
        $dTotal += $this->CalculateOrderDay($oTimeFrom, $oTimeTo, $oCommissions, $pSingleOrder);

        // 4. Nachtsprung
        if ($bNightShift)
        {

            // 4.1 Folgetag bestimmen
            $oOrderDate = $oOrderDate->addDays(1);


            // 4.2 Schichttag des Folgetages ermitteln
            // Projekt_Knr, TeilnetzNr, Datum -> Schichttag
            $oShiftDay = ShiftDay::where('Projekt_Nr', '=', $pOrder->Projekt_Knr)
                ->where('TeilnetzNr', '=', $pOrder->TeilnetzNr)
                ->where('Datum', '=', $oOrderDate->toDateString())
                ->first();

            if ($oShiftDay != null)
            {
                $sShiftDay = $oShiftDay->Schichttag;
            }

            // 4.3 Commissions ermitteln
            $oCommissions = Commission::where('Projekt_Knr', '=', $pOrder->Projekt_Knr)
                ->where('TeilnetzNr', '=', $pOrder->TeilnetzNr)
                ->where('Schichttag', 'LIKE', '%|' . $pOrder->Schichttag . '|%')
                ->where('Bs', 'LIKE', '%|' . $pOrder->Bs . '|%')
                ->where('Ma_Knr_Von', '<=', $pUser->idnr)
                ->where('Ma_Knr_Bis', '>=', $pUser->idnr)
                ->where('Tarif', '=', $sTarif)
                ->get();

            // 4.4 Folgetag berechnen
            $dTotal += $this->CalculateOrderDay($oTimeFrom2, $oTimeTo2, $oCommissions, $pSingleOrder);

        }

        return $dTotal;

    }

    private function CalculateOrderDay($pTimeFrom, $pTimeTo, $pCommissions, $pSingleOrder)
    {

        $dPrice = 0.0;
        $dRate = 0.0;
        $dTotal = 0.0;
        $oCommissionTimeFrom = null;
        $oCommissionTimeTo = null;
        $oTimeDuration = null;
        $oTimeLeft = null;
        $oTimeTotal = null;


        $oTimeTotal = $pTimeTo - $pTimeFrom;
        $oTimeLeft = $oTimeTotal;

        foreach ($pCommissions as $oCommission)
        {

            $oCommissionTimeFrom = strtotime($oCommission->Zeit_von);
            $oCommissionTimeTo = strtotime($oCommission->Zeit_bis);

            if ($oCommission->Zeit_bis == '00:00:00')
            {
                $oCommissionTimeTo = strtotime('24:00:00');
            }

            if (($pTimeFrom <= $oCommissionTimeTo) && ($pTimeTo >= $oCommissionTimeFrom))
            {

                $oTimeDuration = Min($pTimeTo, $oCommissionTimeTo) - Max($pTimeFrom, $oCommissionTimeFrom);
                $oTimeLeft -= $oTimeDuration;

                if ($pSingleOrder)
                {
                    $dRate = $oCommission->PreisEinzel;
                }
                else
                {
                    $dRate = $oCommission->PreisKette;
                }

                $dPrice = ($oTimeDuration / 60) * $dRate;

                $dTotal += $dPrice;

                if ($oTimeLeft > 0)
                {
                    $pTimeFrom = $oCommissionTimeFrom;
                }

            }

        }

        return $dTotal;

    }

    public function Max($pValue1, $pValue2)
    {
        return ($pValue1 > $pValue2) ? $pValue1 : $pValue2;
    }

    public function Min($pValue1, $pValue2)
    {
        return ($pValue1 < $pValue2) ? $pValue1 : $pValue2;
    }

}
