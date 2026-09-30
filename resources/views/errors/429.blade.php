@include('errors._page', [
    'code' => '429',
    'titre' => 'Trop de requêtes',
    'message' => 'Vous allez un peu vite. Patientez quelques instants puis réessayez.',
    'icon' => 'i-bolt',
    'showLogin' => true,
])
