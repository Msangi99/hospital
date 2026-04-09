<?php

test('homepage can be rendered', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Sema', escape: false);
});

test('locale can be switched', function () {
    $this->from(route('home'))
        ->post(route('locale.set'), ['locale' => 'fr'])
        ->assertRedirect(route('home'));

    $this->withSession(['locale' => 'fr'])
        ->get(route('home'))
        ->assertOk()
        ->assertSee('S&#039;INSCRIRE', escape: false);
});

