<?php

use App\Models\Issue;
use App\Services\IssueSeverityScorer;

it('scores critical issues higher than low priority issues', function () {
    $scorer = app(IssueSeverityScorer::class);

    $critical = Issue::factory()->make([
        'priority' => 'critical',
        'title' => str_repeat('A', 40),
        'description' => str_repeat('B', 120),
    ]);

    $low = Issue::factory()->make([
        'priority' => 'low',
        'title' => 'Short',
        'description' => 'Short description',
    ]);

    expect($scorer->score($critical))->toBeGreaterThan($scorer->score($low));
});
