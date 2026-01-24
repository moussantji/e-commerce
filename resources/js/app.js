
document.addEventListener('livewire:load', function () {
    Livewire.hook('morph.updated', () => {
        if (typeof feather !== 'undefined') {
            feather.replace(); // Re-init icônes
        }
    });
});
