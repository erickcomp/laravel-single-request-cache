<?php

namespace ErickComp\SingleRequestCache\Tests;

use ErickComp\SingleRequestCache\Facades\SingleRequestCache;
use ErickComp\SingleRequestCache\Providers\SingleRequestCacheServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class Test extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $provider = new SingleRequestCacheServiceProvider(app());
        $provider->register();
        $provider->boot();
    }

    /** @test */
    public function t01_it_can_erase_all_items_from_cache()
    {
        SingleRequestCache::flush();

        SingleRequestCache::put('key1', 'value1');
        SingleRequestCache::put('key2', 'value2');
        SingleRequestCache::put('key3', 'value3');

        $this->assertEquals(SingleRequestCache::get('key1'), 'value1');
        $this->assertEquals(SingleRequestCache::get('key1'), 'value1');
        $this->assertEquals(SingleRequestCache::get('key1'), 'value1');

        SingleRequestCache::flush();

        $this->assertFalse(SingleRequestCache::has('key1'));
        $this->assertFalse(SingleRequestCache::has('key2'));
        $this->assertFalse(SingleRequestCache::has('key3'));
    }

    /** @test */
    public function t02_it_can_cache_and_retrieve_a_simple_value()
    {
        SingleRequestCache::flush();

        SingleRequestCache::put('key1', 'value1');

        $this->assertEquals(
            'value1',
            SingleRequestCache::get('key1')
        );
    }

    /** @test */
    public function t03_it_can_check_if_an_item_exists_in_cache()
    {
        SingleRequestCache::flush();

        SingleRequestCache::put('key1', 'value1');

        $this->assertTrue(SingleRequestCache::has('key1'));
        $this->assertFalse(SingleRequestCache::has('non-existent-key'));
    }

    /** @test */
    public function t04_it_can_remember_an_item()
    {
        SingleRequestCache::flush();

        $value = SingleRequestCache::remember('key1', fn() => 'remember-value-001');

        $this->assertEquals($value, 'remember-value-001');
        $this->assertTrue(SingleRequestCache::has('key1'));
        $this->assertEquals(SingleRequestCache::get('key1'), $value);
    }

    /** @test */
    public function t05_it_can_cache_and_retrieve_a_value_with_data()
    {
        SingleRequestCache::flush();

        SingleRequestCache::putWith('key1', ['a' => 1, 'b' => 2], 'value1');

        $this->assertEquals(
            'value1',
            SingleRequestCache::getWith('key1', ['a' => 1, 'b' => 2])
        );
    }

    /** @test */
    public function t06_it_can_check_if_an_item_with_data_exists_in_cache()
    {
        SingleRequestCache::flush();

        SingleRequestCache::putWith('key1', ['a' => 1, 'b' => 2], 'value1');

        $this->assertTrue(SingleRequestCache::hasWith('key1', ['a' => 1, 'b' => 2]));
        $this->assertFalse(SingleRequestCache::has('non-existent-key', ['a' => 1, 'b' => 2]));
    }

    /** @test */
    public function t07_it_can_remember_an_item_with_data()
    {
        SingleRequestCache::flush();

        $value = SingleRequestCache::rememberWith('key1', ['a' => 1, 'b' => 2], fn() => 'remember-value-001');

        $this->assertEquals($value, 'remember-value-001');
        $this->assertTrue(SingleRequestCache::hasWith('key1', ['a' => 1, 'b' => 2]));
        $this->assertEquals(SingleRequestCache::getWith('key1', ['a' => 1, 'b' => 2]), $value);
    }

    public function t01_it_can_generate_correct_key_from_key_plus_with_data ()
    {
        keyFromKeyWithData(string $key, mixed $with): string

    }
}
