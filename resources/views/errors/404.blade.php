@include('errors._page', [
    'code' => '404',
    'titre' => 'Page introuvable',
    'message' => 'La page demandée n\'existe pas ou a été déplacée.',
    'icon' => 'i-search',
    'showLogin' => true,
])
