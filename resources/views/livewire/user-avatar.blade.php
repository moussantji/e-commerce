<div class="col-12 col-sm-auto">
    <div x-data="{ open: false }">
        <input
            wire:model="avatarFile"
            type="file"
            class="d-none"
            id="avatarFile"
            accept="image/*">

        <label class="cursor-pointer avatar avatar-5xl" for="avatarFile">
            <img class="rounded-circle"
                 wire:ignore
                 x-ref="preview"
                 src="{{ $avatarPreview ?? $user->getPhoto()?->getImageUrl(300, 300) ?? asset('assets/img/team/15.webp') }}"
                 alt="Avatar"
                 id="avatarPreview" />
        </label>
    </div>
</div>

<script>
document.addEventListener('livewire:load', function () {
    Livewire.hook('component.initialized', (component) => {
        component.$wire.on('avatar-updated', () => {
            // Refresh parent si besoin
        });
    });
});
</script>
