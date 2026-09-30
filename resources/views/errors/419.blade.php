@include('errors._page', [
    'code' => '419',
    'titre' => 'Session expirée',
    'message' => 'Votre session a expiré. Rechargez la page et réessayez.',
    'icon' => 'i-b2-info',
    'showLogin' => true,
])
