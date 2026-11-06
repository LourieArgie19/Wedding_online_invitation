<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['to' => ['nullable', 'string', 'max:255']]);
        $invitation = Wedding::where('status', 'Active')->orderBy('id')->first();
        $program = $invitation?->invitation_profile === 'dy-ann-dennis' ? config('invitation.dy-ann-dennis') : null;
        $details = $invitation?->detail()->orderBy('date')->get() ?? collect();
        $albums = $invitation?->galery()->get() ?? collect();
        $photos = $albums->flatMap(fn ($album) => collect(range(1, 6))->map(fn ($n) => $album->{'gallery'.$n}))->filter()->values();
        $date = $invitation?->wedding_date
            ? Carbon::parse($invitation->wedding_date.' '.($program['ceremony'] ?? '00:00'), $program['timezone'] ?? config('app.timezone'))
            : ($details->first()?->date ? Carbon::parse($details->first()->date) : null);

        return view('home', compact('invitation', 'program', 'details', 'albums', 'photos', 'date') + ['to' => $request->query('to')]);
    }
}
