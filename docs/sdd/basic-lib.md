# Telegram Bot Lib Basic — SDD

> **Module:** `telegram-bot-lib-basic` (`BAGArt\TelegramBotBasic`)
> **Status:** complete

---

## What Was Done

CLI commands for Telegram bot operations: poller, chatting, webhook management.

### Core Components

- **Update Processing**: Async pipeline supporting poller, webhooks, and internal events. At-least-once delivery semantics, idempotent execution.
- **UpdateContext**: Normalization layer for incoming Telegram updates.
- **CLI Commands**: Poller (long-polling mode), Chatting (interactive message handling), Webhook (set/remove/test webhook endpoints).

### Key Decisions

- At-least-once delivery: updates may be delivered multiple times; handlers must be idempotent.
- UpdateContext normalization decouples raw Telegram API from business logic.

### Bug Fixes

- **TgPollerCommand registration** (2026-09-17): `TgPollerCommand` was missing from `TelegramBotBasicServiceProvider::$commands`. The `tg:poll` command was defined but never registered in the Artisan kernel — dead code. Fixed by adding the import and class reference to the provider.

### Test Coverage (2026-09-17)

Added unit tests covering command structure, option wiring, processor contracts, and service provider registration:

| Test File | Tests | Coverage |
|---|---|---|
| `TgPollerCommandTest` | 14 | Signature, description, all 8 options, 3 traits |
| `TgWhoamiCommandTest` | 6 | Signature, description, token option, trait, FAILURE paths |
| `TgChattingCommandTest` | 12 | Signature, description, 5 options, 3 traits, FAILURE paths |
| `ConsoleEchoUpdateProcessorTest` | 9 | Contract impl, build throws, support, process, strict ordering |
| `TuiChattingUpdateProcessorTest` | 9 | Contract impl, build throws, support, process with input events |
| `TelegramBotBasicServiceProviderTest` | 6 | All 10 commands registered, correct count |

Total: 56 new tests (74 total across module). Skipped `LongPollingCommandTrait` tests — the trait requires full AsyncKernel boot with Redis, not suitable for unit testing.

### Files

- `src/` — Commands, update processing, middleware
- `tests/Unit/` — Unit tests (56 assertions across 6 new + 3 existing files)
- `docs/EN/processing.md` — English processing pipeline doc (301 lines)
- `docs/RU/processing.md` — Russian translation (307 lines)
