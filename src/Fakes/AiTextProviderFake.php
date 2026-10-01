<?php

declare(strict_types=1);

namespace AvaroAI\LaravelDusk\Fakes;

use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Gateway\TextGenerationLoop;
use Laravel\Ai\Contracts\Providers\TextProvider;
use Laravel\Ai\Contracts\Gateway\StepTextGateway;
use Laravel\Ai\Responses\StreamableAgentResponse;

final class AiTextProviderFake implements TextProvider
{
    /**
     * Create a transport-safe provider around the configured fake gateway.
     *
     */
    public function __construct(
        private TextProvider $provider,
        private AiFake $fake,
        private string $agent,
    ) {}

    /**
     * Retrieve provider configuration beyond its standard connection values.
     *
     */
    public function additionalConfiguration() : array
    {
        return $this->provider->additionalConfiguration();
    }

    /**
     * Retrieve the provider's lowest-cost text model.
     *
     */
    public function cheapestTextModel() : string
    {
        return $this->provider->cheapestTextModel();
    }

    /**
     * Retrieve the provider's default text model.
     *
     */
    public function defaultTextModel() : string
    {
        return $this->provider->defaultTextModel();
    }

    /**
     * Retrieve the underlying provider driver.
     *
     */
    public function driver() : string
    {
        return $this->provider->driver();
    }

    /**
     * Retrieve the configured provider name.
     *
     */
    public function name() : string
    {
        return $this->provider->name();
    }

    /**
     * Run a prompt through the retained fake gateway.
     *
     */
    public function prompt(AgentPrompt $prompt) : AgentResponse
    {
        $this->fake->recordPrompt($prompt);

        return $this->fake->withoutAgentFake(
            $this->agent,
            fn() : AgentResponse => $this->provider->prompt($prompt),
        );
    }

    /**
     * Retrieve the provider's credentials.
     *
     */
    public function providerCredentials() : array
    {
        return $this->provider->providerCredentials();
    }

    /**
     * Retrieve the provider's highest-capability text model.
     *
     */
    public function smartestTextModel() : string
    {
        return $this->provider->smartestTextModel();
    }

    /**
     * Stream a prompt through the retained fake gateway.
     *
     */
    public function stream(AgentPrompt $prompt) : StreamableAgentResponse
    {
        $this->fake->recordPrompt($prompt);

        return $this->fake->withoutAgentFake(
            $this->agent,
            fn() : StreamableAgentResponse => $this->provider->stream($prompt),
        );
    }

    /**
     * Retrieve the provider's text generation loop.
     *
     */
    public function textGenerationLoop() : TextGenerationLoop
    {
        return $this->provider->textGenerationLoop();
    }

    /**
     * Replace the provider's text gateway.
     *
     */
    public function useTextGateway(StepTextGateway $gateway) : self
    {
        $this->provider->useTextGateway($gateway);

        return $this;
    }

    /**
     * Retrieve a provider that sends the given headers with each request.
     *
     */
    public function withHeaders(array $headers) : static
    {
        return new self($this->provider->withHeaders($headers), $this->fake, $this->agent);
    }    
}
