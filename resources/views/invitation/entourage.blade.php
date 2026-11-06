<section class="paper-section entourage" id="entourage" aria-labelledby="entourage-title">
    <div class="floral-rule" aria-hidden="true"><i>✧</i></div>
    <p class="label">Our wedding party</p>
    <h2 id="entourage-title">The Entourage</h2>
    <p class="intro">With the love of our parents and the people standing beside us.</p>
    <h3 class="group-title">Our Parents</h3>
    @foreach($program['parents'] as $role => $people)
        <div class="parents"><h4 class="label">{{ $role }}</h4><p>{{ $people[0] }}<span class="amp"> & </span>{{ $people[1] }}</p></div>
    @endforeach
    <h3 class="group-title">Principal Sponsors</h3>
    <ol class="pairs">
        @foreach($program['sponsors'] as [$left, $right])
            <li><span>{{ $left }}</span><i aria-label="and">&</i><span>{{ $right }}</span></li>
        @endforeach
    </ol>
    <h3 class="group-title">Ceremonial Sponsors</h3>
    @foreach($program['ceremonial_pairs'] as $role => $pairs)
        <h4 class="label people-heading">{{ $role }}</h4>
        <ul class="pairs">@foreach($pairs as [$left, $right])<li><span>{{ $left }}</span><i aria-label="and">&</i><span>{{ $right }}</span></li>@endforeach</ul>
    @endforeach
    <h3 class="group-title">Our Attendants</h3>
    <div class="party-grid">
        @foreach($program['party'] as $role => $people)
            <div class="party-group {{ $loop->last ? 'full' : '' }}"><h4 class="label">{{ $role }}</h4><ul>@foreach($people as $person)<li>{{ $person }}</li>@endforeach</ul></div>
        @endforeach
    </div>
    <div class="parents"><h4 class="label">Pastor & officiant</h4><p>{{ $program['officiant'] }}</p></div>
</section>
