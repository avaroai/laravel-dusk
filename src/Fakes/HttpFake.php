<?php

declare(strict_types=1);

namespace AvaroAI\LaravelDusk\Fakes;

use Illuminate\Support\Collection;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\ResponseSequence;

final class HttpFake extends Factory
{
    protected $config = [];

    public function __construct(mixed $config = null)
    {
        $config       = json_decode(json_encode($config), true);
        $this->config = is_array($config) ? $config : [];

        parent::__construct(null);

        $this->configure();
    }

    public function __sleep() : array
    {
        return [
            'recording',
            // 'recorded',
            'preventStrayRequests',
            'allowedStrayRequestUrls',
            'config',
        ];
    }

    public function __wakeup() : void
    {
        $this->stubCallbacks = Collection::make();

        $this->configure();
    }

    protected function configure() : void
    {
        if ($this->config === []) {
            return;
        }

        if (! array_is_list($this->config)) {
            parent::fake($this->config);

            return;
        }

        $sequence = $this->fakeSequence();

        Collection::make($this->config)->each(
            fn(mixed $response) : ResponseSequence => $sequence->push($response),
        );
    }
}
