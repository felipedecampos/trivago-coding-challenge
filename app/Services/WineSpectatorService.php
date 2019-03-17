<?php

namespace App\Services;

use App\Repositories\WineSpectatorRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Response;
use Illuminate\Log\LogManager;

class WineSpectatorService
{
    /**
     * @var string
     */
    protected $rssUrl;

    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @var WineSpectatorRepository
     */
    public $wineSpectatorRepository;

    /**
     * @var LogManager
     */
    protected $logManager;

    public function __construct(
        string $rssUrl,
        DatabaseManager $db,
        WineSpectatorRepository $wineSpectatorRepository,
        LogManager $logManager
    ) {
        $this->rssUrl                  = $rssUrl;
        $this->db                      = $db;
        $this->wineSpectatorRepository = $wineSpectatorRepository;
        $this->logManager              = $logManager;
    }

    /**
     * @param \DateTime|null $dateTime
     * @return bool
     * @throws \Exception
     */
    public function updateWines(\DateTime $dateTime = null)
    {
        try {
            $wines = $this->fetchWines($dateTime);

            $this->db->beginTransaction();

            foreach ($wines as $wine) {
                $status = $this->wineSpectatorRepository->put($wine);

                if (true !== $status) {
                    $exceptionMessage = sprintf(
                        'Could not save the wine: %s',
                        print_r($wine, true)
                    );

                    $this->logManager->channel('application')->error($exceptionMessage);

                    throw new \Exception($exceptionMessage, Response::HTTP_EXPECTATION_FAILED);
                }
            }

            $this->logManager->channel('application')->info(
                'Wines were successfully updated.'
            );

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();

            throw $e;
        }

        return true;
    }

    /**
     * Fetches the rss feed and parses them to wine objects
     *
     * @param \DateTime|null $date
     * @return array
     */
    private function fetchWines(\DateTime $date = null): array
    {
        $wines = $this->fetchWineList($date);

        if (! count($wines)) {
            return $wines;
        }

        $columns = ['guid', 'title', 'link', 'pub_date'];

        foreach ($wines as &$wine) {
            $wine['pub_date'] = $wine['pubDate'];
            $wine = array_intersect_key($wine, array_flip($columns));
        }

        return $wines;
    }

    /**
     * @param \DateTime|null $pubDateFilter
     * @return array
     */
    private function fetchWineList(\DateTime $pubDateFilter = null): array
    {
        $content = file_get_contents($this->rssUrl);
        $xmlObj  = new \SimpleXmlElement($content);
        $objArr  = json_decode(json_encode($xmlObj), true);
        $wines   = $objArr['channel']['item'] ?? [];

        if ($pubDateFilter !== null && count($wines)) {
            $wines = array_filter($wines, function ($wine) use ($pubDateFilter) {
                $pubDate = new \DateTime($wine['pubDate']);

                return $pubDateFilter->format('Y-m-d') == $pubDate->format('Y-m-d');
            });
        }

        return $wines;
    }

    public function getAll()
    {
        return $this->wineSpectatorRepository->getAll();
    }
}
