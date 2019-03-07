<?php

namespace App\Http\Controllers;

use App\External\Api\Client;
use App\External\WineSpectator\WineSpectatorAuthenticator;
use Illuminate\Http\Request;

class WineSpectatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function wines()
    {
        $wine_spectator_config                  = config('external.wine-spectator');
        $wine_spectator_config['authenticator'] = new WineSpectatorAuthenticator();

        $client = new Client($wine_spectator_config);

        // Converting Xml to Object
        $fileContents = $client->get($wine_spectator_config['rss'])->getData();
        $fileContents = str_replace(["\n", "\r", "\t"], '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $simpleXmlObj = simplexml_load_string($fileContents);

        return response()->json($simpleXmlObj);
    }
}
