<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TokenController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Request as RequestGuzzle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Exception;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Display a screen login.
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function autentication(Request $request)
    {
        Log::debug("INICIO");

        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $head = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        $bodyRequest = $request->only(['email', 'password']);
        $body = json_encode($bodyRequest);

        $credentials = [];
        try {
            $client = new Client();
            $requestUser = new RequestGuzzle('POST', env('END_POINT_BACKEND') . '/api/login', $head, $body);
            $promiseCredentials = $client->sendAsync($requestUser)->then(function ($response) {
                return json_decode($response->getBody()->getContents(),true);
            });
            $credentials = $promiseCredentials->wait();

            Log::debug("OPA");

            dd($credentials);

            $tokenController = new TokenController;
            $tokenController->setCredentials($credentials);
            $expires_in = Carbon::now()->addSeconds(($credentials['expires_in'] - env('SESSION_EXPIRE')))->format('Y-m-d H:i:s');
            Session::put('expires_in', $expires_in);
            Log::debug("PASSOU AQUI");
            return redirect()->route('home');

        } catch (Exception $e) {
            Log::debug("ERRO: ".$e);
            Session::put('fail', 'Email ou senha incorreto');
            return view('auth.login', ['credential' => $credentials]);
        }

    }
}
