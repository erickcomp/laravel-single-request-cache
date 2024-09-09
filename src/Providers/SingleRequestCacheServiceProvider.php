<?php

namespace ErickComp\SingleRequestCache\Providers;

use ErickComp\SingleRequestCache\SingleRequestCache;
use Illuminate\Support\ServiceProvider;

class SingleRequestCacheServiceProvider extends ServiceProvider
{
    public const FACADE_ACCESSOR = 'erickcomp-single-request-cache-facade';

    public function register()
    {
        $this->app->singleton(self::FACADE_ACCESSOR, fn() => $this->app->make(SingleRequestCache::class));
    }

    public function boot()
    {

    }
}
