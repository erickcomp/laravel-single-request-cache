<?php

namespace ErickComp\SingleRequestCache\Tests;

use ErickComp\SingleRequestCache\Facades\SingleRequestCache;
use ErickComp\SingleRequestCache\Providers\SingleRequestCacheServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class CacheIsErasedBetweenRequestsTest extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $provider = new SingleRequestCacheServiceProvider(app());
        $provider->register();
        $provider->boot();

    }

    /** @test */
    public function it_can_cache_a_value_during_a_request()
    {
        Route::getRoutes()->add(
            Route::get('/erickcomp-single-request-cache-test-001/{cachekey}/{cacheval}', function (string $cachekey, string $cacheval) {
                SingleRequestCache::put($cachekey, $cacheval);
                $cachedVal = SingleRequestCache::get($cachekey);

                return response()->json([$cachekey => $cachedVal]);
            })
        );

        $cacheKey = Str::slug(Str::random(10));
        $cacheVal = Str::slug(Str::random(20));
        $response = $this->get("/erickcomp-single-request-cache-test-001/$cacheKey/$cacheVal");

        $response->assertExactJson([
            $cacheKey => $cacheVal
        ]);
    }

    /** @test */
    public function it_can_erase_a_cache_value_during_a_request()
    {
        Route::getRoutes()->add(
            Route::get('/erickcomp-single-request-cache-test-002/{cachekey}/{cacheval}', function (string $cachekey, string $cacheval) {
                SingleRequestCache::put($cachekey, $cacheval);
                SingleRequestCache::forget($cachekey);
                $cachedVal = SingleRequestCache::get($cachekey, null);

                return response()->json([$cachekey => $cachedVal]);
            })
        );

        $cacheKey = Str::slug(Str::random(10));
        $cacheVal = Str::slug(Str::random(20));
        $response = $this->get("/erickcomp-single-request-cache-test-002/$cacheKey/$cacheVal");

        $response->assertExactJson([
            $cacheKey => null
        ]);
    }

    /** @test */
    public function it_does_not_persist_cache_between_requests()
    {
        Route::getRoutes()->add(
            Route::get('/erickcomp-single-request-cache-test-003-1/{cachekey}/{cacheval}', function (string $cachekey, string $cacheval) {
                SingleRequestCache::put($cachekey, $cacheval);
                $cachedVal = SingleRequestCache::get($cachekey, null);

                return response()->json([$cachekey => $cachedVal]);
            })
        );

        Route::getRoutes()->add(
            Route::get('/erickcomp-single-request-cache-test-003-2/{cachekey}/{defaultval}', function (string $cachekey, string $defaultval) {
                $cachedVal = SingleRequestCache::get($cachekey, $defaultval);

                return response()->json([$cachekey => $cachedVal]);
            })
        );

        $cacheKey = Str::slug(Str::random(10));
        $cacheVal = Str::slug(Str::random(20));
        $defaultVal = Str::slug(Str::random(20));

        $response1 = $this->get("/erickcomp-single-request-cache-test-003-1/$cacheKey/$cacheVal");
        $response1->assertExactJson([
            $cacheKey => $cacheVal
        ]);

        $response2 = $this->get("/erickcomp-single-request-cache-test-003-2/$cacheKey/$defaultVal");
        $response2->assertExactJson([
            $cacheKey => $defaultVal
        ]);
    }

    protected function registerTestRoutes()
    {

    }
}
