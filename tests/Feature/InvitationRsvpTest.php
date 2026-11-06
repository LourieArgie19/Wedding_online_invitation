<?php

use App\Livewire\CreateRsvp;
use App\Models\Rsvp;
use App\Models\Wedding;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed();
});

test('guest RSVP submissions are closed even from an older invitation page', function () {
    $count = Rsvp::count();

    Livewire::test(CreateRsvp::class)
        ->set('name', 'Alex Guest')
        ->set('attendance', 'yes')
        ->set('amount', '2')
        ->call('submit')
        ->assertForbidden();

    $this->assertDatabaseCount('rsvps', $count);
});

test('only the active wedding is shown', function () {
    $original = Wedding::where('status', 'Active')->sole();
    $original->update(['status' => 'Deactive']);
    Wedding::create([
        'name' => 'Alex & Sam', 'note' => 'A new chapter', 'hero1' => 'hero-1.jpeg', 'status' => 'Active',
    ]);

    $this->get('/')->assertOk()->assertSee('Alex &amp; Sam', false)->assertDontSee('Jakarta Cathedral');
});

test('the program uses the confirmed wedding date and local ceremony time', function () {
    $this->get('/')->assertOk()->assertSee('Tuesday, December 8, 2026')
        ->assertSee('3:30 PM')->assertSee('2026-12-08T15:30:00+08:00');

    Wedding::where('status', 'Active')->sole()->update(['wedding_date' => '2027-02-14']);
    $this->get('/')->assertOk()->assertSee('February')->assertSee('2027-02-14T15:30:00+08:00');
});
