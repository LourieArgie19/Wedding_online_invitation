<div class="wish-list" aria-label="Messages for the couple">
    @forelse($wish as $item)
        <article class="wish-message" wire:key="wish-{{ $item->id }}"><span class="wish-heart" aria-hidden="true">♡</span><blockquote>{{ $item->comment }}</blockquote><p>{{ $item->name }}</p></article>
    @empty
        <p class="no-wishes">No wedding wishes have been added yet.</p>
    @endforelse
</div>
