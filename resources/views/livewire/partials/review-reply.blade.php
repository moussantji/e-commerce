@foreach ($repliesByParent->get($parentId, collect()) as $reply)
    @php
        $replyAuthor = optional($reply->user)->name ?? 'Client';
        $replyRole = optional($reply->user)->role;
        $replyBadge = $replyRole === 'admin' ? 'Admin' : ($replyRole === 'vendeur' ? 'Vendeur' : null);
    @endphp
    <div class="rreply" wire:key="review-reply-{{ $reply->id }}">
        <div class="rreply-main">
            <span class="rav">{{ mb_strtoupper(mb_substr(trim($replyAuthor), 0, 1)) }}</span>
            <div class="rreply-body">
                <div class="rreply-head">
                    <b>{{ $replyAuthor }}</b>
                    @if ($replyBadge)
                        <span class="rbadge">{{ $replyBadge }}</span>
                    @endif
                    <small>{{ optional($reply->created_at)->format('d/m/Y H:i') }}</small>
                </div>
                <p>{{ $reply->message }}</p>

                @auth
                    <button type="button" class="rreply-btn"
                        wire:click="startReply({{ $reviewId }}, {{ $reply->id }})">
                        <svg class="ic" style="width:12px;height:12px"><use href="#i-reply" /></svg>
                        Répondre
                    </button>
                @endauth

                @if ((int) $replyingToReview === (int) $reviewId && (int) $replyingToReply === (int) $reply->id)
                    @include('livewire.partials.review-reply-form', [
                        'replyToName' => $replyingToName,
                    ])
                @endif
            </div>
        </div>
    </div>

    {{-- Les réponses aux réponses restent au même niveau visuel (niveau 1). --}}
    @include('livewire.partials.review-reply', [
        'repliesByParent' => $repliesByParent,
        'parentId' => $reply->id,
        'reviewId' => $reviewId,
    ])
@endforeach
