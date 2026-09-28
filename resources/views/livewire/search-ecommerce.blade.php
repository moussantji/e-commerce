<div class="s-box">
<form wire:submit.prevent="search" style="position:relative">
<svg class="ic s-ic"><use href="#i-search"/></svg>
<input class="s-input" type="search" wire:model.live.debounce.150ms="search" placeholder="produits, catégories, marques..." aria-label="Rechercher">
@if($search)
<button type="button" class="s-clear" wire:click="clear" aria-label="Effacer">×</button>
@endif
</form>
@if(!empty($suggestions))
<div class="s-list">
@foreach($suggestions as $suggestion)
<a href="{{ $suggestion['url'] }}" class="s-row">
<span class="t"><span class="n">{{ $suggestion['title'] }}</span><span class="k">{{ $suggestion['type'] }}{{ isset($suggestion['count']) && $suggestion['count'] > 0 ? ' · ' . $suggestion['count'] . ' produits' : '' }}</span></span>
<svg class="ic ic-sm" style="color:#8b5cf6"><use href="#i-chevron"/></svg>
</a>
@endforeach
</div>
@endif
</div>
