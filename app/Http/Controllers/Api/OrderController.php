<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\OrderCartItem;
use App\Models\User;
use App\Services\CommissionCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    private $commissionCalculator = null;


    public function __construct(CommissionCalculator $commissionCalculator) {
        $this->commissionCalculator = $commissionCalculator;
    }

    public function index(Request $request) {

        $dateTime = null;
        $filter = null;
        $orders = null;
        $query = null;
        $user = null;


        $dateTime = Carbon::now()->subHour(1);

        //echo $dateTime->toDateTimeString();

        /*
        $result = DB::table('order_cart_items')
            ->select(DB::raw(1))
            ->whereRaw('order_cart_items.Knr = 1404561')
            ->whereRaw('order_cart_items.created_at > (NOW() - INTERVAL 1 HOUR)')
            ->get();

        var_dump($result);
        */

        $filter = $request->input('filter');

        $user = $request->user();
        //$user = User::find(21);

        // Aufträge über Benutzer -> Benutzerprojekte laden
        $query = $user->Orders();


        /* ----- Filterung ----- */
        $query->whereHas('OrderFilter', function ($subQuery) use ($filter) {

            // Prüfen, ob Auftragsdatum leer ist bei Schichttagsaufträge
            if ($filter['type'] === 'ShiftDayOrders') {
                $subQuery->whereNull('Auftragsdatum');
            }
            else
            {
                $subQuery->whereNotNull('Auftragsdatum');
            }

            if ($filter['type'] === 'OpenOrders') {
                $subQuery->join('projects', 'order_filters.Projekt_Knr', '=', 'projects.Projekt_Nr');
                $subQuery->whereRaw('(order_filters.Auftragsdatum >= DATE_ADD(CURRENT_DATE(), INTERVAL projects.Delta DAY))');
            }
            else if ($filter['type'] === 'PastOrders') {
                $subQuery->join('projects', 'order_filters.Projekt_Knr', '=', 'projects.Projekt_Nr');
                $subQuery->whereRaw('order_filters.Auftragsdatum <= DATE_ADD(CURRENT_DATE(), INTERVAL projects.Delta - 1 DAY)');
            }

            // Auftragsdatum von
            if ($filter['auftragsdatumFrom']) {
                $subQuery->whereDate('Auftragsdatum', '>=', $filter['auftragsdatumFrom']);
            }

            // Auftragsdatum bis
            if ($filter['auftragsdatumTo']) {
                $subQuery->whereDate('Auftragsdatum', '<=', $filter['auftragsdatumTo']);
            }

            // Uhrzeit von
            if ($filter['timeFrom']) {
                $subQuery->whereTime('Ab_Zeit', '>=', $filter['timeFrom']);
            }

            // Uhrzeit bis
            if ($filter['timeTo']) {
                $subQuery->whereTime('An_Zeit', '<=', $filter['timeTo']);
            }

            // Start
            if ($filter['start']) {
                $subQuery->where('Ab_Ort', $filter['start']);
            }

            // Ziel
            if ($filter['ziel']) {
                $subQuery->where('An_Ort', $filter['ziel']);
            }

            // Projekt
            if ($filter['project']) {
                $subQuery->where('Projekt', $filter['project']);
            }

            // Schichttag
            if ($filter['schichttag']) {
                $subQuery->where('Schichttag', $filter['schichttag']);
            }

            //$subQuery->where('PaketNr', 1276310);

        });
        /* ----- /Filterung ----- */

        /*
        if ($filter['type'] === 'OpenOrders') {
            $query->join('projects', 'orders.Projekt_Knr', '=', 'projects.Projekt_Nr');
            $query->whereRaw('(orders.Auftragsdatum >= DATE_ADD(CURRENT_DATE(), INTERVAL projects.Delta DAY))');
            // (orders.Auftragsdatum >= DATE_ADD("' . $pDate . '", INTERVAL projects.Delta DAY))

        }
        else if ($filter['type'] === 'PastOrders') {
            // (orders.Auftragsdatum <= DATE_ADD("' . $pDate . '", INTERVAL projects.Delta - 1 DAY))
            //$subQuery->whereRaw('(orders.Auftragsdatum >= DATE_ADD(CURRENT_DATE(), INTERVAL projects.Delta - 1 DAY))');
            $query->join('projects', 'orders.Projekt_Knr', '=', 'projects.Projekt_Nr');
            $query->whereRaw('(orders.Auftragsdatum <= DATE_ADD(CURRENT_DATE(), INTERVAL projects.Delta - 1 DAY))');
        }
        */

        // Prüfen, ob der Auftrag im Auftragskorb ist (CreateDate > (NOW - 60))
        $query->whereNotExists(function ($subQuery) use ($dateTime) {
            $subQuery->select(DB::raw(1))
                ->from('order_cart_items')
                ->whereRaw('order_cart_items.Knr = orders.Knr')
                ->whereRaw('order_cart_items.created_at > (UTC_TIMESTAMP() - INTERVAL 1 HOUR)');
        });

        // Prüfen, ob der Auftrag in der Buchungstabelle vorhanden ist
        $query->whereNotExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('bookings')
                ->whereRaw('bookings.Knr = orders.Knr');
        });

        //return $query->toSql();

        // Aufträge auf Basis der Filterung laden
        $orders = $query->orderBy('PaketNr', 'asc')->orderBy('OrdNr', 'asc')->get();
        //$orders = $query->orderBy('Auftragsdatum', 'asc')->orderBy('Ab_Zeit', 'asc')->get();


        //$orders = $query->get();
        $excludedPackageIds = collect();
        $allowedOrders = collect();

        // Bausteineinschränkung
        foreach ($orders as $order) {
            $userProject = $order->UserProject()->where('Ma_Knr', $user->knr)->first();
            $bs = $order->Bs;

            if($userProject['Bs' . $bs . '_ind'] == 1) {
                $allowedOrders->push($order);
            } else {
                $excludedPackageIds->push($order->PaketNr);
            }
        }

        $orders = $allowedOrders->filter(function ($order) use($excludedPackageIds) {
            return !$excludedPackageIds->contains($order->PaketNr);
        });


        $additional = [
            'Projects' => $orders->pluck('Projekt')->unique()->sort()->values(),
            'Start' => $orders->pluck('Ab_Ort')->unique()->sort()->values(),
            'Ziel' => $orders->pluck('An_Ort')->unique()->sort()->values(),
            'Shiftdays' => $orders->pluck('Schichttag')->unique()->sort()->values(),
        ];

        $orderCollection = new OrderCollection($orders);
        $orderCollection//->additional(['Type' => $filter['type']])
            ->additional($additional);

        //print_r($orders->pluck('Ab_Ort'));

        return $orderCollection;

    }

    public function index2(Request $request) {

        /*
         *
         *
         * Scopes für globale Abfragen (https://medium.com/@janaksan_/using-scope-with-laravel-7c80dd6a2c3d)
         */

        //$from = 'Wolfhagen';
        //$to = 'Wolfhagen';

        //$from = 'Korbach Hbf';
        //$to = 'Korbach Hbf';
        //$project = '118';

        $from = '';
        $to = '';
        $project = '';

        $user = User::find(4581);


        // UserProject -> with
        $query = $user->Orders();

        /* ----- Filterung ----- */
        $query->whereHas('OrderFilter', function ($subQuery) use ($project, $from, $to) {

            // TODO: Prüfen, ob Auftragsdatum leer ist bei Schichttagsaufträge

            if ($project) {
                $subQuery->where('Projekt_Knr', $project);
            }

            if ($from) {
                $subQuery->where('Ab_Ort', $from);
            }

            if ($to) {
                $subQuery->where('An_Ort', $to);
            }

            //$subQuery->where('PaketNr', 1276310);

        });
        /* ----- /Filterung ----- */

        // TODO: Prüfen, ob der Auftrag im Auftragskorb ist (CreateDate > (NOW - 60))

        // TODO: Prüfen, ob der Auftrag in der Buchungstabelle ist


        /*
         * - Mitarbeiter ist einem oder mehreren Projekten zugeordnet
         * - Mitarbeiter ist für bestimmte Bausteine Bs(n)_ind = 1 freigeschaltet, z.B. Bs1_ind = 1, Bs2_ind = 0
         * - Ein Projekt hat mehrere Pakete
         * - Ein Paket kann aus einem oder mehreren Aufträgen bestehen
         * - Jeder Auftrag hat ein eigenen Baustein Bs z.B. 1, 2, 5, 6, 7, 8, 9
         *
         * - Wenn ein Auftrag in einem Paket ein nicht freigeschalteten Baustein hat, dann wird das gesamte Paket nicht angezeigt
         *
         */

        // Bausteineinschränkung

        /*
        $query->whereIn('PaketNr', function ($subQuery) use ($user) {

            // Bs1_ind = 1
            // Bs2_ind = 0
            // Bs5_ind = 1
            // -> 1, 5

            $bausteine = [];

            if ($user->Bs1_ind == 1)
                $bausteine[] = 1;

            if ($user->Bs2_ind == 1)
                $bausteine[] = 2;

            if ($user->Bs5_ind == 1)
                $bausteine[] = 5;

            if ($user->Bs6_ind == 1)
                $bausteine[] = 6;

            if ($user->Bs7_ind == 1)
                $bausteine[] = 7;

            if ($user->Bs8_ind == 1)
                $bausteine[] = 8;

            if ($user->Bs9_ind == 1)
                $bausteine[] = 9;

            //if ($user['Bs' + $i + '_ind'] != null)

            $subQuery->whereIn('Bs', $bausteine);

            /*
             * $query->select(DB::raw(1))
              ->from('orders')
              ->whereRaw('orders.user_id = users.id');
             *

        });
        */

        $orders = $query->get();


        //dump($query->toSql());
        dump($orders);

        //return;

        // TODO: Bausteinfilterung (https://stackoverflow.com/questions/45359514/laravel-collection-group-by/45360653#45360653)
        // Orders nach Bausteinen filtern, siehe Zeile 131
        // Orders nach Paket Gruppieren
        // Jede Gruppe durchgehen, ob es die gleiche Anzahl an Aufträgen in dem Paket sind

        $filteredOrders = [];
        $valid = true;

        foreach ($orders as $order) {

            //echo "Bs{$order->Bs}_ind";

            $ordersInPackage = $orders->where('PaketNr', $order->PaketNr);

            //dump($ordersInPackage);

            foreach ($ordersInPackage as $orderInPackage) {

                //echo $user["Bs{$orderInPackage->Bs}_ind"];

                echo "Bs{$orderInPackage->Bs}_ind";

                $userProject = $user->UserProjects()->where('Projekt_Nr', $order->Projekt_Knr)->first();

                //dump($userProject);

                //dump($orderInPackage->UserProject["Bs{$orderInPackage->Bs}_ind"]);

                if ($userProject["Bs{$orderInPackage->Bs}_ind"] === 0)
                {
                    echo $orderInPackage->id;
                    $valid = false;
                }

            }

            if ($valid)
                $filteredOrders[] = $order;

            $valid = true;

        }

        dump($filteredOrders);




        /*
        if ($from) {
            $query->whereHas('OrderFilter', function ($subQuery) use ($from) {
                $subQuery->where('Ab_Ort', $from);
            });
        }

        if ($to) {
            $query->whereHas('OrderFilter', function ($subQuery) use ($to) {
                $subQuery->where('An_Ort', $to);
            });
        }
        */



        // ->whereHas('card.users', function ($query) use ($userId) {

        /*
        foreach ($userProjects as $userProject) {

            //var_dump($userProject->Project->Delta);

            //dump($userProject->Orders);

            foreach ($userProject->OrderFilters as $orderFilter) {

                dump($orderFilter);

            }

        }
        */

    }

    /*
     * Methode addOrder in CheckoutController ausgelagert
    public function addOrder(Request $request) {

        $honorar = 0.0;
        $orderCart = null;
        $orderCartItem = null;
        $orders = null;
        $user = null;
        $valid = true;


        $user = User::find(4581);

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

        foreach ($orders as $order) {

            $ordersInPackage = Order::where('PaketNr', $order['PaketNr'])->get();

            //dump($ordersInPackage);

            foreach ($ordersInPackage as $orderInPackage) {

                //echo $user["Bs{$orderInPackage->Bs}_ind"];

                //echo "Bs{$orderInPackage->Bs}_ind";

                $userProject = $user->UserProjects()->where('Projekt_Nr', $order['Projekt_Knr'])->first();

                //dump($userProject);

                //dump($orderInPackage->UserProject["Bs{$orderInPackage->Bs}_ind"]);

                if ($userProject["Bs{$orderInPackage->Bs}_ind"] === 0)
                {
                    //echo $orderInPackage->id;
                    $valid = false;
                }

            }

            if (!$valid)
                continue;

            $oOrder = Order::where('Knr', '=', $order['Knr'])
                ->first();

            $singleOrder = (Order::where('PaketNr', $oOrder->PaketNr)->count() == 1);

            //echo $singleOrder;

            $orderCartItem = $user
                ->OrderCart
                ->whereHas('OrderCartItems', function ($query) use($order) {
                    $query->where('Knr', '=', $order['Knr']);
                })->get();

            //echo count($orderCartItem);

            if (count($orderCartItem) === 0)
            {

                $honorar = $this->commissionCalculator->CalculateOrder(
                    $oOrder,
                    $user,
                    $singleOrder,
                    ($order['Vorschlagsdatum'] !== '') ? $order['Vorschlagsdatum'] : null);

                //var_dump($honorar);

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
                $orderCartItem->Type = 'OpenOrders';

                $user->OrderCart->OrderCartItems()->save($orderCartItem);

            }

            //print_r($order);

            //echo $order['id'];

        }

    }
    */

    public function Delete()
    {

        $oOrders = null;


        $oOrders = Order::all();

        foreach ($oOrders as $oOrder)
        {
            $oOrder->delete();
        }

    }

    public function Get(Request $request)
    {

        $oOrders = null;


        $oOrders = Order::all();

        return response()->json($oOrders);

    }

    public function Save(Request $request)
    {

        $oOrder = null;
        $oValues = null;
        $sValueString = '';


        $sValueString = $request->input('Value');

        $oValues = json_decode($sValueString);

        foreach ($oValues as $oValue)
        {

            $oOrder = new Order();
            $oOrder->Knr = $oValue->Knr;
            $oOrder->Projekt_Knr = $oValue->Projekt_Knr;
            $oOrder->Projekt = $oValue->Projekt;
            $oOrder->FahrtNr = $oValue->FahrtNr;
            $oOrder->Auftragsdatum = $oValue->Auftragsdatum;
            $oOrder->Schichttag = $oValue->Schichttag;
            $oOrder->Wochentag = $oValue->Wochentag;
            $oOrder->Ab_Ort = $oValue->Ab_Ort;
            $oOrder->Ab_Zeit = $oValue->Ab_Zeit;
            $oOrder->An_Ort = $oValue->An_Ort;
            $oOrder->An_Zeit = $oValue->An_Zeit;
            $oOrder->Bs = $oValue->Bs;
            $oOrder->AnzahlPers = $oValue->AnzahlPers;
            $oOrder->TeilnetzNr = $oValue->TeilnetzNr;
            $oOrder->AG9 = $oValue->AG9;
            $oOrder->Ab_mm = $oValue->Ab_mm;
            $oOrder->An_mm = $oValue->An_mm;
            $oOrder->EpVorschlag_dat = $oValue->EpVorschlag_dat;
            $oOrder->SysBemerkung = $oValue->SysBemerkung;
            $oOrder->PaketNr = $oValue->PaketNr;
            $oOrder->OrdNr = $oValue->OrdNr;
            $oOrder->Dauer = $oValue->Dauer;
            $oOrder->Auslagen = $oValue->Auslagen;

            $oOrder->save();

        }

    }

}
