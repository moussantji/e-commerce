<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Avis #{{ $avis->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { display: flex; justify-content: space-between; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Avis Client #{{ $avis->id }}</h1>
    </div>

    <div class="info">
        <div>
            <strong>Utilisateur:</strong> {{ $avis->user->name ?? 'N/A' }}
        </div>
        <div>
            <strong>Date:</strong> {{ $avis->created_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <h3>Produit: {{ $avis->product->name ?? 'N/A' }}</h3>

    <div>Note: {{ $avis->nb_etoiles }}/5 étoiles</div>

    <h4>Commentaire:</h4>
    <p>{{ $avis->commentaire }}</p>
</body>
</html>
