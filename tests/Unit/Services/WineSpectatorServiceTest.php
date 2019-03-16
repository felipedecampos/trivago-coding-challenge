<?php

namespace Tests\Unit\Services;

use App\Repositories\WineSpectatorRepository;
use App\Services\WineSpectatorService;
use Illuminate\Database\DatabaseManager;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @coversDefaultClass \App\Services\WineSpectatorService
 */
class WineSpectatorServiceTest extends TestCase
{
    /**
     * @var DatabaseManager|MockObject
     */
    private $db;

    /**
     * @var WineSpectatorRepository|MockObject
     */
    private $wineSpectatorRepository;

    /**
     * @var WineSpectatorService
     */
    private $service;

    public function setUp()
    {
        $rssUrl = TEST_FILES_PATH . 'rss.xml';

        $this->db = $this->createPartialMock(
            DatabaseManager::class,
            ['beginTransaction', 'commit', 'rollback']
        );

        $this->wineSpectatorRepository = $this->createMock(WineSpectatorRepository::class);

        $this->service = new WineSpectatorService($rssUrl, $this->db, $this->wineSpectatorRepository);

        parent::setUp();
    }

    /**
     * @covers ::updateWines
     * @throws \Exception
     */
    public function testUpdateWinesSucceeds()
    {
        $this->db->expects(self::once())->method('beginTransaction');
        $this->db->expects(self::once())->method('commit');
        $this->db->expects(self::never())->method('rollback');

        $this->wineSpectatorRepository->expects(self::exactly(3))
            ->method('put')
            ->withConsecutive(
                [
                    [
                        'guid'     => 'https://www.winespectator.com/dailypicks/show/date/2019-03-16/dwpid/15382',
                        'link'     => 'https://www.winespectator.com/dailypicks/show/date/2019-03-16/dwpid/15382',
                        'title'    => 'CHARLES SMITH Rosé Band of Roses Washington 2017 $13 (Wine Spectator)',
                        'pub_date' => 'Sat, 16 Mar 2019 00:00:00 -0400',
                    ]
                ],
                [
                    [
                        'guid'     => 'https://www.winespectator.com/dailypicks/show/date/2019-03-16/dwpid/15383',
                        'link'     => 'https://www.winespectator.com/dailypicks/show/date/2019-03-16/dwpid/15383',
                        'title'    => 'DUCK HUNTER Pinot Noir Marlborough 2018 $30 (Wine Spectator)',
                        'pub_date' => 'Sat, 16 Mar 2019 00:00:00 -0400',
                    ],
                ],
                [
                    [
                        'guid'     => 'https://www.winespectator.com/dailypicks/show/date/2019-03-16/dwpid/15384',
                        'link'     => 'https://www.winespectator.com/dailypicks/show/date/2019-03-16/dwpid/15384',
                        'title'    => 'DOW Tawny Port 20 Year Old NV $65 (Wine Spectator)',
                        'pub_date' => 'Sat, 16 Mar 2019 00:00:00 -0400',
                    ],
                ]
            )
            ->willReturn(true);

        $date = new \DateTime('2019-03-16', new \DateTimeZone('+00:00'));

        $this->service->updateWines($date);
    }

    /**
     * @covers ::updateWines
     * @throws \Exception
     */
    public function testUpdateWinesFails()
    {
        $this->db->expects(self::once())->method('beginTransaction');
        $this->db->expects(self::never())->method('commit');
        $this->db->expects(self::once())->method('rollback');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageRegExp('/Could not save the wine/i');

        $this->wineSpectatorRepository->expects(static::once())
            ->method('put')
            ->willReturn(false);

        $this->service->updateWines();
    }
}