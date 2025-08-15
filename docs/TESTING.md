# Testing

We use Codeception with **Unit** and **Integration** suites.

## Running tests
```bash
vendor/bin/codecept run Unit
vendor/bin/codecept run Integration
```

## DB in integration tests
- Integration tests get DB config from the same env DSN used by the app.
- No `TRUNCATE` scattered in tests. Prefer prepared fixtures/migrations or a clean state per test case.

**Db module config**
Point the Codeception Db module to the PDO triple produced by the DSN parsing step. Keep config centralized.

## Test data helpers

### EntryHelper
Generates valid baseline payloads for entries to reduce duplication.
Use it in unit tests and data providers as the default “happy path” input, then override fields under test.

```php
$data = EntryHelper::getData(); // returns a valid array
```

## Data Providers
Prefer data providers for negative cases (empty title, too long body, invalid dates) to keep tests compact and exhaustive.

## Fakes vs Mocks
- Start with **simple fakes** (in `Tests/Support/Fakes`) for repositories; they are stable and keep test intent clear.
- Only use mocks when you need to assert specific interaction patterns that a fake cannot represent.

## UUID checks
Use a single centralized UUID v4 pattern/helper; do not duplicate regexes inside tests.

## Naming & PHPDoc
- Tests and helpers must have clear PHPDoc (English) describing intent.
- Use **camelCase** for test method names.
- For positive cases: `methodOrUcConditionExpectedOutcome()`, describing the specific scenario and expected result.
- For negative cases with a common expected effect, use a **single parameterized test** with a DataProvider, where the name reflects the shared outcome, for example:
```php
public function testValidationErrorsDoNotTouchRepository(array $overrides): void
```

