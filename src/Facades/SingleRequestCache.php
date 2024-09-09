<?php

namespace ErickComp\SingleRequestCache\Facades;

use ErickComp\SingleRequestCache\Providers\SingleRequestCacheServiceProvider;
use ErickComp\SingleRequestCache\SingleRequestCache as SingleRequestCacheClass;
use Illuminate\Support\Facades\Facade;

/**
 * SingleRequestCache: Facade for: @see \ErickComp\SingleRequestCache\SingleRequestCache
 * 
 * @method public static SingleRequestCacheClass put(string $key, mixed $value)
 * @method public static mixed get(string $key, mixed $default = null)
 * @method public static mixed remember(string $key, \Closure $valueResolver)
 * @method public static SingleRequestCacheClass forget(string $key)
 * @method public static SingleRequestCacheClass putWith(string $key, mixed $with, mixed $value)
 * @method public static mixed getWith(string $key, mixed $with, mixed $default = null)
 * @method public static mixed rememberWith(string $key, mixed $with, \Closure $valueResolver)
 * @method public static SingleRequestCacheClass forgetWith(string $key, mixed $with)
 */
class SingleRequestCache extends Facade
{
    /**
     *
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return SingleRequestCacheServiceProvider::FACADE_ACCESSOR;
    }
}
