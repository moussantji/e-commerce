@include('errors._page', [
    'code' => '500',
    'titre' => 'Erreur serveur',
    'message' => 'Un problème est survenu. Nos équipes sont prévenues, réessayez dans un moment.',
    'icon' => 'i-b2-alert',
    'showLogin' => true,
])
