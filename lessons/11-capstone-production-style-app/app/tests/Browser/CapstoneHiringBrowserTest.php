<?php

it('opens the capstone job creation screen and shows required inputs', function () {
    visit('/jobs')
        ->assertSee('Create job')
        ->click('Create job')
        ->assertPathIs('/jobs/create')
        ->assertSee('Create Job')
        ->assertSee('Title')
        ->assertSee('Department')
        ->assertSee('Location')
        ->assertSee('Description')
        ->assertSee('Create Job');
});
