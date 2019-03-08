<?php

namespace Tests\Unit\Repositories;

use App\Models\Wine;
use App\Repositories\WineSpectatorRepository;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Generator as Faker;

/**
 * @coversDefaultClass \App\Repositories\WineSpectatorRepository
 */
class WineSpectatorRepositoryTest extends TestCase
{

    // @codeCoverageIgnoreStart
    public $wine;

    /**
     * @param int $rows
     */
    private function mockWines($rows = 10) {
        $this->wine = Mockery::mock(Wine::class);

        /** @var Faker $faker */
        $faker = new Faker();

        for ($i = 1; $i <= $rows; $i++) {
            $url = 'https://www.winespectator.com/dailypicks/show/date/'
                . $faker->dateTime(null, '-05:00')
                . '/dwpid/'
                . $faker->randomNumber(5);

            $this->wines->fill([
                'variety'    => $faker->word,
                'region'     => $faker->word,
                'year'       => $faker->year,
                'price'      => $faker->randomFloat(3),
                'link'       => $url,
                'pub_date'   => $faker->dateTime(null, '-05:00'),
                'created_at' => $faker->dateTime('', '-05:00'),
                'updated_at' => $faker->dateTime('now', '-05:00'),
                'deleted_at' => null
            ]);

            $this->wines->setAttibute('guid', $url);

            $this->wines->save();
        }
    }
    // @codeCoverageIgnoreEnd

    /**
     * @covers ::getAll
     */
    public function testGetAll()
    {
        $this->mockWines(5);

        $mockRepository = Mockery::mock(WineSpectatorRepository::class);
        $mockRepository->shouldReceive('getAll')
            ->andReturn($this->wine);

        $this->assertTrue(true);
    }

    /**
     * @covers ::find
     */
    public function testFind()
    {
        $this->mockWines(5);

        $wine = current($this->wine);

        $mockRepository = Mockery::mock(WineSpectatorRepository::class);
        $mockRepository->shouldReceive('find')->with($wine['guid'])->andReturn($wine);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->mockWines(5);

        $wine1 = current($this->wine);
        $wine1->variety  = 'Mendoza';
        $wine1->region   = 'Argento';
        $wine1->year     = 2013;
        $wine1->price    = 125;
        $wine1->link     = 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/00001';
        $wine1->pub_date = 'Fri, 08 Mar 2019 00:00:00 -0500';

        $mockRepository = Mockery::mock(WineSpectatorRepository::class);
        $mockRepository->shouldReceive('put')->with($wine1)->andReturn(true);

        $wine2 = [
            'guid'     => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/00001',
            'variety'  => 'Mendoza',
            'region'   => 'Argento',
            'year'     => 2013,
            'price'    => 125,
            'link'     => 'https://www.winespectator.com/dailypicks/show/date/2019-03-08/dwpid/00001',
            'pub_date' => 'Fri, 08 Mar 2019 00:00:00 -0500'
        ];

        $mockRepository->shouldReceive('put')->with($wine2)->andReturn(true);
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $this->mockWines(5);

        $wine = current($this->wine);

        $mockRepository = Mockery::mock(WineSpectatorRepository::class);
        $mockRepository->shouldReceive('delete')->with($wine['guid'])->andReturn(true);
    }
}
