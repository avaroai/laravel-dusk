<?php

namespace AvaroAI\LaravelDusk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AvaroAI\LaravelDusk\MockingManager
 * @see \AvaroAI\LaravelDusk\Driver
 * @method static void registerFake(string $facade, string $fake)
 */
class Mocking extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dusk-mocking';
    }
}
