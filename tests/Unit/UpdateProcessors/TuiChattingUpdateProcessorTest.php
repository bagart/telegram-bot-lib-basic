<?php

declare(strict_types=1);

use BAGArt\TelegramBot\Configs\TgBotConfig;
use BAGArt\TelegramBot\Contracts\ApiCommunication\TgBotApiDTOClientContract;
use BAGArt\TelegramBot\Contracts\Processing\Processors\TgTypeDTOProcessorContract;
use BAGArt\TelegramBot\Contracts\TgApi\TgApiTypeDTOContract;
use BAGArt\TelegramBot\Processing\BotProcessorContext;
use BAGArt\TelegramBotBasic\Commands\Processors\TuiChattingUpdateProcessor;
use BAGArt\TelegramBot\TgApi\Types\DTO\UpdateTypeDTO;
use BAGArt\TelegramBot\Wrappers\Wrappers\TgOutputWrapper;

beforeEach(function () {
    $this->dtoClient = Mockery::mock(TgBotApiDTOClientContract::class);
    $this->botConfig = new TgBotConfig(token: '1234567890:'.str_repeat('A', 35));
});

test('TuiChattingUpdateProcessor implements TgTypeDTOProcessorContract', function () {
    $processor = new TuiChattingUpdateProcessor(
        dtoClient: $this->dtoClient,
        botConfig: $this->botConfig,
        chatSession: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TuiChatSession::class),
        inputHandler: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\Input\TerminalInputHandler::class),
        uiRenderer: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TerminalUiRendererInterface::class),
        output: Mockery::mock(TgOutputWrapper::class),
    );

    expect($processor)->toBeInstanceOf(TgTypeDTOProcessorContract::class);
});

test('TuiChattingUpdateProcessor build throws LogicException', function () {
    $context = (new ReflectionClass(BotProcessorContext::class))->newInstanceWithoutConstructor();

    TuiChattingUpdateProcessor::build($context);
})->throws(\LogicException::class, 'instance-registered');

test('TuiChattingUpdateProcessor support returns true for UpdateTypeDTO', function () {
    $processor = new TuiChattingUpdateProcessor(
        dtoClient: $this->dtoClient,
        botConfig: $this->botConfig,
        chatSession: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TuiChatSession::class),
        inputHandler: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\Input\TerminalInputHandler::class),
        uiRenderer: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TerminalUiRendererInterface::class),
        output: Mockery::mock(TgOutputWrapper::class),
    );

    $update = Mockery::mock(UpdateTypeDTO::class);
    expect($processor->support($update, $this->botConfig))->toBeTrue();
});

test('TuiChattingUpdateProcessor support returns false for non-UpdateTypeDTO', function () {
    $processor = new TuiChattingUpdateProcessor(
        dtoClient: $this->dtoClient,
        botConfig: $this->botConfig,
        chatSession: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TuiChatSession::class),
        inputHandler: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\Input\TerminalInputHandler::class),
        uiRenderer: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TerminalUiRendererInterface::class),
        output: Mockery::mock(TgOutputWrapper::class),
    );

    $dto = Mockery::mock(TgApiTypeDTOContract::class);
    expect($processor->support($dto, $this->botConfig))->toBeFalse();
});

test('TuiChattingUpdateProcessor isNeedUpdateDTO returns false', function () {
    $processor = new TuiChattingUpdateProcessor(
        dtoClient: $this->dtoClient,
        botConfig: $this->botConfig,
        chatSession: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TuiChatSession::class),
        inputHandler: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\Input\TerminalInputHandler::class),
        uiRenderer: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TerminalUiRendererInterface::class),
        output: Mockery::mock(TgOutputWrapper::class),
    );

    expect($processor->isNeedUpdateDTO())->toBeFalse();
});

test('TuiChattingUpdateProcessor executionKey returns null', function () {
    $processor = new TuiChattingUpdateProcessor(
        dtoClient: $this->dtoClient,
        botConfig: $this->botConfig,
        chatSession: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TuiChatSession::class),
        inputHandler: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\Input\TerminalInputHandler::class),
        uiRenderer: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TerminalUiRendererInterface::class),
        output: Mockery::mock(TgOutputWrapper::class),
    );

    $dto = Mockery::mock(UpdateTypeDTO::class);
    expect($processor->executionKey($dto))->toBeNull();
});

test('TuiChattingUpdateProcessor isStrictOrdered returns default false', function () {
    $processor = new TuiChattingUpdateProcessor(
        dtoClient: $this->dtoClient,
        botConfig: $this->botConfig,
        chatSession: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TuiChatSession::class),
        inputHandler: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\Input\TerminalInputHandler::class),
        uiRenderer: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TerminalUiRendererInterface::class),
        output: Mockery::mock(TgOutputWrapper::class),
    );

    $dto = Mockery::mock(UpdateTypeDTO::class);
    expect($processor->isStrictOrdered($dto, $this->botConfig))->toBeFalse();
});

test('TuiChattingUpdateProcessor isStrictOrdered returns true when set', function () {
    $processor = new TuiChattingUpdateProcessor(
        dtoClient: $this->dtoClient,
        botConfig: $this->botConfig,
        chatSession: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TuiChatSession::class),
        inputHandler: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\Input\TerminalInputHandler::class),
        uiRenderer: Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TerminalUiRendererInterface::class),
        output: Mockery::mock(TgOutputWrapper::class),
        isStrictOrdered: true,
    );

    $dto = Mockery::mock(UpdateTypeDTO::class);
    expect($processor->isStrictOrdered($dto, $this->botConfig))->toBeTrue();
});

test('TuiChattingUpdateProcessor process polls input and processes matching update', function () {
    $inputHandler = Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\Input\TerminalInputHandler::class);
    $inputHandler->shouldReceive('poll')->once()->andReturn(null);

    $chatSession = Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TuiChatSession::class);
    $uiRenderer = Mockery::mock(\BAGArt\TelegramBot\CLI\Chatting\TerminalUiRendererInterface::class);

    $processor = new TuiChattingUpdateProcessor(
        dtoClient: $this->dtoClient,
        botConfig: $this->botConfig,
        chatSession: $chatSession,
        inputHandler: $inputHandler,
        uiRenderer: $uiRenderer,
        output: Mockery::mock(TgOutputWrapper::class),
    );

    $chat = Mockery::mock(\BAGArt\TelegramBot\TgApi\Types\DTO\ChatTypeDTO::class);
    $chat->id = '123';

    $message = Mockery::mock(\BAGArt\TelegramBot\TgApi\Types\DTO\MessageTypeDTO::class);
    $message->chat = $chat;
    $message->text = 'hello';

    $update = Mockery::mock(UpdateTypeDTO::class);
    $update->message = $message;

    $chatSession->shouldReceive('matchesFilter')->with($update)->once()->andReturn(true);
    $chatSession->shouldReceive('addMessage')->with($message)->once();
    $chatSession->shouldReceive('getMessages')->once()->andReturn([]);
    $inputHandler->shouldReceive('getInputBuffer')->once()->andReturn('');
    $uiRenderer->shouldReceive('renderChatInterface')->once();

    $processor->process($update, $this->botConfig);
});

afterEach(function () {
    Mockery::close();
});
