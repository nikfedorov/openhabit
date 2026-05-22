## Code Coverage

- Use `@codeCoverageIgnore` only in exceptional cases when code cannot be tested (e.g., code that can't run in test environment like database deletion in tests).
- Prefer writing tests for all code paths including exception handling using mocks and `andThrow()`.

## PHPStan

- Do not use `@phpstan-ignore`, `@phpstan-ignore-line`, `@phpstan-ignore-next-line` or similar inline ignore comments.
- If PHPStan reports type errors, fix them properly with correct type annotations or refactor the code.
