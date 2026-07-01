<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Commandes as Order;
use App\Support\OrderStatus;
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

        // Filtre par statut si présent dans la requête (normalisé pour tolérer
        // d'anciennes valeurs éventuelles dans l'URL)
        if ($request->filled('status')) {
            $query->where('statut', OrderStatus::normalize($request->status));
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

        // Récupérer les statuts disponibles pour le filtre (source unique)
        $statuses = OrderStatus::LABELS;

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

        // Normalise vers le vocabulaire canonique (en_cours -> traitement, etc.)
        $status = OrderStatus::normalize($status);

        if (! array_key_exists($status, OrderStatus::LABELS)) {
            return back()->with('error', 'Statut invalide.');
        }

        $order->update(['statut' => $status]);

        // Mise à jour des dates de suivi en fonction du statut
        $dateField = [
            OrderStatus::EN_ATTENTE => 'date_en_attente',
            OrderStatus::PAYEE => 'date_traitement',
            OrderStatus::TRAITEMENT => 'date_traitement',
            OrderStatus::EXPEDIE => 'date_expedition',
            OrderStatus::LIVRE => 'date_livraison',
            OrderStatus::ANNULE => 'date_annulation',
        ][$status] ?? null;

        if ($dateField) {
            $order->update([$dateField => now()]);
        }

        // Ici, vous pourriez ajouter une notification à l'utilisateur
        // par email ou notification push

        return back()->with('success', 'Statut de la commande mis à jour avec succès');
    }

    /**
     * L'édition d'une commande se fait via la page de détail (show) qui
     * contient le workflow de statut (confirmer paiement, préparer, expédier,
     * livrer, annuler). On y redirige pour éviter une vue d'édition dupliquée.
     */
    public function edit(Order $order)
    {
        return redirect()->route('admin.orders.show', $order);
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
