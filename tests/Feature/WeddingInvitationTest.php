<?php

use App\Livewire\CreateWish;
use App\Models\User;
use App\Models\Wishes;
use Filament\Facades\Filament;
use Livewire\Livewire;

test('the seeded invitation renders the guest, wedding, and gallery', function () {
    $this->seed();

    $this->get('/?to=Wedding+Guest')
        ->assertOk()
        ->assertSee('Wedding Guest')
        ->assertSee('Dy Ann & Dennis')
        ->assertSee('storage/dy-ann-dennis/smiles.webp')
        ->assertSee('Lourdes Mills')
        ->assertSee('3:00 PM')->assertSee('3:30 PM')->assertSee('Ptr. Danilo Maboloc')
        ->assertSeeInOrder(['Cherry Grace S. Ampo', 'Jisbert Ampo', 'Ma. Corazon Tibon', 'Federico Tibon', 'Arwin C. Balbon', 'Avemly G. Balbon'])
        ->assertDontSee('Darren & Jun')->assertDontSee('2050')->assertDontSee('Jakarta');
});

test('wishes are not displayed and guest submission paths remain closed', function () {
    $this->seed();
    Wishes::create([
        'wedding_id' => \App\Models\Wedding::where('status', 'Active')->sole()->id,
        'name' => 'Wedding Guest',
        'comment' => 'Congratulations to you both!',
    ]);
    $initialCount = Wishes::count();

    $this->get('/')->assertOk()
        ->assertDontSee('Wedding Wishes')->assertDontSee('Congratulations to you both!')
        ->assertDontSee('Wedding Guest')
        ->assertDontSee('<form', false)->assertDontSee('<input', false)
        ->assertDontSee('<textarea', false)->assertDontSee('Leave a message');

    $this->post('/home', ['name' => 'New Guest', 'comment' => 'New message'])
        ->assertNotFound();

    Livewire::test(CreateWish::class)
        ->set('name', 'New Guest')
        ->set('comment', 'New message')
        ->call('createWish')
        ->assertForbidden();

    $this->assertDatabaseCount('wishes', $initialCount);
});

test('the seeded admin can open every resource and its forms', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@codelogy.dev')->firstOrFail());
    $panel = Filament::getPanel('admin');
    Filament::setCurrentPanel($panel);

    $this->get('/admin')->assertOk();

    foreach ($panel->getResources() as $resource) {
        $this->get($resource::getUrl('index'))->assertOk();
        $this->get($resource::getUrl('create'))->assertStatus($resource::canCreate() ? 200 : 403);
        $record = $resource::getModel()::firstOrFail();
        $this->get($resource::getUrl('edit', ['record' => $record]))
            ->assertStatus($resource::canEdit($record) ? 200 : 403);
    }
});