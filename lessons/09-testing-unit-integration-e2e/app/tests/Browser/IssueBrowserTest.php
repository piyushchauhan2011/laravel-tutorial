<?php

it('creates an issue from the browser flow with pest browser testing', function () {
    visit('/issues')
        ->assertSee('Issue Tracker')
        ->click('Report new issue')
        ->assertSee('Report Issue')
        ->fill('Title', 'Pest browser flow created issue')
        ->fill('Description', 'Pest browser testing validates the web form journey end to end.')
        ->select('Priority', 'high')
        ->fill('Reported by', 'pest-browser')
        ->press('Submit Issue')
        ->assertSee('Issue reported and queued for severity assessment.')
        ->assertSee('Pest browser flow created issue')
        ->assertSee('Status:')
        ->assertSee('open');
});
