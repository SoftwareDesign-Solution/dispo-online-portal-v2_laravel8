<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderCart;
use App\Models\Booking;
use Illuminate\Http\Request;

class OrderCartController extends Controller
{

    /*
	public function Delete()
	{

		$oBookings = null;


		$oBookings = Booking::all();

		foreach ($oBookings as $oBooking)
		{
			$oBooking->delete();
		}

	}
	*/

    public function Get(Request $request)
    {

        $oOrderCarts = null;
        $oResult = null;
        $oRow = null;


        $oResult = array();

        $oOrderCarts = OrderCart::all();

        foreach ($oOrderCarts as $oOrderCart)
        {

            foreach ($oOrderCart->OrderCartItems as $oOrderCartItem)
            {

                $oRow = array();
                $oRow['id'] = $oOrderCart->id;
                $oRow['Ma_Knr'] = $oOrderCart->user->knr;
                $oRow['Knr'] = $oOrderCartItem->Knr;
                $oRow['PaketNr'] = $oOrderCartItem->PaketNr;
                $oRow['Vorschlagsdatum'] = $oOrderCartItem->Vorschlagsdatum;
                $oRow['Auslagen'] = $oOrderCartItem->Auslagen;
                $oRow['Honorar'] = $oOrderCartItem->Honorar;
                $oRow['Type'] = $oOrderCartItem->Type;
                $oResult[] = $oRow;

            }

        }

        return response()->json($oResult);

    }

}
