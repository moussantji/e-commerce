@extends('admin.base')
@include('admin.tags.form')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Génération automatique du slug à partir du nom
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        if (nameInput && slugInput) {
            nameInput.addEventListener('blur', function() {
                if (!slugInput.value) {
                    slugInput.value = this.value.toLowerCase()
                        .replace(/[^\w\s-]/g, '') // Supprime les caractères spéciaux
                        .replace(/\s+/g, '-')       // Remplace les espaces par des tirets
                        .replace(/-+/g, '-');        // Évite les tirets multiples
                }
            });
        }
    });
</script>
@endpush
