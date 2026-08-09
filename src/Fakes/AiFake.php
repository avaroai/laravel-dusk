<?php

declare(strict_types=1);

namespace AvaroAI\LaravelDusk\Fakes;

use RuntimeException;
use Laravel\Ai\AiManager;
use InvalidArgumentException;
use Laravel\Ai\Contracts\Agent;
use Illuminate\Support\Facades\App;
use Laravel\Ai\Responses\Data\ToolCall;
use Laravel\Ai\Contracts\Providers\TextProvider;

final class AiFake extends AiManager
{
    private const string RESPONSE_TYPE = '_ai_fake_laravel_dusk_response_type';

    /**
     * Configure the fake responses for each agent.
     *
     */
    public function __construct(mixed $agents = [])
    {
        parent::__construct(App::getFacadeRoot());

        $agents = json_decode(json_encode($agents, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($agents)) {
            throw new InvalidArgumentException('The AI fake configuration must be an array of agents.');
        }

        foreach ($agents as $agent => $responses) {
            if (! is_string($agent) || ! is_array($responses)) {
                throw new InvalidArgumentException('Each AI fake agent must have an array of responses.');
            }

            $responses = array_map($this->prepareResponse(...), $responses);

            $this->fakeAgent($agent, $responses)->preventStrayPrompts();
        }
    }

    /**
     * Preserve the fake agent state between browser requests.
     *
     */
    public function __sleep() : array
    {
        return [
            'fakeAgentGateways',
            'recordedPrompts',
            'recordedQueuedPrompts',
        ];
    }

    /**
     * Restore the application dependencies after deserialization.
     *
     */
    public function __wakeup() : void
    {
        $this->app    = App::getFacadeRoot();
        $this->config = $this->app->make('config');
    }

    /**
     * Resolve the faked text provider for the given agent.
     *
     */
    public function textProviderFor(Agent $agent, ?string $name = null) : TextProvider
    {
        if (! $this->hasFakeGatewayFor($agent)) {
            $agent_class = $agent::class;

            throw new RuntimeException("Attempted prompt for unfaked agent [{$agent_class}].");
        }

        return parent::textProviderFor($agent, $name);
    }

    /**
     * Describe a tool call response that can cross the browser process boundary.
     *
     */
    public static function toolCall(string $id, string $name, array $arguments) : array
    {
        return [
            self::RESPONSE_TYPE => 'tool_call',
            'id'                => $id,
            'name'              => $name,
            'arguments'         => $arguments,
        ];
    }

    /**
     * Convert transport-safe response descriptions into SDK response objects.
     *
     */
    private function prepareResponse(mixed $response) : mixed
    {
        if (! is_array($response) || ($response[self::RESPONSE_TYPE] ?? null) !== 'tool_call') {
            return $response;
        }

        unset($response[self::RESPONSE_TYPE]);

        return ToolCall::fromArray($response);
    }
}
