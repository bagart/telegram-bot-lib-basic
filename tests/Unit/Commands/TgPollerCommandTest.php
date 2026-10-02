<?php

declare(strict_types=1);

use BAGArt\TelegramBotBasic\Commands\TgPollerCommand;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

beforeEach(function () {
    $this->command = new TgPollerCommand();
    $this->output = new OutputStyle(new ArrayInput([]), new BufferedOutput());
    $this->command->setOutput($this->output);
});

test('tg:poll command has correct signature', function () {
    expect($this->command->getName())->toBe('tg:poll');
});

test('tg:poll command has correct description', function () {
    expect($this->command->getDescription())->toBe('Start the Telegram bot in long-polling mode with Echo mode');
});

test('tg:poll command defines token option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('token'))->toBeTrue()
        ->and($definition->getOption('token')->isValueOptional())->toBeTrue();
});

test('tg:poll command defines echo option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('echo'))->toBeTrue()
        ->and($definition->getOption('echo')->isValueRequired())->toBeFalse();
});

test('tg:poll command defines show option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('show'))->toBeTrue();
});

test('tg:poll command defines silent option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('silent'))->toBeTrue();
});

test('tg:poll command defines timeout option with default 30', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('timeout'))->toBeTrue()
        ->and($definition->getOption('timeout')->getDefault())->toBe('30');
});

test('tg:poll command defines limit option with default 100', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('limit'))->toBeTrue()
        ->and($definition->getOption('limit')->getDefault())->toBe('100');
});

test('tg:poll command defines once option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('once'))->toBeTrue();
});

test('tg:poll command defines no-ack option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('no-ack'))->toBeTrue();
});

test('tg:poll command defines dbg option', function () {
    $definition = $this->command->getDefinition();
    expect($definition->hasOption('dbg'))->toBeTrue();
});

test('tg:poll command uses TokenResolverTrait', function () {
    $uses = array_keys((new ReflectionClass(TgPollerCommand::class))->getTraits());
    expect($uses)->toContain('BAGArt\\TelegramBotBasic\\Commands\\Traits\\TokenResolverTrait');
});

test('tg:poll command uses LongPollingCommandTrait', function () {
    $uses = array_keys((new ReflectionClass(TgPollerCommand::class))->getTraits());
    expect($uses)->toContain('BAGArt\\TelegramBotBasic\\Commands\\Traits\\LongPollingCommandTrait');
});

test('tg:poll command uses ArtisanExtraTrait', function () {
    $uses = array_keys((new ReflectionClass(TgPollerCommand::class))->getTraits());
    expect($uses)->toContain('BAGArt\\TelegramBotBasic\\Commands\\Traits\\ArtisanExtraTrait');
});
