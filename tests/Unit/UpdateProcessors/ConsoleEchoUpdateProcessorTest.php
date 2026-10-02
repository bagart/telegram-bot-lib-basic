<?php

declare(strict_types=1);

use BAGArt\TelegramBot\Configs\TgBotConfig;
use BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiDTOClientContract;
use BAGArt\TelegramBot\Contracts\Processing\Processors\TgTypeDTOProcessorContract;
use BAGArt\TelegramBot\Contracts\TgApi\TgApiTypeDTOContract;
use BAGArt\TelegramBot\Processing\BotProcessorContext;
use BAGArt\TelegramBotBasic\Commands\Processors\ConsoleEchoUpdateProcessor;
use BAGArt\TelegramBot\TgApi\Types\DTO\UpdateTypeDTO;
use BAGArt\TelegramBot\Wrappers\Wrappers\TgOutputWrapper;

beforeEach(function () {
    $this->dtoClient = Mockery::mock(TgBotApiDTOClientContract::class);
    $this->output = Mockery::mock(TgOutputWrapper::class);
    $this->botConfig = new TgBotConfig(token: '1234567890:'.str_repeat('A', 35));
});

test('ConsoleEchoUpdateProcessor implements TgTypeDTOProcessorContract', function () {
    $processor = new ConsoleEchoUpdateProcessor(
        dtoClient: $this->dtoClient,
        output: $this->output,
        botConfig: $this->botConfig,
        echoMode: false,
        showMode: false,
        once: false,
    );

    expect($processor)->toBeInstanceOf(TgTypeDTOProcessorContract::class);
});

test('ConsoleEchoUpdateProcessor build throws LogicException', function () {
    $context = (new ReflectionClass(BotProcessorContext::class))->newInstanceWithoutConstructor();

    ConsoleEchoUpdateProcessor::build($context);
})->throws(\LogicException::class, 'instance-registered');

test('ConsoleEchoUpdateProcessor support returns true for UpdateTypeDTO', function () {
    $processor = new ConsoleEchoUpdateProcessor(
        dtoClient: $this->dtoClient,
        output: $this->output,
        botConfig: $this->botConfig,
        echoMode: false,
        showMode: false,
        once: false,
    );

    $update = Mockery::mock(UpdateTypeDTO::class);
    expect($processor->support($update, $this->botConfig))->toBeTrue();
});

test('ConsoleEchoUpdateProcessor support returns false for non-UpdateTypeDTO', function () {
    $processor = new ConsoleEchoUpdateProcessor(
        dtoClient: $this->dtoClient,
        output: $this->output,
        botConfig: $this->botConfig,
        echoMode: false,
        showMode: false,
        once: false,
    );

    $dto = Mockery::mock(TgApiTypeDTOContract::class);
    expect($processor->support($dto, $this->botConfig))->toBeFalse();
});

test('ConsoleEchoUpdateProcessor isNeedUpdateDTO returns false', function () {
    $processor = new ConsoleEchoUpdateProcessor(
        dtoClient: $this->dtoClient,
        output: $this->output,
        botConfig: $this->botConfig,
        echoMode: false,
        showMode: false,
        once: false,
    );

    expect($processor->isNeedUpdateDTO())->toBeFalse();
});

test('ConsoleEchoUpdateProcessor executionKey returns null', function () {
    $processor = new ConsoleEchoUpdateProcessor(
        dtoClient: $this->dtoClient,
        output: $this->output,
        botConfig: $this->botConfig,
        echoMode: false,
        showMode: false,
        once: false,
    );

    $dto = Mockery::mock(UpdateTypeDTO::class);
    expect($processor->executionKey($dto))->toBeNull();
});

test('ConsoleEchoUpdateProcessor isStrictOrdered returns default false', function () {
    $processor = new ConsoleEchoUpdateProcessor(
        dtoClient: $this->dtoClient,
        output: $this->output,
        botConfig: $this->botConfig,
        echoMode: false,
        showMode: false,
        once: false,
    );

    $dto = Mockery::mock(UpdateTypeDTO::class);
    expect($processor->isStrictOrdered($dto, $this->botConfig))->toBeFalse();
});

test('ConsoleEchoUpdateProcessor isStrictOrdered returns true when set', function () {
    $processor = new ConsoleEchoUpdateProcessor(
        dtoClient: $this->dtoClient,
        output: $this->output,
        botConfig: $this->botConfig,
        echoMode: false,
        showMode: false,
        once: false,
        isStrictOrdered: true,
    );

    $dto = Mockery::mock(UpdateTypeDTO::class);
    expect($processor->isStrictOrdered($dto, $this->botConfig))->toBeTrue();
});

test('ConsoleEchoUpdateProcessor process throws TgApiUserBreakException when once mode', function () {
    $processor = new ConsoleEchoUpdateProcessor(
        dtoClient: $this->dtoClient,
        output: $this->output,
        botConfig: $this->botConfig,
        echoMode: false,
        showMode: false,
        once: true,
    );

    $chat = Mockery::mock(\BAGArt\TelegramBot\TgApi\Types\DTO\ChatTypeDTO::class);
    $chat->id = '123';

    $message = Mockery::mock(\BAGArt\TelegramBot\TgApi\Types\DTO\MessageTypeDTO::class);
    $message->chat = $chat;
    $message->text = 'hello';

    $update = Mockery::mock(UpdateTypeDTO::class);
    $update->message = $message;

    $processor->process($update, $this->botConfig);
})->throws(\BAGArt\TelegramBot\Exceptions\TgApiUserBreakException::class, 'Manual break by User');

afterEach(function () {
    Mockery::close();
});
