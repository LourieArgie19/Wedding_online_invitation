<?php

use App\Models\User;
use App\Models\Wedding;
use Database\Seeders\DyAnnDennisSeeder;

test('the public deployment seeds only the invitation and preserves it on restart', function () {
    $this->seed(DyAnnDennisSeeder::class);
    $wedding = Wedding::sole();
    $wedding->update(['note' => 'Our saved invitation note']);

    $this->seed(DyAnnDennisSeeder::class);

    expect(Wedding::count())->toBe(1)
        ->and(User::count())->toBe(0)
        ->and($wedding->fresh()->note)->toBe('Our saved invitation note')
        ->and($wedding->galery()->count())->toBe(1);

    config(['invitation.public_only' => true]);
    $this->get('/?to=Wedding+Guest')->assertOk()
        ->assertSee('Wedding Guest')->assertSee('Dennis & Dy Ann')
        ->assertSee('December 8, 2026')->assertSee('Lourdes Mills');
    $this->get('/up')->assertOk();
    $this->get('/livewire/livewire.js')->assertOk();
});

test('the public deployment does not expose accounts or accept writes', function () {
    config(['invitation.public_only' => true]);

    foreach (['/admin', '/admin/login', '/login', '/register', '/dashboard', '/forgot-password', '/api/user'] as $path) {
        $this->get($path)->assertNotFound();
    }

    foreach (['/', '/register', '/login', '/livewire/update', '/livewire/upload-file'] as $path) {
        $this->post($path, [])->assertNotFound();
    }
});
