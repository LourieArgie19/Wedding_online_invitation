<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#faf8f7"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $invitation?->name ?? 'Our wedding' }} — You're invited</title>
    <meta name="description" content="An invitation to the wedding of {{ $invitation?->name ?? 'our happy couple' }}.">
    @if($invitation)<meta property="og:image" content="{{ asset('storage/'.$invitation->hero1) }}">@endif
    <link rel="icon" href="{{ asset('assets/invitation/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Allura&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@400;500;600&display=swap">
    @vite(['resources/css/invitation.css', 'resources/js/invitation.js'])
    @livewireStyles
</head>
<body>
@if($invitation)
<main class="stage">
    <section class="cover" id="envelope" aria-label="Sealed wedding invitation">
        <div class="cover-artwork">
            <div class="cover-interior" aria-hidden="true"></div>
            <div class="cover-seal-paper" aria-hidden="true"></div>
            <img class="cover-photo" src="{{ asset('assets/invitation/blush-envelope.webp') }}" alt="A white envelope sealed with dusty blue wax, surrounded by pink and white flowers" fetchpriority="high" width="750" height="1425">
            <div class="cover-flap" aria-hidden="true"><div class="cover-flap-front"></div><div class="cover-flap-back"></div></div>
            <p class="cover-names">{{ $invitation->name }}</p>
            <div class="cover-seal" aria-hidden="true"></div>
        </div>
        <div class="cover-action"><a href="#invitation" id="open-invitation">Open Invitation</a></div>
    </section>
    <nav class="invitation-nav" id="invitation-nav" aria-label="Invitation sections">
        <a class="nav-couple" href="#home" aria-label="{{ $invitation->name }} — back to top">{{ $invitation->name }}</a>
        <button hidden class="nav-menu-toggle" id="nav-menu-toggle" type="button" aria-expanded="false" aria-controls="invitation-links">
            <span>Menu</span><svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M3 5h12M3 9h12M3 13h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
        <ul class="invitation-links" id="invitation-links">
            <li><a href="#home" aria-current="location">Home</a></li>
            @if($program)<li><a href="#story">Our Story</a></li>@endif
            <li><a href="#the-day">Schedule</a></li>
            <li><a href="#attire">Attire</a></li>
            @if($program)<li><a href="#venue">Venue</a></li>@endif
            @if($photos->isNotEmpty())<li><a href="#gallery">Photos</a></li>@endif
            @if($program)<li><a href="#entourage">Entourage</a></li>@endif
            <li><a href="#faq">FAQs</a></li>
            @if($date)<li><a href="#celebration">Countdown</a></li>@endif
        </ul>
    </nav>
    <article class="scroll-paper" id="invitation" tabindex="-1" aria-label="{{ $invitation->name }} wedding invitation">
        <header class="hero" id="home">
            <img src="{{ asset('storage/'.$invitation->hero1) }}" alt="{{ $invitation->name }} together" height="900" width="675">
            <div class="hero-card"><p class="label">You’re cordially</p><b>Invited</b><small>{{ $date ? $date->format('F j, Y') : 'to our wedding' }}</small></div>
        </header>
        <section class="welcome floral" aria-labelledby="couple-title">
            <div class="floral-rule" aria-hidden="true"><i>❀</i></div>
            <h1 id="couple-title">{{ $invitation->name }}</h1>
            <p class="label">Are getting married</p>
            <div class="floral-rule" aria-hidden="true"><i>✧</i></div>
            <h2>Join Our Celebration</h2>
            @if($to)<p class="salutation">Dear {{ $to }},</p>@endif
            <p class="intro">{{ $invitation->note }}</p>
            @if($date)
            <div class="calendar" aria-label="Wedding date: {{ $date->format('F j, Y') }}">
                <h3>{{ $date->format('F') }} <small>{{ $date->year }}</small></h3>
                <div class="month-grid">@foreach(['Mo','Tu','We','Th','Fr','Sa','Su'] as $day)<b>{{ $day }}</b>@endforeach
                    @for($i=1;$i<$date->copy()->startOfMonth()->dayOfWeekIso;$i++)<span aria-hidden="true">·</span>@endfor
                    @for($d=1;$d<=$date->daysInMonth;$d++)<span @class(['marked'=>$d===$date->day])>{{ $d }}</span>@endfor
                </div>
            </div>
            @elseif($program)
                <p class="venue-stamp"><span class="label">The ceremony</span><br>{{ $program['venue'] }}<br><small>{{ $program['address'] }}</small></p>
            @endif
        </section>
        <div class="prints floral">
            @foreach(collect([$invitation->hero2, $program ? 'dy-ann-dennis/rings-in-scripture.webp' : $invitation->hero3])->filter() as $photo)
                <figure><img @class(['print-square' => $photo === 'dy-ann-dennis/together.webp']) src="{{ asset('storage/'.$photo) }}" alt="{{ $loop->first ? $invitation->name.' together' : 'A detail from the couple’s wedding album' }}" loading="lazy"></figure>
            @endforeach
        </div>
        @if($program)
            <section class="paper-section story" id="story"><h2>Our Love Story</h2>
                <div class="story-row">
                    <a class="story-photo" href="{{ asset('storage/dy-ann-dennis/by-the-stairs.webp') }}" data-gallery aria-label="Enlarge photo of {{ $invitation->name }} on the stairs">
                        <img src="{{ asset('storage/dy-ann-dennis/by-the-stairs.webp') }}" alt="{{ $invitation->name }} sharing a quiet moment" loading="lazy" width="960" height="1440">
                        <span class="photo-expand" aria-hidden="true">View photo ↗</span>
                    </a>
                    <div><h3>A Chance Encounter</h3><p>Our story began in the same IT department. He was a third-year student, while I was just in my first year. Little did I know that someone had already noticed me from a distance and secretly admired me. What started as a simple crush would soon become something more beautiful than we ever imagined.</p></div>
                </div>
                <div class="story-chapter">
                    <h3>A Simple Message, A Beautiful Beginning</h3>
                    <p>Sometimes, love begins with the simplest words. After life's unexpected turns, a shared post led to a little conversation — first a “done,” then a “kumusta?” From those simple messages, we slowly got to know each other. Even distance couldn't stop what was meant to grow. And as our story unfolded, we discovered that God's timing was far more beautiful than our plans.</p>
                </div>
                <div class="story-row story-row-reverse">
                    <a class="story-photo" href="{{ asset('storage/dy-ann-dennis/foreheads.webp') }}" data-gallery aria-label="Enlarge photo of {{ $invitation->name }} together in church">
                        <img src="{{ asset('storage/dy-ann-dennis/foreheads.webp') }}" alt="The couple together in church" loading="lazy" width="1440" height="960">
                        <span class="photo-expand" aria-hidden="true">View photo ↗</span>
                    </a>
                    <div><h3>From Two Hearts to One Promise</h3><p>What began as a secret admiration and a simple conversation became a love worth waiting for. Through every season, we learned to choose each other, grow together, and put God at the center of our relationship. Now, we are ready to turn our beautiful love story into a lifelong promise.</p></div>
                </div>
            </section>
        @endif
        <section class="paper-section schedule" id="the-day"><p class="label">Order of service</p><h2>The Schedule of Our Day</h2>
            @if($date)<p><time datetime="{{ $date->toDateString() }}">{{ $date->format('l, F j, Y') }}</time></p>@endif
            @if($program)
            <ol class="timeline">
                <li><time datetime="{{ $program['arrival'] }}">{{ \Illuminate\Support\Carbon::parse($program['arrival'])->format('g:i A') }}</time><div><h3>Guest arrival</h3><p>Please join us at Lourdes Mills and settle in before the service begins.</p></div></li>
                <li><time datetime="{{ $program['ceremony'] }}">{{ \Illuminate\Support\Carbon::parse($program['ceremony'])->format('g:i A') }}</time><div><h3>Wedding ceremony</h3><p>{{ $invitation->name }} exchange vows, with Pastor {{ $program['officiant'] }} officiating.</p></div></li>
            </ol>
            @else
                @forelse($details as $event)<p>{{ \Illuminate\Support\Carbon::parse($event->date)->format('g:i A') }} · {{ $event->address }}</p>@empty<p>Our wedding day details will be shared here soon.</p>@endforelse
            @endif
        </section>
        <section class="paper-section motif floral" id="attire"><h2>Attire & Color Motif</h2><p>We’d love to see you in pink, white, and dusty blue. Choose a shade you love and celebrate with us.</p>
            <ul class="palette">@foreach(config('invitation.colors') as $color=>$hex)<li><span style="--color:{{ $hex }}"></span>{{ $color }}</li>@endforeach</ul>
        </section>
        @if($program)
        <section class="paper-section location" id="venue"><h2>Location & Map</h2><p class="label">The ceremony</p><h3>{{ $program['venue'] }}</h3><p>{{ $program['address'] }}</p>
            <a class="pill" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($program['venue'].', '.$program['address'].', Philippines') }}">View on map <span aria-hidden="true">↗</span></a>
        </section>
        @endif
        @if($photos->isNotEmpty())
        <section class="album" id="gallery"><h2>Our Photo Album</h2><p>A few moments we’ll always treasure.</p>
            <div class="album-grid">@foreach($photos as $photo)<a href="{{ asset('storage/'.$photo) }}" data-gallery aria-label="Enlarge wedding photo {{ $loop->iteration }}"><img src="{{ asset('storage/'.$photo) }}" alt="{{ $invitation->name }}’s wedding album, photograph {{ $loop->iteration }}" loading="lazy"></a>@endforeach</div>
        </section>
        @endif
        @if($program) @include('invitation.entourage') @endif
        <section class="paper-section faq" id="faq"><h2>Good to Know</h2>
            @if($program)<details><summary>What time should I arrive?</summary><p>Arrive at 3:00 PM for the 3:30 PM ceremony. Times are local to Tomas Oppus, Philippines.</p></details>@endif
            <details><summary>What colors are the motif?</summary><p>Blush pink and white, with dusty blue accents.</p></details>
        </section>
        <footer @class(['closing', 'floral', 'closing-with-photo' => (bool) $invitation->hero3]) id="celebration" aria-labelledby="closing-title">
            @if($invitation->hero3)
                <figure class="closing-portrait"><img src="{{ asset('storage/'.$invitation->hero3) }}" alt="{{ $invitation->name }}" loading="lazy" width="675" height="900"></figure>
            @endif
            <div class="closing-details">
                <p class="label">With love & grateful hearts</p>
                <h2 id="closing-title">{{ $invitation->name }}</h2>
                <p class="closing-note">We can’t wait to share this beautiful day with you.</p>
                <div class="closing-date">
                    <span aria-hidden="true">❦</span>
                    @if($date)<time datetime="{{ $date->toDateString() }}">{{ $date->format('F j, Y') }}</time>@endif
                    @if($program)<p>{{ $program['venue'] }}<br><small>{{ $program['address'] }}</small></p>@endif
                </div>
                @if($date)
                <div class="closing-countdown" role="group" aria-labelledby="countdown-title">
                    <h3 id="countdown-title">Until our forever</h3>
                    <div class="countdown" data-countdown="{{ $date->toIso8601String() }}">@foreach(['days','hours','minutes','seconds'] as $unit)<div><strong data-unit="{{ $unit }}">00</strong><small>{{ $unit }}</small></div>@endforeach</div>
                </div>
                @endif
            </div>
            <p class="end-mark"><a href="#home">Back to the beginning <span aria-hidden="true">↑</span></a></p>
        </footer>
    </article>
    @if($program)
    <audio id="invitation-music" loop preload="none" aria-hidden="true">
        <source src="{{ asset('storage/music/'.rawurlencode('Moses Bliss - For Life [Official Video].mp3')) }}" type="audio/mpeg">
    </audio>
    <button hidden type="button" id="music-toggle" class="music-toggle" aria-label="Play music" aria-pressed="false" title="For Life — Moses Bliss">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18V5l12-2v13M9 9l12-2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><ellipse cx="6" cy="18" rx="3" ry="2.5" stroke="currentColor" stroke-width="1.6"/><ellipse cx="18" cy="16" rx="3" ry="2.5" stroke="currentColor" stroke-width="1.6"/></svg>
        <span data-music-label>Play music</span>
    </button>
    @endif
    <button hidden type="button" id="scroll-toggle" class="scroll-toggle" aria-label="Start automatic scrolling" title="Play or pause automatic scrolling" aria-pressed="false">▷</button>
</main>
<dialog id="photo-dialog" class="photo-dialog" aria-label="Wedding photo viewer"><button type="button" class="dialog-close" aria-label="Close photo">×</button><button type="button" class="dialog-previous" aria-label="Previous photo">←</button><img alt="Enlarged wedding photo"><button type="button" class="dialog-next" aria-label="Next photo">→</button><p class="photo-counter"></p></dialog>
@else
    <main class="empty"><h1>Our Wedding</h1><p>The invitation is being prepared. Please check back soon.</p></main>
@endif
@livewireScripts
</body>
</html>
