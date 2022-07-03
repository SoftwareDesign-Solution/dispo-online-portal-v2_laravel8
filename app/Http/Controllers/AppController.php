<?php

/***************************************************************
 * Project: Dispo Online Portal v2
 ***************************************************************
 * \file    AppController.php
 * \brief
 * \author  Manuel Kübler (mail@softwaredesign-solution.de)
 * \version 1.0.0
 ***************************************************************
 * \date    2022-02-23 - Erstellung der Klasse
 ***************************************************************
 * \todo    ...
 * \test    ...
 * \bug     ...
 * \remarks ...
 ***************************************************************/

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{

    public function __invoke()
    {
        return view('app');
    }

}
