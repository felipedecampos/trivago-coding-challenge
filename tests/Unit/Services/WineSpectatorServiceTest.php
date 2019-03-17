<?php

namespace Tests\Unit\Services;

use App\Repositories\WineSpectatorRepository;
use App\Services\WineSpectatorService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\LogManager;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
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
     * @var LogManager|MockObject
     */
    private $logManager;
    /**
     * @var TestHandler
     */
    private $loggerHandler;

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

        $this->loggerHandler = new TestHandler();
        $this->logManager    = $this->createMock(LogManager::class);

        $this->logManager->method('channel')
            ->willReturn(new Logger('test', [$this->loggerHandler]));

        $this->service = new WineSpectatorService($rssUrl, $this->db, $this->wineSpectatorRepository, $this->logManager);

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

        $this->wineSpectatorRepository->expects(self::exactly(3))
            ->method('put')
            ->withConsecutive(
                [
                    static::identicalTo([
                        'title' => 'CHÂTEAU TEYSSIER St.-Emilion 2016 $13 (Wine Spectator)',
                        'link' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15361',
                        'guid' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15361',
                        'pub_date' => 'Fri, 08 Mar 2019 00:00:00 -0500',
                    ])
                ],
                [
                    static::identicalTo([
                        'title' => 'NIGL Grüner Veltliner Niederösterreich Freiheit 2017 $20 (Wine Spectator)',
                        'link' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15362',
                        'guid' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15362',
                        'pub_date' => 'Fri, 08 Mar 2019 00:00:00 -0500',
                    ]),
                ],
                [
                    static::identicalTo([
                        'title' => 'SPRING MOUNTAIN VINEYARD Elivette Napa Valley 2015 $150 (Wine Spectator)',
                        'link' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15363',
                        'guid' => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/15363',
                        'pub_date' => 'Fri, 08 Mar 2019 00:00:00 -0500',
                    ]),
                ]
            )
            ->willReturn(true);

        $date = new \DateTime('2019-03-08', new \DateTimeZone('-05:00'));

        $this->service->updateWines($date);

        static::assertTrue($this->loggerHandler->hasInfoRecords());
        static::assertFalse($this->loggerHandler->hasErrorRecords());
    }

    /**
     * @covers ::updateWines
     * @throws \Exception
     */
    public function testUpdateWinesFails()
    {
        $this->db->expects(self::once())->method('beginTransaction');
        $this->db->expects(self::once())->method('rollback');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageRegExp('/Could not save the wine/i');

        $this->wineSpectatorRepository->expects(static::once())
            ->method('put')
            ->willReturn(false);

        $this->service->updateWines();

        static::assertTrue($this->loggerHandler->hasErrorRecords());
        static::assertFalse($this->loggerHandler->hasInfoRecords());
    }
}