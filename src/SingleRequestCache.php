<?php

namespace ErickComp\SingleRequestCache;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;

class SingleRequestCache
{
    private Repository $cache;

    public function __construct(ArrayStore $arrayStore, Repository $repository)
    {
        $this->cache = $repository;
        $this->cache->setStore($arrayStore);
    }

    public function put(string $key, mixed $value): static
    {
        $cached = $this->cache->get($key, []);
        $cached['without'] = $value;

        $this->cache->forever($key, $cached);

        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $cached = $this->cache->get($key, []);

        return $cached['without'] ?? $default;
    }

    public function remember(string $key, \Closure $valueResolver): mixed
    {
        $cached = $this->cache->get($key, []);

        if (!\array_key_exists('without', $cached)) {
            $cached['without'] = $valueResolver();
            $this->cache->forever($key, $cached);
        }

        return $cached['without'];
    }

    public function forget(string $key): static
    {
        $cached = $this->cache->get($key, []);
        unset($cached['without']);

        $this->cache->forever($key, $cached);

        return $this;
    }

    /**
     * 
     *
     * @return $this
     */
    public function putWith(string $key, mixed $with, mixed $value): static
    {
        $cached = $this->cache->get($key, []);

        foreach ($cached as $withKey => $withValues) {
            if ($withKey === 'without') {
                continue;
            }

            if ($withValues['with'] === $with) {
                $newCached = $cached;
                $newCached[$withKey]['value'] = $value;

                $this->cache->forever($key, $newCached);

                return $this;
            }
        }

        $cached[$key][] = ['with' => $with, 'value' => $value];

        return $this;
    }

    public function getWith(string $key, mixed $with, mixed $default = null): mixed
    {
        $cached = $this->cache->get($key, []);

        foreach ($cached as $withKey => $withValues) {
            if ($withKey === 'without') {
                continue;
            }

            if ($withValues['with'] === $with) {
                return $withValues['value'];
            }
        }

        return $default;
    }

    public function rememberWith(string $key, mixed $with, \Closure $valueResolver): mixed
    {
        $cached = $this->cache->get($key, []);

        foreach ($cached as $withKey => $withValues) {
            if ($withKey === 'without') {
                continue;
            }

            if ($withValues['with'] === $with) {
                return $withValues['value'];
            }
        }

        $value = $valueResolver();
        $cached[$key][] = ['with' => $with, 'value' => $value];

        return $value;
    }

    public function forgetWith(string $key, mixed $with): static
    {
        $cached = $this->cache->get($key, []);

        foreach ($cached as $withKey => $withValues) {
            if ($withKey === 'without') {
                continue;
            }

            if ($withValues['with'] === $with) {
                $newCached = $cached;
                unset($newCached[$withKey]);

                $this->cache->forever($key, $newCached);

                return $this;
            }
        }

        return $this;
    }
}
