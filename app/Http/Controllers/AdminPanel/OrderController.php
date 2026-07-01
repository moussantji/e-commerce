<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Commandes as Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes
     */
    public function index(Request $request)
    {
        // Nettoyer les paramètres vides avant tout traitement
        $params = array_filter($request->query(), function($value) {
            return $value !== '' && $value !== null;
        });

        // Rediriger si des paramètres vides ont été supprimés
        if (count($params) !== count($request->query())) {
            return redirect()->route('admin.orders.index', $params);
        }

        // Si on arrive ici, l'URL est propre, on peut traiter la requête
        $query = Order::with(['user', 'produits'])
            ->latest('created_at');

        // Filtre par statut si présent dans la requête
        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        // Filtre par recherche si présent
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_commande', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $commandes = $query->get();

        // Récupérer les statuts disponibles pour le filtre
        $statuses = [
            'en_attente' => 'En attente',
            'traitement' => 'En traitement',
            'expedie' => 'Expédié',
            'livre' => 'Livré',
            'annule' => 'Annulé'
        ];

        return view('admin.orders.index', compact('commandes', 'statuses'));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        $order->load(['user', 'produits', 'paiement', 'livraison', 'promoCode']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Accept either 'status' (views) or 'statut' (other callers)
        $status = $request->input('status') ?? $request->input('statut');
        if (! $status) {
            return back()->with('error', 'Statut requis.');
        }

        $allowed = [
            'en_attente', 'en_traitement', 'traitement', 'expediee', 'expédition',
            'expedie', 'en_cours', 'livree', 'livre', 'annulee', 'annule', 'payee'
        ];
        if (! in_array($status, $allowed, true)) {
            return back()->with('error', 'Statut invalide.');
        }

        $order->update(['statut' => $status]);

        // Mise à jour des dates en fonction du statut
        $now = now();
        $updates = [];
        switch ($status) {
            case 'en_attente':
                $updates['date_en_attente'] = $now;
                break;
            case 'en_traitement':
            case 'traitement':
            case 'en_cours':
                $updates['date_traitement'] = $now;
                break;
            case 'expediee':
            case 'expedie':
                $updates['date_expedition'] = $now;
                break;
            case 'livree':
            case 'livre':
                $updates['date_livraison'] = $now;
                break;
            case 'annulee':
            case 'annule':
                $updates['date_annulation'] = $now;
                break;
            case 'payee':
                $updates['date_traitement'] = $now;
                break;
        }

        if (! empty($updates)) {
            $order->update($updates);
        }

        // Ici, vous pourriez ajouter une notification à l'utilisateur
        // par email ou notification push

        return back()->with('success', 'Statut de la commande mis à jour avec succès');
    }

    /**
     * Affiche le formulaire d'édition d'une commande
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'produits', 'paiement', 'livraison', 'promoCode']);
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Met à jour une commande
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'adresse_livraison' => 'required|array',
            'adresse_facturation' => 'required|array',
            'notes' => 'nullable|string',
            'frais_livraison' => 'required|numeric|min:0',
            'remise' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0'
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Commande mise à jour avec succès');
    }

    /**
     * Affiche l'historique des commandes
     */
    public function history(Order $order)
    {
        // Dans le modèle Commandes, vous devriez ajouter une relation pour l'historique
        // Par exemple: $order->historique()
        // Pour l'instant, nous retournons un tableau vide
        $history = collect([
            [
                'date' => $order->updated_at,
                'statut' => $order->statut,
                'utilisateur' => 'Système',
                'commentaire' => 'Commande ' . $order->statut
            ]
        ]);

        return view('admin.orders.history', compact('order', 'history'));
    }

    /**
     * Génère une facture PDF pour une commande
     */
    public function invoice(Order $order)
    {
        $order->load(['user', 'produits', 'paiement', 'livraison', 'promoCode']);
        // Ici, vous pourriez utiliser un package comme barryvdh/laravel-dompdf
        // pour générer un PDF de la facture
        // $pdf = \PDF::loadView('admin.orders.invoice', compact('order'));
        // return $pdf->download('facture-' . $order->id . '.pdf');

        // Pour l'instant, on retourne simplement la vue
        return view('admin.orders.invoice', compact('order'));
    }
}
