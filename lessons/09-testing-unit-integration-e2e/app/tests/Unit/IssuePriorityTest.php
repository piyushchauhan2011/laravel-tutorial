<?php

use App\ValueObjects\IssuePriority;

it('maps priority weights correctly', function () {
    expect(IssuePriority::from('low')->weight())->toBe(25)
        ->and(IssuePriority::from('medium')->weight())->toBe(50)
        ->and(IssuePriority::from('high')->weight())->toBe(75)
        ->and(IssuePriority::from('critical')->weight())->toBe(100);
});

it('defaults unknown priority values to medium', function () {
    $priority = IssuePriority::from('unexpected');

    expect($priority->value())->toBe('medium')
        ->and($priority->weight())->toBe(50);
});
