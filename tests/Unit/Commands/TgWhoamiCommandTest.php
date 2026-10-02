<?php

declare(strict_types=1);

use BAGArt\TelegramBotBasic\Commands\TgWhoamiCommand;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

beforeEach(function () {
    $this->command = new TgWhoamiCommand();
    $this->output = new OutputStyle(new ArrayInput([]), new BufferedOutput());
    $this->command->setOutput($this->output);
    $this->command->setInput(new ArrayInput([], $this->command->getDefinition()));
});

test('tg:whoami command has correct signature', function () {
    expect($this->command->getName())->toBe('tg:whoami');
});

test('tg:whoami command has correct description', function () {
    expect($this->command->getDescription())->toBe('Method: getMe');
});

test('tg:whoami command defines token option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('token'))->toBeTrue()
        ->and($definition->getOption('token')->isValueOptional())->toBeTrue();
});

test('tg:whoami command uses TokenResolverTrait', function () {
    $uses = array_keys((new ReflectionClass(TgWhoamiCommand::class))->getTraits());
    expect($uses)->toContain('BAGArt\\TelegramBotBasic\\Commands\\Traits\\TokenResolverTrait');
});

test('tg:whoami handle returns FAILURE when no token provided', function () {
    $result = $this->command->handle(
        Mockery::mock(\BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiDTOClientContract::class),
    );

    expect($result)->toBe(\Illuminate\Console\Command::FAILURE);
});

test('tg:whoami handle returns FAILURE on api exception', function () {
    $client = Mockery::mock(\BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiDTOClientContract::class);
    $client->shouldReceive('request')
        ->andThrow(new \BAGArt\TelegramBot\Exceptions\ApiCommunication\TgApiCommunicationException('fail'));

    $this->command->setInput(new ArrayInput(
        ['--token' => '1234567890:'.str_repeat('A', 35)],
        $this->command->getDefinition(),
    ));

    $result = $this->command->handle($client);

    expect($result)->toBe(\Illuminate\Console\Command::FAILURE);
});

afterEach(function () {
    Mockery::close();
});
