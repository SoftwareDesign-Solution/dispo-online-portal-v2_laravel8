<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\OrderCartItem;
use App\Models\OrderFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{

    public function Delete()
    {

        $oBookings = null;
        $oOrders = null;
        $oOrderFilters = null;


        $oBookings = Booking::all();

        foreach ($oBookings as $oBooking)
        {

            $oOrders = Order::where('Knr', '=', $oBooking->Knr)
                ->get();

            foreach ($oOrders as $oOrder)
            {
                $oOrder->delete();
            }


            $oOrderFilters = OrderFilter::where('Knr', $oBooking->Knr)
                ->get();

            foreach ($oOrderFilters as $oOrderFilter)
            {
                $oOrderFilter->delete();
            }


            $oBooking->delete();

        }

    }

    public function Get(Request $request)
    {

        $oBookings = null;


        $oBookings = Booking::all();

        return response()->json($oBookings);

    }

    public function ShowBookingDetails(Request $pRequest)
    {

        $oBookings = null;


        $oBookings = DB::table('bookings')
            ->join('users', 'bookings.Ma_Knr', '=', 'users.knr')
            ->join('orders', 'bookings.Knr', '=', 'orders.Knr')
            ->join('projects', 'orders.Projekt_Knr', '=', 'projects.Projekt_Nr')
            ->select(DB::raw('projects.Projekt, CONCAT(users.vorname, \' \', users.nachname) AS Interviewer, COUNT(bookings.id) AS \'Anzahl Buchungen\''))
            ->groupBy(DB::raw('projects.Projekt, users.vorname, users.nachname'))
            ->get();

        return response()->json($oBookings);

    }

    public function bookOrders(Request $request) {

        $user = $request->user();

        $orders = $request->input('orders');

        if (is_array($orders))
        {

            try {

                foreach ($orders as $order) {

                    $booking = new Booking();
                    $booking->Knr = $order['Knr'];
                    $booking->Ma_Knr = $user->knr;//3154;
                    $booking->Vorschlagsdatum = $order['Vorschlagsdatum'];
                    $booking->Honorar = $order['Honorar'];
                    $booking->Auslagen = $order['Auslagen'];
                    $booking->Type = $order['Type'];
                    $booking->save();

                    $orderCartItem = OrderCartItem::find($order['id']);
                    $orderCartItem->delete();

                }

                echo 'Die Aufträge wurden verbindlich gebucht.';

            } catch (\Exception $exception) {
                var_dump($exception);
            }

        }

    }

}
