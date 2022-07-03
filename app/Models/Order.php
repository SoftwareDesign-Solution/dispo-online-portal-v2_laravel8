<?php

/***************************************************************
 * Project: TRENDline Dispo Online
 ***************************************************************
 * \file    Order.php
 * \brief
 * \author  Manuel Kübler (mail@softwaredesign-solution.de)
 * \version 1.0.0
 ***************************************************************
 * \date    2018-09-13 - Erstellung der Klasse
 ***************************************************************
 * \todo    ...
 * \test    ...
 * \bug     ...
 * \remarks ...
 ***************************************************************/


namespace App\Models;


use DB;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use Carbon;


class Order extends Model
{

    use Compoships;

    /*************************
     * Variables
     *************************/
    public $timestamps = false;


    /*************************
     * Constructor
     *************************/
    public function __construct()
    {
        //
    }


    /*************************
     * Relations
     *************************/
    public function UserProject()
    {
        return $this->hasOne('App\Models\UserProject', 'Projekt_Nr', 'Projekt_Knr');
    }

    public function Project()
    {
        return $this->hasOne(Project::class, 'Projekt_Nr', 'Projekt_Knr');
    }

    public function OrderFilter()
    {
        return $this->hasOne('App\Models\OrderFilter', 'Knr', 'Knr');
    }

    public function ShiftDays()
    {

        // return $this->hasMany('App\Comment', 'foreign_key', 'local_key');

        return $this->hasMany('App\Models\ShiftDay', ['Projekt_Nr', 'TeilnetzNr', 'Schichttag'], ['Projekt_Knr', 'TeilnetzNr', 'Schichttag']);

    }


    /*************************
     * Accessor
     *************************/
    //


    /*************************
     * Mutators
     *************************/
    //


    /*************************
     * Functions
     *************************/
    public function scopeOfType($query, $type)
    {
        return $query->where('FahrtNr', $type);
    }

    public function scopeOpenOrder($query, $pDate)
    {
        return $query->whereDate('Auftragsdatum', '>=', $pDate);
    }

    public static function GetOpenOrders($pUserId, $pDate, $pDateFrom = null, $pTimeFrom = null, $pTimeTo = null, $pFrom = null, $pTo = null, $pProject = null)
    {

        $oDateFrom = null;
        $oOrders = null;


        if ($pDateFrom != null)
        {
            $oDateFrom = Carbon::createFromFormat('d.m.Y', $pDateFrom);
        }

        /* Dominik
         * -------
         * Mitarbeiter_Bs_1 == Auftrag_Bs_1 OR 0
         *
         */

        /* Manuel
         * ------
         *
         * user. oder user_projects.Bs1()
         * user. oder user_projects.Bs2()
         */

        $oOrders = DB::table('users')
            ->join('user_projects', 'users.knr', '=', 'user_projects.Ma_Knr') // Hier stehen die Baustein-Beschränkungen drin
            ->join('projects', 'user_projects.Projekt_Nr', '=', 'projects.Projekt_Nr')
            ->join('order_filters', 'projects.Projekt_Nr', '=', 'order_filters.Projekt_Knr')
            ->join('orders', 'order_filters.Knr', '=', 'orders.Knr') // Hier stehen die Bausteine drin
            //->join('orders', 'projects.Projekt_Nr', '=', 'orders.Projekt_Knr')
            ->distinct()
            //->select(DB::raw('projects.*, orders.*'))
            ->select(DB::raw('user_projects.Bs1_ind, user_projects.Bs2_ind, user_projects.Bs5_ind, user_projects.Bs6_ind, user_projects.Bs7_ind, user_projects.Bs8_ind, user_projects.Bs9_ind, projects.*, order_filters.Ab_Ort AS Ab_OrtFilter, order_filters.Ab_Zeit AS Ab_ZeitFilter, order_filters.An_Ort AS An_OrtFilter, order_filters.An_Zeit AS An_ZeitFilter, order_filters.Auftragsdatum AS AuftragsdatumFilter, orders.*'))
            //->select('orders.*')
            ->where('users.id', '=', $pUserId)
            ->whereNotNull('orders.Auftragsdatum')
            //->whereRaw('(orders.Auftragsdatum >= DATE_ADD("' . $pDate . '", INTERVAL projects.Delta DAY))')
            //->whereRaw('((user_projects.Bs5_ind = 1) AND (orders.Bs = 5))')
            //->whereRaw('CASE WHEN (user_projects.Bs5_ind = 1) THEN (orders.Bs = 5) END')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('order_cart_items')
                    ->whereRaw('order_cart_items.Knr = orders.Knr');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('bookings')
                    ->whereRaw('bookings.Knr = orders.Knr');
            });

        if ($oDateFrom != null)
        {
            //$oOrders = $oOrders->whereDate('Auftragsdatum', '>=', $sDateFrom);
            $oOrders = $oOrders->whereDate('order_filters.Auftragsdatum', '>=', $oDateFrom->toDateString());
        }

        if (($pTimeFrom != null) && ($pTimeFrom != ''))
        {
            //$oOrders = $oOrders->whereTime('Ab_Zeit', '>=', $pTimeFrom);
            $oOrders = $oOrders->whereTime('order_filters.Ab_Zeit', '>=', $pTimeFrom);
        }

        if (($pTimeTo != null) && ($pTimeTo != ''))
        {
            //$oOrders = $oOrders->whereTime('An_Zeit', '<=', $pTimeTo);
            $oOrders = $oOrders->whereTime('order_filters.An_Zeit', '<=', $pTimeTo);
        }

        if (($pFrom != null) && ($pFrom != ''))
        {
            //$oOrders = $oOrders->where('Ab_Ort', '=', $pFrom);
            $oOrders = $oOrders->where('order_filters.Ab_Ort', '=', $pFrom);
        }

        if (($pTo != null) && ($pTo != ''))
        {
            //$oOrders = $oOrders->where('An_Ort', '=', $pTo);
            $oOrders = $oOrders->where('order_filters.An_Ort', '=', $pTo);
        }

        if (intval($pProject) > 0)
        {
            $oOrders = $oOrders->where('projects.Projekt_Nr', '=', intval($pProject));
        }

        $oOrders = $oOrders->orderBy('OrdNr', 'asc');

        $oOrders = $oOrders->get();

        return $oOrders;

    }

    public static function GetPastOrders($pUserId, $pDate, $pDateFrom = null, $pTimeFrom = null, $pTimeTo = null, $pFrom = null, $pTo = null, $pProject = null)
    {

        $oDateFrom = null;
        $oOrders = null;


        if ($pDateFrom != null)
        {
            $oDateFrom = Carbon::createFromFormat('d.m.Y', $pDateFrom);
        }

        $oOrders = DB::table('users')
            ->join('user_projects', 'users.knr', '=', 'user_projects.Ma_Knr')
            ->join('projects', 'user_projects.Projekt_Nr', '=', 'projects.Projekt_Nr')
            ->join('order_filters', 'projects.Projekt_Nr', '=', 'order_filters.Projekt_Knr')
            ->join('orders', 'order_filters.Knr', '=', 'orders.Knr')
            //->join('orders', 'projects.Projekt_Nr', '=', 'orders.Projekt_Knr')
            ->distinct()
            //->select(DB::raw('projects.*, orders.*'))
            ->select(DB::raw('user_projects.Bs1_ind, user_projects.Bs2_ind, user_projects.Bs5_ind, user_projects.Bs6_ind, user_projects.Bs7_ind, user_projects.Bs8_ind, user_projects.Bs9_ind, projects.*, order_filters.Ab_Ort AS Ab_OrtFilter, order_filters.Ab_Zeit AS Ab_ZeitFilter, order_filters.An_Ort AS An_OrtFilter, order_filters.An_Zeit AS An_ZeitFilter, order_filters.Auftragsdatum AS AuftragsdatumFilter, orders.*'))
            //->select('orders.*')
            ->where('users.id', '=', $pUserId)
            ->whereNotNull('orders.Auftragsdatum')
            ->whereRaw('(orders.Auftragsdatum <= DATE_ADD("' . $pDate . '", INTERVAL projects.Delta - 1 DAY))')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('order_cart_items')
                    ->whereRaw('order_cart_items.Knr = orders.Knr');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('bookings')
                    ->whereRaw('bookings.Knr = orders.Knr');
            });

        if ($oDateFrom != null)
        {
            //$oOrders = $oOrders->whereDate('Auftragsdatum', '>=', $sDateFrom);
            $oOrders = $oOrders->whereDate('order_filters.Auftragsdatum', '>=', $oDateFrom->toDateString());
        }

        if (($pTimeFrom != null) && ($pTimeFrom != ''))
        {
            //$oOrders = $oOrders->whereTime('Ab_Zeit', '>=', $pTimeFrom);
            $oOrders = $oOrders->whereTime('order_filters.Ab_Zeit', '>=', $pTimeFrom);
        }

        if (($pTimeTo != null) && ($pTimeTo != ''))
        {
            //$oOrders = $oOrders->whereTime('An_Zeit', '<=', $pTimeTo);
            $oOrders = $oOrders->whereTime('order_filters.An_Zeit', '<=', $pTimeTo);
        }

        if (($pFrom != null) && ($pFrom != ''))
        {
            //$oOrders = $oOrders->where('Ab_Ort', '=', $pFrom);
            $oOrders = $oOrders->where('order_filters.Ab_Ort', '=', $pFrom);
        }

        if (($pTo != null) && ($pTo != ''))
        {
            //$oOrders = $oOrders->where('An_Ort', '=', $pTo);
            $oOrders = $oOrders->where('order_filters.An_Ort', '=', $pTo);
        }

        if (intval($pProject) > 0)
        {
            $oOrders = $oOrders->where('projects.Projekt_Nr', '=', intval($pProject));
        }

        $oOrders = $oOrders->orderBy('OrdNr', 'asc');

        $oOrders = $oOrders->get();

        return $oOrders;

    }

    public static function GetShiftDayOrders($pUserId, $pDate, $pDateFrom = null, $pTimeFrom = null, $pTimeTo = null, $pFrom = null, $pTo = null, $pProject = null)
    {

        $oDateFrom = null;
        $oOrders = null;


        if ($pDateFrom != null)
        {
            $oDateFrom = Carbon::createFromFormat('d.m.Y', $pDateFrom);
        }

        $oOrders = DB::table('users')
            ->join('user_projects', 'users.knr', '=', 'user_projects.Ma_Knr')
            ->join('projects', 'user_projects.Projekt_Nr', '=', 'projects.Projekt_Nr')
            ->join('order_filters', 'projects.Projekt_Nr', '=', 'order_filters.Projekt_Knr')
            ->join('orders', 'order_filters.Knr', '=', 'orders.Knr')
            //->join('orders', 'projects.Projekt_Nr', '=', 'orders.Projekt_Knr')
            ->distinct()
            //->select(DB::raw('projects.*, orders.*'))
            ->select(DB::raw('user_projects.Bs1_ind, user_projects.Bs2_ind, user_projects.Bs5_ind, user_projects.Bs6_ind, user_projects.Bs7_ind, user_projects.Bs8_ind, user_projects.Bs9_ind, projects.*, order_filters.Ab_Ort AS Ab_OrtFilter, order_filters.Ab_Zeit AS Ab_ZeitFilter, order_filters.An_Ort AS An_OrtFilter, order_filters.An_Zeit AS An_ZeitFilter, order_filters.Auftragsdatum AS AuftragsdatumFilter, orders.*'))
            //->select('orders.*')
            ->where('users.id', '=', $pUserId)
            ->whereNull('orders.Auftragsdatum')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('order_cart_items')
                    ->whereRaw('order_cart_items.Knr = orders.Knr');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('bookings')
                    ->whereRaw('bookings.Knr = orders.Knr');
            });

        /*
        if ($oDateFrom != null)
        {
            //$oOrders = $oOrders->whereDate('Auftragsdatum', '>=', $sDateFrom);
            $oOrders = $oOrders->whereDate('order_filters.Auftragsdatum', '>=', $oDateFrom->toDateString());
        }
        */

        if (($pTimeFrom != null) && ($pTimeFrom != ''))
        {
            //$oOrders = $oOrders->whereTime('Ab_Zeit', '>=', $pTimeFrom);
            $oOrders = $oOrders->whereTime('order_filters.Ab_Zeit', '>=', $pTimeFrom);
        }

        if (($pTimeTo != null) && ($pTimeTo != ''))
        {
            //$oOrders = $oOrders->whereTime('An_Zeit', '<=', $pTimeTo);
            $oOrders = $oOrders->whereTime('order_filters.An_Zeit', '<=', $pTimeTo);
        }

        if (($pFrom != null) && ($pFrom != ''))
        {
            //$oOrders = $oOrders->where('Ab_Ort', '=', $pFrom);
            $oOrders = $oOrders->where('order_filters.Ab_Ort', '=', $pFrom);
        }

        if (($pTo != null) && ($pTo != ''))
        {
            //$oOrders = $oOrders->where('An_Ort', '=', $pTo);
            $oOrders = $oOrders->where('order_filters.An_Ort', '=', $pTo);
        }

        if (intval($pProject) > 0)
        {
            $oOrders = $oOrders->where('projects.Projekt_Nr', '=', intval($pProject));
        }

        $oOrders = $oOrders->orderBy('OrdNr', 'asc');

        $oOrders = $oOrders->get();

        return $oOrders;

    }

}
