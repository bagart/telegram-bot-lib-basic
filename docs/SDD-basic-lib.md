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

### Files

- `src/` — Commands, update processing, middleware
- `docs/EN/processing.md` — English processing pipeline doc (301 lines)
- `docs/RU/processing.md` — Russian translation (307 lines)
