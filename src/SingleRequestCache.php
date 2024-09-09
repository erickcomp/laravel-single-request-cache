<?php

namespace ErickComp\SingleRequestCache;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;

class SingleRequestCache
{
    public const HASH_ALGO = 'sha256';
    private Repository $cache;

    public function __construct(ArrayStore $arrayStore, Repository $repository)
    {
        $this->cache = $repository;
        $this->cache->setStore($arrayStore);
    }

    public function put(string $key, mixed $value): static
    {
        $this->cache->forever($key, $value);

        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    public function remember(string $key, \Closure $valueResolver): mixed
    {
        if (!$this->has($key)) {
            $value = $valueResolver();
            $this->put($key, $value);

            return $value;
        }

        return $this->get($key);
    }

    public function forget(string $key): static
    {
        $this->cache->forget($key);

        return $this;
    }

    public function putWith(string $key, mixed $with, mixed $value): static
    {
        return $this->put($this->keyFromKeyWithData($key, $with), $value);
    }

    public function getWith(string $key, mixed $with, mixed $default = null): mixed
    {
        return $this->get($this->keyFromKeyWithData($key, $with), $default);
    }

    public function hasWith(string $key, mixed $with): bool
    {
        return $this->has($this->keyFromKeyWithData($key, $with));
    }

    public function rememberWith(string $key, mixed $with, \Closure $valueResolver): mixed
    {
        return $this->remember($this->keyFromKeyWithData($key, $with), $valueResolver);
    }

    public function forgetWith(string $key, mixed $with): static
    {
        return $this->forget($this->keyFromKeyWithData($key, $with));
    }

    public function flush(): void
    {
        $this->cache->flush();
    }

    protected function keyFromKeyWithData(string $key, mixed $with): string
    {
        return \hash(
            static::HASH_ALGO,
            \serialize([
                'key' => $key,
                'with' => $with
            ])
        );
    }
}
