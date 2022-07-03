<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderFilter;
use Illuminate\Http\Request;

class OrderFilterController extends Controller
{

    public function Delete()
    {

        $oOrderFilters = null;


        $oOrderFilters = OrderFilter::all();

        foreach ($oOrderFilters as $oOrderFilter)
        {
            $oOrderFilter->delete();
        }

    }

    public function Get(Request $request)
    {

        $oOrderFilters = null;


        $oOrderFilters = OrderFilter::all();

        return response()->json($oOrderFilters);

    }

    public function Save(Request $request)
    {

        $oOrderFilter = null;
        $oValues = null;
        $sValueString = '';


        $sValueString = $request->input('Value');

        $oValues = json_decode($sValueString);

        foreach ($oValues as $oValue)
        {

            $oOrderFilter = new OrderFilter();
            $oOrderFilter->Knr = $oValue->Knr;
            $oOrderFilter->Projekt_Knr = $oValue->Projekt_Knr;
            $oOrderFilter->PaketNr = $oValue->PaketNr;
            $oOrderFilter->Auftragsdatum = $oValue->Auftragsdatum;
            $oOrderFilter->Ab_Ort = $oValue->Ab_Ort;
            $oOrderFilter->Ab_Zeit = $oValue->Ab_Zeit;
            $oOrderFilter->An_Ort = $oValue->An_Ort;
            $oOrderFilter->An_Zeit = $oValue->An_Zeit;

            $oOrderFilter->save();

        }

    }

    public function addBausteineToOrderFilter(Request $request) {

        $orderFilters = OrderFilter::all();

        foreach ($orderFilters as $orderFilter) {

            $order = Order::firstWhere('Knr', $orderFilter->Knr);

            OrderFilter::where('Knr', $order->Knr)->update(['Bs' . $order->Bs . '_ind' => 1]);

        }

        return response()->json("Fertig");

    }

}
