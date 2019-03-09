<?php

namespace Tests\Unit\Services;

use App\Repositories\WineSpectatorRepository;
use App\Services\WineSpectatorService;
use Illuminate\Database\DatabaseManager;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @coversDefaultClass \App\Services\WineSpectatorService
 */
class WineSpectatorServiceTest extends TestCase
{
    /**
     * @var DatabaseManager|MockInterface
     */
    private $db;

    /**
     * @var WineSpectatorRepository|MockInterface
     */
    private $wineSpectatorRepository;

    /**
     * @var WineSpectatorService
     */
    private $service;

    public function setUp()
    {
        $rssUrl = TEST_FILES_PATH . 'rss.xml';

        $this->db = Mockery::mock(DatabaseManager::class);

        $this->wineSpectatorRepository = Mockery::mock(WineSpectatorRepository::class);

        $this->service = new WineSpectatorService($rssUrl, $this->db, $this->wineSpectatorRepository);

        parent::setUp();
    }

    /**
     * @covers ::updateWines
     * @throws \Exception
     */
    public function testUpdateWinesSucceeds()
    {
        $this->db->shouldReceive('beginTransaction');
        $this->db->shouldReceive('commit');

        $this->wineSpectatorRepository->shouldReceive('put')->once()
            ->with([
                'link' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15361',
                'guid' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15361',
                'variety' => 'CH TEAU TEYSSIER',
                'region' => 'St Emilion',
                'year' => '2016',
                'price' => 13.0,
                'pub_date' => 'Fri, 08 Mar 2019 00:00:00 -0500',
            ])
            ->andReturn(true);

        $this->wineSpectatorRepository->shouldReceive('put')->once()
            ->with([
                'link' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15362',
                'guid' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15362',
                'variety' => 'NIGL',
                'region' => 'Gr ner Veltliner Nieder sterreich Freiheit',
                'year' => '2017',
                'price' => 20.0,
                'pub_date' => 'Fri, 08 Mar 2019 00:00:00 -0500',
            ])
            ->andReturn(true);

        $this->wineSpectatorRepository->shouldReceive('put')->once()
            ->with([
                'link' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15363',
                'guid' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15363',
                'variety' => 'SPRING MOUNTAIN VINEYARD',
                'region' => 'Elivette Napa Valley',
                'year' => '2015',
                'price' => 150.0,
                'pub_date' => 'Fri, 08 Mar 2019 00:00:00 -0500',
            ])
            ->andReturn(true);

        $date = new \DateTime('2019-03-08', new \DateTimeZone('-05:00'));

        $this->service->updateWines($date);
    }

    /**
     * @covers ::updateWines
     * @throws \Exception
     */
    public function testUpdateWinesFails()
    {
        $this->db->shouldReceive('beginTransaction');

        $this->db->shouldNotReceive('commit');

        $this->db->shouldReceive('rollback');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageRegExp('/Could not save the wine/i');

        $this->wineSpectatorRepository->shouldReceive('put')
            ->andReturn(false);

        $this->service->updateWines();
    }

    /**
     * @covers ::getWines
     */
    public function testGetWines()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers ::watch
     */
    public function testWatch()
    {
        $this->assertTrue(true);
    }

    /**
     * @covers ::parseTitle
     */
    public function testParseTitle()
    {
        $this->assertTrue(true);
    }
}
