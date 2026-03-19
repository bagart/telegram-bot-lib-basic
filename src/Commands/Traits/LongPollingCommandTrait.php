<?php

declare(strict_types=1);

namespace BAGArt\TelegramBotBasic\Commands\Traits;

use BAGArt\AsyncKernel\AsyncKernel;
use BAGArt\AsyncKernel\Backpressure\ASKBackpressureStrategyDynamicSkip;
use BAGArt\AsyncKernel\Contracts\AsyncKernelContract;
use BAGArt\AsyncKernel\Wrappers\ASKLogWrapper;
use BAGArt\TelegramBot\ApiCommunication\Polling\TgPollerDaemon;
use BAGArt\TelegramBot\Configs\TgBotConfig;
use BAGArt\TelegramBot\Configs\TgPollerConfig;
use BAGArt\TelegramBot\Configs\TgServiceConfig;
use BAGArt\TelegramBot\Exceptions\TgApiUserBreakException;
use BAGArt\TelegramBot\Processing\RegisteredUpdateProcessorSelector;
use BAGArt\TelegramBot\TgApi\Methods\DTO\GetUpdatesMethodDTO;
use BAGArt\TelegramBot\TgApi\Types\DTO\UpdateTypeDTO;
use BAGArt\TelegramBot\TgBotSetup;
use BAGArt\TelegramBot\TgBotSetupFactory;
use BAGArt\TelegramBot\Wrappers\Wrappers\TgOutputWrapper;
use Illuminate\Console\Command;

/** @mixin Command */
trait LongPollingCommandTrait
{
    private ?TgBotSetup $botSetup = null;

    private function buildConfigPoller(
        string $token,
        callable $fn,
        ASKLogWrapper $logger,
        bool $isStrictOrdered = false,
        ?TgPollerConfig $pollerConfig = null,
        ?AsyncKernelContract $asyncKernel = null,
    ): TgPollerDaemon {
        $output = new TgOutputWrapper($this->output);
        $serviceConfig = new TgServiceConfig();

        $this->botSetup = TgBotSetupFactory::build(
            logger: $logger,
        )
            ->create(serviceConfig: $serviceConfig);

        // Register the callable $fn on the processor registry
        // It will be auto-wrapped in CallableProcessor and invoked for each update
        $this->botSetup->processorRegistry->register(
            UpdateTypeDTO::class,
            $fn,
        );

        $updateProcessorSelector = new RegisteredUpdateProcessorSelector(
            serviceConfig: $serviceConfig,
            botSetup: $this->botSetup,
        );

        $this->trap(SIGINT, function () use ($output): void {
            $output->newLine();
            $output->info('Stopping...');

            throw new TgApiUserBreakException(
                GetUpdatesMethodDTO::tgApiEntity()->name,
            );
        });

        ($asyncKernel ?? new AsyncKernel($logger))
            ->addDaemon(
                new TgPollerDaemon(
                    botConfig: new TgBotConfig(token: $token),
                    queue: $this->botSetup->queue,
                    dtoClient: $this->botSetup->dtoClient,
                    updateProcessorSelector: $updateProcessorSelector,
                    logger: $this->botSetup->logger,
                    pollerConfig: $pollerConfig,
                    processingStatistics: $this->botSetup->processingStatistics,
                    queueName: 'tg-inbox',
                    backpressureStrategy: new ASKBackpressureStrategyDynamicSkip(),
                )
            )
            ->run();
    }
}
