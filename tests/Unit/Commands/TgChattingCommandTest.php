<?php

declare(strict_types=1);

use BAGArt\TelegramBotBasic\Commands\TgChattingCommand;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

beforeEach(function () {
    $this->command = new TgChattingCommand();
    $this->output = new OutputStyle(new ArrayInput([]), new BufferedOutput());
    $this->command->setOutput($this->output);
    $this->command->setInput(new ArrayInput([], $this->command->getDefinition()));
});

test('tg:chatting command has correct signature', function () {
    expect($this->command->getName())->toBe('tg:chatting');
});

test('tg:chatting command has correct description', function () {
    expect($this->command->getDescription())->toBe('Interactive Telegram chat from terminal');
});

test('tg:chatting command defines token option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('token'))->toBeTrue();
});

test('tg:chatting command defines chat option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('chat'))->toBeTrue();
});

test('tg:chatting command defines user-id option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('user-id'))->toBeTrue();
});

test('tg:chatting command defines username option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('username'))->toBeTrue();
});

test('tg:chatting command defines timeout option with default 30', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('timeout'))->toBeTrue()
        ->and($definition->getOption('timeout')->getDefault())->toBe('30');
});

test('tg:chatting command defines dbg option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('dbg'))->toBeTrue();
});

test('tg:chatting command uses TokenResolverTrait', function () {
    $uses = array_keys((new ReflectionClass(TgChattingCommand::class))->getTraits());
    expect($uses)->toContain('BAGArt\\TelegramBotBasic\\Commands\\Traits\\TokenResolverTrait');
});

test('tg:chatting command uses LongPollingCommandTrait', function () {
    $uses = array_keys((new ReflectionClass(TgChattingCommand::class))->getTraits());
    expect($uses)->toContain('BAGArt\\TelegramBotBasic\\Commands\\Traits\\LongPollingCommandTrait');
});

test('tg:chatting command uses ArtisanExtraTrait', function () {
    $uses = array_keys((new ReflectionClass(TgChattingCommand::class))->getTraits());
    expect($uses)->toContain('BAGArt\\TelegramBotBasic\\Commands\\Traits\\ArtisanExtraTrait');
});

test('tg:chatting handle returns FAILURE when no token provided', function () {
    $result = $this->command->handle(
        Mockery::mock(\BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiDTOClientContract::class),
        Mockery::mock(\BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiClientContract::class),
    );

    expect($result)->toBe(\Illuminate\Console\Command::FAILURE);
});

test('tg:chatting handle returns FAILURE when no chat id provided', function () {
    $this->command->setInput(new ArrayInput(
        ['--token' => '1234567890:'.str_repeat('A', 35)],
        $this->command->getDefinition(),
    ));

    $result = $this->command->handle(
        Mockery::mock(\BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiDTOClientContract::class),
        Mockery::mock(\BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiClientContract::class),
    );

    expect($result)->toBe(\Illuminate\Console\Command::FAILURE);
});

afterEach(function () {
    Mockery::close();
});
