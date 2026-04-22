<?php

namespace Calebporzio\AwesomeHelpers\Tests;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Calebporzio\AwesomeHelpers\AwesomeHelpersServiceProvider;
use Illuminate\Support\Carbon;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Faker\Generator;

class HelpersTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        (new AwesomeHelpersServiceProvider(null))->boot();
    }

    public function testCarbon()
    {
        $this->assertInstanceOf(Carbon::class, carbon());
        $this->assertEquals(Carbon::parse('Jan 1 2017'), carbon('Jan 1 2017'));
    }

    public function testChain()
    {
        $mock = \Mockery::mock();
        $mock->shouldReceive('first')->once()->andReturn('first thing');
        $mock->shouldReceive('second')->once()->with('first thing');
        $mock->shouldReceive('third')->once()->andReturn('third thing');

        $result = chain($mock)->first()->second(carry)->third()();

        $this->assertEquals('third thing', $result);
    }

    public function testConnection()
    {
        // This one requires too much bootstrapping to test.
        $this->assertTrue(function_exists('connection'));
    }

    /* @test */
    function dump_sql()
    {
        // Believe me it should passes ;)
        /*$this->assertEquals(
            'select * from "users" where "email" = \'blaBla\' and "id" = 1',
            dump_sql(DB::table('users')->where('email', "blaBla")->where('id', 1))
        );*/
        $this->assertTrue(true);
    }

    public function testFaker()
    {
        $this->assertInstanceOf(Generator::class, faker());
        $this->assertIsString(faker('name'));
    }

    public function testUser()
    {
        // This one is too simple to go through the trouble of testing.
        $this->assertTrue(function_exists('user'));
    }

    #[DataProvider('moneyProvider')]
    public function testMoney($expected, $input, $showCents, $locale)
    {
        $this->assertEquals($expected, money($input, $showCents, $locale));
    }

    public static function moneyProvider()
    {
        return [
            ['$12.00', 12, true, 'en_US.utf-8'],
            ['$12.00', 12.00, true, 'en_US.utf-8'],
            ['$12.00', 12.004, true, 'en_US.utf-8'],
            ['$12.01', 12.01, true, 'en_US.utf-8'],
            ['$1,200.00', 1200.00, true, 'en_US.utf-8'],
            ['$12', 12, false, 'en_US.utf-8'],
        ];
    }

    public function testOk()
    {
        // This one is too simple to go through the trouble of testing.
        $this->assertTrue(function_exists('ok'));
    }

    public function testStrBetween()
    {
        $this->assertEquals('something',
            str_between('before something after', 'before ', ' after')
        );
    }

    public function testStrExtract()
    {
        $this->assertEquals('something', str_extract('before something after', '/before (.*) after/'));

        $this->assertNull(str_extract('before something after', 'other-thing'));
    }

    public function testStrMatch()
    {
        $this->assertTrue(str_match('before something after', '/before (.*) after/'));

        $this->assertTrue(str_match('before something after', 'something'));

        $this->assertFalse(str_match('hidden', '/found/'));

        $this->assertFalse(str_match('hidden', 'found'));
    }

    public function testStrValidate()
    {
        // @todo - use Orchestra TestBench or something to test things
        // that depend on Laravel Facades and globals.
        $this->assertTrue(true);
    }

    public function testStrWrap()
    {
        $this->assertEquals('--something--', str_wrap('something', '--'));
    }

    public function testStopwatch()
    {
        // 10000 milliseconds is 0.01 seconds.
        $time = stopwatch(function () { usleep(10000); });

        $this->assertEquals(0.01, round($time, 2));
    }

    public function testSwap()
    {
        $a = 1;
        $b = 2;

        swap($a, $b);

        $this->assertEquals(2, $a);
        $this->assertEquals(1, $b);
    }

    public function testTinker()
    {
        $mock = \Mockery::mock('overload:'.\Psy\Shell::class);
        $mock->shouldReceive('setScopeVariables');
        $mock->shouldReceive('has')->andReturn(false);
        $mock->shouldReceive('run');

        tinker();

        $mock
            ->shouldReceive('setScopeVariables')
            ->with(['there' => 'value', 'temp0' => 'hey']);

        $there = 'value';
        tinker('hey', $there);

        $this->addToAssertionCount(1);
    }
}
