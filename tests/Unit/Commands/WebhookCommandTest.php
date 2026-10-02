<?php

declare(strict_types=1);

use BAGArt\TelegramBot\Configs\TgBotConfig;
use BAGArt\TelegramBot\Configs\TgPollerConfig;
use BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiDTOClientContract;
use BAGArt\TelegramBot\TgApi\Types\DTO\UserTypeDTO;
use BAGArt\TelegramBot\TgApi\Types\DTO\WebhookInfoTypeDTO;
use BAGArt\TelegramBotBasic\Commands\WebhookCommand;
use Illuminate\Console\OutputStyle;
use Laravel\Prompts\Prompt;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Laravel Prompts fallback registration is process-global: any command run
 * earlier in the same Pest process (e.g. the TelegramBotLib suite) leaves
 * `Prompt::$shouldFallback`/`$fallbacks` bound to that command's mocked
 * output, which hijacks multiselect() here. Reset to the solo-run state.
 */
function resetPromptFallbackStatics(): void
{
    $prompt = new ReflectionClass(Prompt::class);
    $prompt->getProperty('shouldFallback')->setValue(null, false);
    $prompt->getProperty('fallbacks')->setValue(null, []);
}

beforeEach(function () {
    resetPromptFallbackStatics();

    $this->command = new class () extends WebhookCommand {
        public function exposeResolveBotName(
            TgBotApiDTOClientContract $client,
            string $token,
        ): string {
            return $this->resolveBotName($client, $token);
        }

        public function exposeDisplayWebhookInfo(
            WebhookInfoTypeDTO $info,
            \BAGArt\TelegramBot\TgIntegration\WebhookManager $webhookManager
        ): void {
            $this->displayWebhookInfo($info, $webhookManager);
        }

        public function exposeResolveAllowedUpdates(?WebhookInfoTypeDTO $currentInfo): array
        {
            return $this->resolveAllowedUpdates($currentInfo);
        }
    };
    $output = new OutputStyle(new ArrayInput([]), new BufferedOutput());
    $this->command->setOutput($output);
});

afterEach(function () {
    Mockery::close();
});

test('resolveBotName returns @username when available', function () {
    $client = Mockery::mock(TgBotApiDTOClientContract::class);
    $user = new UserTypeDTO(id: '1', isBot: true, firstName: 'Bot', username: 'mybot');
    $client->shouldReceive('request')
        ->with(Mockery::type(TgBotConfig::class), Mockery::any())
        ->andReturn(new \BAGArt\TelegramBot\Http\Pure\TgApiResponse(true, [], $user));

    $name = $this->command->exposeResolveBotName($client, '123456789:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

    expect($name)->toBe('@mybot');
});

test('resolveBotName returns firstName when no username', function () {
    $client = Mockery::mock(TgBotApiDTOClientContract::class);
    $user = new UserTypeDTO(id: '1', isBot: true, firstName: 'MyBot', username: '');
    $client->shouldReceive('request')
        ->with(Mockery::type(TgBotConfig::class), Mockery::any())
        ->andReturn(new \BAGArt\TelegramBot\Http\Pure\TgApiResponse(true, [], $user));

    $name = $this->command->exposeResolveBotName($client, '123456789:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

    expect($name)->toBe('MyBot');
});

test('resolveBotName returns unknown on error', function () {
    $client = Mockery::mock(TgBotApiDTOClientContract::class);
    $client->shouldReceive('request')
        ->with(Mockery::type(TgBotConfig::class), Mockery::any())
        ->andThrow(new RuntimeException('Network error'));

    $name = $this->command->exposeResolveBotName($client, '123456789:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

    expect($name)->toBe('unknown');
});

test('allowedUpdatesDefault keeps an explicit webhook list (operator-owned)', function () {
    $current = new WebhookInfoTypeDTO(url: 'https://example.com/hook', hasCustomCertificate: false, pendingUpdateCount: 0, allowedUpdates: ['message']);

    expect($this->command->allowedUpdatesDefault($current))->toBe(['message']);
});

test('allowedUpdatesDefault falls back to the platform subscription when the webhook has no list', function () {
    $withoutList = new WebhookInfoTypeDTO(url: 'https://example.com/hook', hasCustomCertificate: false, pendingUpdateCount: 0);

    expect($this->command->allowedUpdatesDefault(null))->toBe(TgPollerConfig::DEFAULT_ALLOWED_UPDATES)
        ->toContain('chat_member')
        ->toContain('my_chat_member')
        ->and($this->command->allowedUpdatesDefault($withoutList))->toBe(TgPollerConfig::DEFAULT_ALLOWED_UPDATES);
});

test('resolveAllowedUpdates prefers an explicit --allowed-updates CLI value', function () {
    $this->command->setInput(new ArrayInput(
        ['--allowed-updates' => ['chat_member']],
        $this->command->getDefinition(),
    ));

    expect($this->command->exposeResolveAllowedUpdates(null))->toBe(['chat_member']);
});

test('resolveAllowedUpdates defaults to the platform subscription when non-interactive and no list is set', function () {
    Prompt::interactive(false);
    $this->command->setInput(new ArrayInput([], $this->command->getDefinition()));

    expect($this->command->exposeResolveAllowedUpdates(null))->toBe(TgPollerConfig::DEFAULT_ALLOWED_UPDATES);
});

test('resolveAllowedUpdates keeps the current webhook list as the selection default when non-interactive', function () {
    Prompt::interactive(false);
    $this->command->setInput(new ArrayInput([], $this->command->getDefinition()));
    $current = new WebhookInfoTypeDTO(url: 'https://example.com/hook', hasCustomCertificate: false, pendingUpdateCount: 0, allowedUpdates: ['message', 'callback_query']);

    expect($this->command->exposeResolveAllowedUpdates($current))->toBe(['message', 'callback_query']);
});
