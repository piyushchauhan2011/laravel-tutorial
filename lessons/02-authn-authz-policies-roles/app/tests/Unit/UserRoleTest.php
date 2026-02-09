<?php

use App\Models\Role;
use App\Models\User;

test('user role helpers detect admin and member roles', function () {
    $adminRole = Role::factory()->admin()->create();
    $memberRole = Role::factory()->create();

    $user = User::factory()->create();
    $user->roles()->attach($memberRole);

    expect($user->hasRole('member'))->toBeTrue();
    expect($user->isAdmin())->toBeFalse();

    $user->roles()->attach($adminRole);

    expect($user->fresh()->isAdmin())->toBeTrue();
});
