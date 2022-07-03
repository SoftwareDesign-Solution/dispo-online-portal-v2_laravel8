<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCartItemCollection;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\OrderCartItem;
use App\Models\User;
use App\Services\CommissionCalculator;
use Illuminate\Http\Request;
use function PHPUnit\Framework\containsIdentical;

class CheckoutController extends Controller
{

    private $commissionCalculator = null;


    public function __construct(CommissionCalculator $commissionCalculator) {
        $this->commissionCalculator = $commissionCalculator;
    }

    public function Save(Request $request)
    {

        /*
         * Auf Wunsch von Herrn Ehlting am 09.03.2022 deaktiviert
         */

        /*
        $oBooking = null;
        $oOrderCartItems = null;


        $oOrderCartItems = OrderCartItem::all();

        foreach ($oOrderCartItems as $oOrderCartItem)
        {

            $oBooking = new Booking();

            // Knr
            $oBooking->Knr = $oOrderCartItem->Knr;

            // Ma_Knr
            $oBooking->Ma_Knr = $oOrderCartItem->OrderCart->User->knr;

            // Vorschlagsdatum
            $oBooking->Vorschlagsdatum = $oOrderCartItem->Vorschlagsdatum;

            // Honorar
            $oBooking->Honorar = $oOrderCartItem->Honorar;

            // Auslagen
            $oBooking->Auslagen = $oOrderCartItem->Auslagen;

            // Typ
            $oBooking->Type = $oOrderCartItem->Type;

            $oBooking->save();

            $oOrderCartItem->delete();

        }
        */

    }

    public function index(Request $request) {

        $user = $request->user();

        $orderCart = $user->OrderCart;

        $orderCartItems = $orderCart->OrderCartItems()->whereRaw('order_cart_items.created_at > (UTC_TIMESTAMP() - INTERVAL 1 HOUR)')->get();

        return new OrderCartItemCollection($orderCartItems);

    }

    public function addOrder(Request $request) {

        $honorar = 0.0;
        $orderCart = null;
        $orderCartItem = null;
        $orders = null;
        $user = null;
        $valid = true;
        $response = '';


        $user = $request->user();

        // Prüfen, ob der Mitarbeiter ein Auftragskorb hat
        if ($user->OrderCart == null) {
            $orderCart = new OrderCart();
            $user->OrderCart()->save($orderCart);

            $user->load('OrderCart');

        }

        $orders = $request->input('orders');

        if ($orders == null) {
            return;
        }

        try {

            foreach ($orders as $order) {

                $orderCartItem = $user
                    ->OrderCart
                    ->whereHas('OrderCartItems', function ($query) use($order) {
                        $query->where('Knr', '=', $order['Knr']);
                        $query->whereRaw('created_at > (UTC_TIMESTAMP() - INTERVAL 1 HOUR)');
                    })->get();

                if (count($orderCartItem) > 0)
                {
                    continue;
                }

                $ordersInPackage = Order::where('PaketNr', $order['PaketNr'])->get();

                foreach ($ordersInPackage as $orderInPackage) {

                    $userProject = $user->UserProjects()->where('Projekt_Nr', $order['Projekt_Knr'])->first();

                    if ($userProject["Bs{$orderInPackage->Bs}_ind"] === 0)
                    {
                        echo $orderInPackage->id;
                        $valid = false;
                    }

                    /*
                    $orderCartItem = $user
                        ->OrderCart
                        ->whereHas('OrderCartItems', function ($query) use($orderInPackage) {
                            $query->where('Knr', '=', $orderInPackage['Knr']);
                        })->get();

                    if (count($orderCartItem) > 0)
                    {
                        echo $orderInPackage->Knr;
                        $valid = false;
                    }
                    */

                }

                if (!$valid)
                    continue;

                $oOrder = Order::where('Knr', '=', $order['Knr'])
                    ->first();

                $singleOrder = (Order::where('PaketNr', $oOrder->PaketNr)->count() == 1);

                $orderCartItem = $user
                    ->OrderCart
                    ->whereHas('OrderCartItems', function ($query) use($order) {
                        $query->where('Knr', '=', $order['Knr']);
                        $query->whereRaw('created_at > (UTC_TIMESTAMP() - INTERVAL 1 HOUR)');
                    })->get();

                if (count($orderCartItem) > 0)
                {
                    $response .= 'Auftrag ' . $order['Knr'] . ' aus dem Paket ' . $order['PaketNr'] . ' bereits im Auftragskorb vorhanden\r\n';
                    continue;
                }

                if (count($orderCartItem) === 0)
                {

                    $honorar = $this->commissionCalculator->CalculateOrder(
                        $oOrder,
                        $user,
                        $singleOrder,
                        ($order['Vorschlagsdatum'] !== '') ? $order['Vorschlagsdatum'] : null);

                    $orderCartItem = new OrderCartItem();
                    $orderCartItem->Knr = $order['Knr'];
                    $orderCartItem->PaketNr = $order['PaketNr'];

                    if ($order['Vorschlagsdatum'] !== '') {
                        $orderCartItem->Vorschlagsdatum = $order['Vorschlagsdatum'];
                    }

                    if ($order['Auslagen'] !== '')
                    {

                        $orderCartItem->Auslagen = str_replace(['€', '\u202f', '\u20ac'], '', str_replace(',', '.', $order['Auslagen']));

                    }
                    else
                    {
                        $orderCartItem->Auslagen = 0.0;
                    }

                    $orderCartItem->Honorar = $honorar;
                    $orderCartItem->Type = $order['Type'];

                    $user->OrderCart->OrderCartItems()->save($orderCartItem);

                }

            }

            if (($valid) && ($response === ''))
                echo 'Die Aufträge wurden dem Auftragskorb hinzugefügt';
            else if ($response !== '')
                echo 'Folgende Aufträge konnten nicht dem Auftragskorb hinzugefügt werden.\r\n\r\n' . $response;
            else
                echo 'Die Aufträge konnten nicht dem Auftragskorb hinzugefügt werden. Fehler: Bausteinvalidierung.';

        }catch (\Exception $exception) {
            var_dump($exception);
        }

    }

    public function deleteOrder(Request $request) {

        try {

            $user = $request->user();

            $orderCart = $user->OrderCart;

            $orderCart->OrderCartItems()->where('PaketNr', '=', $request->input('PaketNr'))->delete();

        } catch (\Exception $exception) {
            var_dump($exception);
        }

    }

}
