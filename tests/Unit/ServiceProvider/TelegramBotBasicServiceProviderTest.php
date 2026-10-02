<?php

declare(strict_types=1);

use BAGArt\TelegramBotBasic\TelegramBotBasicServiceProvider;

test('TelegramBotBasicServiceProvider registers TgPollerCommand', function () {
    $provider = new TelegramBotBasicServiceProvider(
        Mockery::mock(\Illuminate\Contracts\Foundation\Application::class),
    );

    $reflection = new ReflectionClass($provider);
    $property = $reflection->getProperty('commands');
    $property->setAccessible(true);
    $commands = $property->getValue($provider);

    expect($commands)->toContain(\BAGArt\TelegramBotBasic\Commands\TgPollerCommand::class);
});

test('TelegramBotBasicServiceProvider registers WebhookCommand', function () {
    $provider = new TelegramBotBasicServiceProvider(
        Mockery::mock(\Illuminate\Contracts\Foundation\Application::class),
    );

    $reflection = new ReflectionClass($provider);
    $property = $reflection->getProperty('commands');
    $property->setAccessible(true);
    $commands = $property->getValue($provider);

    expect($commands)->toContain(\BAGArt\TelegramBotBasic\Commands\WebhookCommand::class);
});

test('TelegramBotBasicServiceProvider registers TgWhoamiCommand', function () {
    $provider = new TelegramBotBasicServiceProvider(
        Mockery::mock(\Illuminate\Contracts\Foundation\Application::class),
    );

    $reflection = new ReflectionClass($provider);
    $property = $reflection->getProperty('commands');
    $property->setAccessible(true);
    $commands = $property->getValue($provider);

    expect($commands)->toContain(\BAGArt\TelegramBotBasic\Commands\TgWhoamiCommand::class);
});

test('TelegramBotBasicServiceProvider registers TgChattingCommand', function () {
    $provider = new TelegramBotBasicServiceProvider(
        Mockery::mock(\Illuminate\Contracts\Foundation\Application::class),
    );

    $reflection = new ReflectionClass($provider);
    $property = $reflection->getProperty('commands');
    $property->setAccessible(true);
    $commands = $property->getValue($provider);

    expect($commands)->toContain(\BAGArt\TelegramBotBasic\Commands\TgChattingCommand::class);
});

test('TelegramBotBasicServiceProvider registers all demo commands', function () {
    $provider = new TelegramBotBasicServiceProvider(
        Mockery::mock(\Illuminate\Contracts\Foundation\Application::class),
    );

    $reflection = new ReflectionClass($provider);
    $property = $reflection->getProperty('commands');
    $property->setAccessible(true);
    $commands = $property->getValue($provider);

    expect($commands)->toContain(\BAGArt\TelegramBotBasic\Commands\Demo\DemoSendPollCommand::class)
        ->toContain(\BAGArt\TelegramBotBasic\Commands\Demo\ExampleSequentialCommand::class)
        ->toContain(\BAGArt\TelegramBotBasic\Commands\Demo\ExampleParallelFuturesCommand::class)
        ->toContain(\BAGArt\TelegramBotBasic\Commands\Demo\ExampleParallelBatchCommand::class)
        ->toContain(\BAGArt\TelegramBotBasic\Commands\Demo\ExampleAllVariantsCommand::class)
        ->toContain(\BAGArt\TelegramBotBasic\Commands\Demo\ExampleTransportComparisonCommand::class);
});

test('TelegramBotBasicServiceProvider registers exactly 10 commands', function () {
    $provider = new TelegramBotBasicServiceProvider(
        Mockery::mock(\Illuminate\Contracts\Foundation\Application::class),
    );

    $reflection = new ReflectionClass($provider);
    $property = $reflection->getProperty('commands');
    $property->setAccessible(true);
    $commands = $property->getValue($provider);

    expect($commands)->toHaveCount(10);
});

afterEach(function () {
    Mockery::close();
});
