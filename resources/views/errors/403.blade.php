@include('errors._page', [
    'code' => '403',
    'titre' => 'Accès interdit',
    'message' => 'Vous n\'avez pas l\'autorisation d\'accéder à cette page.',
    'icon' => 'i-b2-lock',
    'showLogin' => true,
])
