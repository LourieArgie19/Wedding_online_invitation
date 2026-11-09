<?php

namespace Database\Seeders;

use App\Models\Wedding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DyAnnDennisSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $path = 'dy-ann-dennis/';
            $wedding = Wedding::firstOrCreate(['invitation_profile' => 'dy-ann-dennis'], [
                'name' => 'Dennis & Dy Ann', 'status' => 'Active',
                'wedding_date' => '2026-12-08',
                'note' => 'With faith at the heart of our promise and our families by our side, we invite you to witness the beginning of our married life.',
                'hero1' => $path.'embrace.webp', 'hero2' => $path.'together.webp', 'hero3' => $path.'on-the-steps.webp',
            ]);
            if (! $wedding->wasRecentlyCreated) {
                return;
            }
            // Keep the previous demo and all its messages, but remove it from the public invitation.
            Wedding::where('name', 'Darren & Jun')->where('hero1', 'hero-1.jpeg')->update(['status' => 'Deactive']);
            $wedding->galery()->create([
                'gallery1' => $path.'smiles.webp', 'gallery2' => $path.'wedding-rings.webp',
                'gallery3' => $path.'shared-faith.webp', 'gallery4' => $path.'on-the-grass.webp',
                'gallery5' => $path.'walking-together.webp', 'gallery6' => $path.'in-the-garden.webp',
            ]);
        });
    }
}
