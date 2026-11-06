<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicInvitationOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('invitation.public_only')) {
            $isPublicPage = $request->is('/', 'up', 'livewire/livewire.js', 'livewire/livewire.min.js');
            $isReadRequest = $request->isMethod('GET') || $request->isMethod('HEAD');

            abort_unless($isPublicPage && $isReadRequest, 404);
        }

        return $next($request);
    }
}
