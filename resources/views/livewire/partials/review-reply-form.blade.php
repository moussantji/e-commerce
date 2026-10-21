<form class="rreply-form" wire:submit="submitReply"
    wire:key="reply-form-{{ $replyingToReview }}-{{ $replyingToReply ?? 'root' }}">
    <div class="rreply-form-head">
        <svg class="ic" style="width:14px;height:14px"><use href="#i-reply" /></svg>
        <span>Répondre à <b>{{ $replyToName !== '' ? $replyToName : 'cet avis' }}</b></span>
    </div>
    <textarea class="ctrl" rows="2" wire:model="replyMessage"
        placeholder="Écrivez votre réponse…" maxlength="1000"></textarea>
    @error('replyMessage')
        <span class="avis-err">{{ $message }}</span>
    @enderror
    <div class="rreply-actions">
        <button type="submit" class="btn-solid" wire:loading.attr="disabled" wire:target="submitReply">
            <span wire:loading.remove wire:target="submitReply">Publier</span>
            <span wire:loading wire:target="submitReply">Publication…</span>
        </button>
        <button type="button" class="rreply-cancel" wire:click="cancelReply">Annuler</button>
    </div>
</form>
