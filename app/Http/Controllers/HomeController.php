<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Log\LogManager;

class HomeController extends Controller
{
    /**
     * @var LogManager
     */
    protected $logManager;

    /**
     * Create a new controller instance.
     *
     * @param LogManager $logManager
     */
    public function __construct(LogManager $logManager)
    {
        $this->logManager = $logManager;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->logManager->channel('application')->info(
            'The customer enters in the home application.',
            auth()->check() ? [auth()->user()->getAuthIdentifierName() => auth()->user()->getAuthIdentifier()] : []
        );

        return view('welcome');
    }
}
