<?php

namespace AvaroAI\LaravelDusk;

use Illuminate\Support\Manager;
use AvaroAI\LaravelDusk\Drivers\CookiesDriver;
use AvaroAI\LaravelDusk\Drivers\SessionDriver;

class MockingManager extends Manager
{
    /**
     * Get the default mocking driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $config = $this->container['config'];

        return $config->has('dusk-mocking')
            ? $config['dusk-mocking.driver']
            : 'session';
    }

    /**
     * Create an instance of the Cookies mocking driver.
     *
     * @return \AvaroAI\LaravelDusk\MockingDriver
     */
    protected function createCookiesDriver()
    {
        return new CookiesDriver;
    }

    /**
     * Create an instance of the Session mocking driver.
     *
     * @return \AvaroAI\LaravelDusk\SessionDriver
     */
    protected function createSessionDriver()
    {
        return new SessionDriver;
    }
}
