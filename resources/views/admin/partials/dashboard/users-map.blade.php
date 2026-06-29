@php $mapUsers = $mapUsers ?? collect(); @endphp
<div class="card mb-3">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <h5 class="mb-0"><span class="fas fa-map-marker-alt text-danger me-2"></span>Position des utilisateurs</h5>
        <div class="d-flex align-items-center gap-3 fs-9">
            <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#22c55e;"></span>
                {{ collect($mapUsers)->where('online', true)->count() }} en ligne</span>
            <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#6366f1;"></span>
                {{ collect($mapUsers)->count() }} localisés</span>
        </div>
    </div>
    <div class="card-body">
        <div id="users-map" style="height: 420px; width: 100%; border-radius: .5rem; z-index: 0;"></div>
        @if (collect($mapUsers)->isEmpty())
            <p class="text-body-tertiary text-center mt-3 mb-0 fs-9">
                Aucune position connue pour le moment. Les positions sont enregistrées à la connexion des utilisateurs
                (ou lancez <code>php artisan users:demo-locations</code> pour une démo).
            </p>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof L === 'undefined') {
                console.error('Leaflet non chargé : carte des utilisateurs indisponible.');
                return;
            }
            var el = document.getElementById('users-map');
            if (!el) return;

            var users = @json($mapUsers);

            var map = L.map(el, { scrollWheelZoom: false }).setView([7.54, -5.55], 3);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var group = (typeof L.markerClusterGroup === 'function') ? L.markerClusterGroup() : L.layerGroup();
            var bounds = [];

            users.forEach(function (u) {
                if (u.lat == null || u.lng == null) return;
                var color = u.online ? '#22c55e' : '#6366f1';
                var marker = L.circleMarker([u.lat, u.lng], {
                    radius: 8, color: '#ffffff', weight: 2, fillColor: color, fillOpacity: 0.9
                });
                var lieu = [u.ville, u.pays].filter(Boolean).join(', ');
                marker.bindPopup(
                    '<strong>' + (u.name || 'Utilisateur') + '</strong>' +
                    (u.role ? ' <span style="color:#6b7280;">(' + u.role + ')</span>' : '') +
                    (u.email ? '<br>' + u.email : '') +
                    (lieu ? '<br>' + lieu : '') +
                    (u.online
                        ? '<br><span style="color:#22c55e;font-weight:600;">● En ligne</span>'
                        : (u.last_login ? '<br><span style="color:#6b7280;">Dernière connexion : ' + u.last_login + '</span>' : ''))
                );
                group.addLayer(marker);
                bounds.push([u.lat, u.lng]);
            });

            map.addLayer(group);
            if (bounds.length) {
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 12 });
            }

            // Corrige l'affichage des tuiles si la carte démarre dans un conteneur en transition
            setTimeout(function () { map.invalidateSize(); }, 300);
        });
    </script>
@endpush
