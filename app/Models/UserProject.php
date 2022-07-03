<?php

/***************************************************************
 * Project: TRENDline Dispo Online
 ***************************************************************
 * \file    UserProject.php
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


use Illuminate\Database\Eloquent\Model;


class UserProject extends Model
{

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
	public function Project()
	{
		return $this->hasOne('App\Models\Project', 'Projekt_Nr', 'Projekt_Nr');
	}

	public function Orders()
	{
		return $this->hasMany('App\Models\Order', 'Projekt_Knr', 'Projekt_Nr');
	}

    public function OrderFilters()
    {
        return $this->hasMany('App\Models\OrderFilter', 'Projekt_Knr', 'Projekt_Nr')->with('Order');
    }

	public function OpenOrders($pDate)
	{

		return $this->hasMany('App\Models\Order', 'Projekt_Knr', 'Projekt_Nr')
			->whereDate('Auftragsdatum', '>=', $pDate)
			->get();

	}

	public function PastOrders($pDate)
	{

		return $this->hasMany('App\Models\Order', 'Projekt_Knr', 'Projekt_Nr')
			->whereDate('Auftragsdatum', '<=', $pDate)
			->get();

	}

	public function ShiftDayOrders()
	{

		return $this->hasMany('App\Models\Order', 'Projekt_Knr', 'Projekt_Nr')
			->whereNull('Auftragsdatum')
			->get();

	}

	public function User()
	{
		return $this->hasOne('App\Models\User', 'knr', 'Ma_Knr');
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
	//

}
