<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        if (Auth::attempt(['idnr' => $request->idnr, 'password' => $request->password])) {
            $auth = Auth::user();
            $accessToken = $auth->createToken('LaravelSanctumAuth')->plainTextToken;
            //$success['name'] = $auth->name;

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'name' => $auth->vorname . ' ' . $auth->nachname,
                'authenticated' => true,
            ]);

            return 'User logged-in!';
        } else {
            return response()->json([
                'authenticated' => false,
            ]);
        }
    }

    public function getAccessToken(Request $request)
    {

        $request->validate([
            'idnr' => 'required|string',
            'password' => 'required|string'
        ]);

        $credentials = array(
            'idnr' => $request->input('idnr'),
            'password' => $request->input('password'),
            'Api' => 1
        );

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $auth = Auth::user();
        $accessToken = $auth->createToken('LaravelSanctumAuth')->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer'
        ]);

    }

    public function logout(Request $request) {

        print_r($request->user());

        /*
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
        */

    }

}
