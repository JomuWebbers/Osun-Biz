<?php

declare(strict_types=1);

namespace App;

/**
 * Validation — form validation helper.
 */
class Validation
{
    private array $errors = [];

    public function required(string $field, mixed $value): void
    {
        if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
            $this->errors[$field][] = ucfirst($field) . ' is required.';
        }
    }

    public function email(string $field, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }
        if (!filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'Invalid email address.';
        }
    }

    public function minLength(string $field, string $value, int $min): void
    {
        if (strlen($value) < $min) {
            $this->errors[$field][] = ucfirst($field) . ' must be at least ' . $min . ' characters.';
        }
    }

    public function maxLength(string $field, string $value, int $max): void
    {
        if (strlen($value) > $max) {
            $this->errors[$field][] = ucfirst($field) . ' must not exceed ' . $max . ' characters.';
        }
    }

    public function range(string $field, int $value, int $min, int $max): void
    {
        if ($value < $min || $value > $max) {
            $this->errors[$field][] = ucfirst($field) . ' must be between ' . $min . ' and ' . $max . '.';
        }
    }

    public function passwordMatch(string $field1, string $field2, string $val1, string $val2): void
    {
        if ($val1 !== $val2) {
            $this->errors[$field2][] = 'Passwords do not match.';
        }
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}