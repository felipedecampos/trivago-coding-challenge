<?php

namespace App\Services;

use App\External\Api\Client;
use App\External\WineSpectator\WineSpectatorAuthenticator;
use App\Models\Wine;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WineSpectatorService
{
    /**
     * @param string $day
     * @return bool
     * @throws \Exception
     */
    public function updateWines(string $day = 'today')
    {
        try {

            $today = $day === 'all'
                ? null
                : new \DateTime('today', new \DateTimeZone('-05:00'));

            $wines = $this->getWines($today);

            DB::beginTransaction();

            foreach ($wines as $wine) {
                $model = Wine::find($wine['guid']);

                $wineModel = is_null($model)
                    ? new Wine()
                    : $model;

                $wineModel->fill($wine);

                if (is_null($model)) {
                    $wineModel->setAttribute('guid', $wine['guid']);
                }

                if (true !== $wineModel->save()) {
                    $exceptionMessage = sprintf(
                        "Could not possible save the wine: %s",
                        print_r($wine, true)
                    );
                    throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
                }
            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            return '(' . $e->getCode() . ') - ' . $e->getMessage();

        }

        return true;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \DateTime|null $date
     * @return array
     */
    private function getWines(\DateTime $date = null): array
    {
        $wines = $this->watch($date);

        if (! count($wines)) {
            return $wines;
        }

        foreach ($wines as &$wine) {
            $wine += $this->parseTitle($wine['title']);
            $wine ['pub_date']= $wine['pubDate'];

            unset($wine['title'], $wine['description'], $wine['author'], $wine['category'], $wine['id'], $wine['pubDate']);
        }

        return $wines;
    }

    /**
     * @param \DateTime|null $pubDateFilter
     * @return array
     */
    private function watch(\DateTime $pubDateFilter = null): array
    {
        $config = config('external.wine-spectator');

        $content = file_get_contents($config['rss']);
        $xmlObj  = new \SimpleXmlElement($content);
        $objArr  = json_decode(json_encode($xmlObj), true);

        $wines = $objArr['channel']['item'] ?? [];

        if (count($wines) && $pubDateFilter instanceof \DateTime) {
            $wines = array_filter($objArr['channel']['item'], function ($wine) use ($pubDateFilter) {
                $pubDate = new \DateTime($wine['pubDate']);

                return $pubDateFilter == $pubDate;
            });
        }

        return $wines;
    }

    /**
     * @param string $title
     * @return array
     */
    private function parseTitle(string $title): array
    {
        $exprVariety = '([[:upper:]]{2,})';
        $exprRegion  = '\b(?!Spectator|Wine)\b([A-Z]{0,1}[a-z]+)';
        $exprYear    = '(\d{4,4})';
        $exprPrice   = '(\$\d+\.\d+|\$\d+)';
        $exprAll     = "/$exprVariety|$exprRegion|$exprYear|$exprPrice/";
        preg_match_all($exprAll, $title, $matches);

        unset($exprVariety, $exprRegion, $exprYear, $exprPrice, $exprAll, $matches[0]);

        $parsed = [];
        $keys   = [null, 'variety', 'region', 'year', 'price'];

        foreach ($matches as $key => $match) {
            $parsed[$keys[$key]] = implode(' ', array_filter($match));

            if ($key === 4) {
                $parsed[$keys[$key]] = (double) str_replace('$', '', $parsed[$keys[$key]]);
            }

            unset($matches[$key]);
        }

        unset($keys);

        return $parsed;
    }
}
