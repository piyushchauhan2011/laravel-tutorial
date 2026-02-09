<?php

namespace App\ValueObjects;

final class IssuePriority
{
    public function __construct(
        private readonly string $value,
    ) {
    }

    public static function from(string $value): self
    {
        $normalized = strtolower(trim($value));
        $allowed = ['low', 'medium', 'high', 'critical'];

        if (! in_array($normalized, $allowed, true)) {
            $normalized = 'medium';
        }

        return new self($normalized);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function weight(): int
    {
        return match ($this->value) {
            'low' => 25,
            'medium' => 50,
            'high' => 75,
            'critical' => 100,
            default => 50,
        };
    }
}
