<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commandes;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Récupère les données de vente mensuelles
     * 
     * @param int $monthsBack Nombre de mois en arrière (0 pour le mois en cours)
     * @return array
     */
    protected function getMonthlySalesData($monthsBack = 0)
    {
        $startDate = now()->subMonths($monthsBack)->startOfMonth();
        $endDate = now()->subMonths($monthsBack)->endOfMonth();
        
        // Récupérer les ventes du mois
        $sales = Commandes::whereIn('statut', ['livre', 'expedie'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COALESCE(SUM(total), 0) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
            
        // Créer un tableau pour tous les jours du mois
        $daysInMonth = $startDate->daysInMonth;
        $result = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = $startDate->copy()->day($day)->format('Y-m-d');
            $result[] = (float) ($sales[$currentDate] ?? 0);
        }
        
        // S'assurer que le tableau contient des nombres
        return array_map('floatval', $result);
    }
    
    /**
     * Afficher le tableau de bord d'administration
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Compter les commandes par statut
        $pendingOrdersCount = Commandes::where('statut', 'en_attente')->count();
        $processingOrdersCount = Commandes::where('statut', 'traitement')->count();
        $shippedOrdersCount = Commandes::where('statut', 'expedie')->count();
        $deliveredOrdersCount = Commandes::where('statut', 'livre')->count();
        $cancelledOrdersCount = Commandes::where('statut', 'annule')->count();
        $totalOrders = $pendingOrdersCount + $processingOrdersCount + $shippedOrdersCount + $deliveredOrdersCount + $cancelledOrdersCount;

        // Récupérer toutes les commandes avec leurs relations pour le tableau
        $allOrders = Commandes::with(['user', 'paiement', 'livraison'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Filtrer les commandes en attente
        $pendingOrders = $allOrders->where('statut', 'en_attente');
        
        // Si aucune commande en attente, on prend les 5 dernières commandes
        if ($pendingOrders->isEmpty() && !$allOrders->isEmpty()) {
            $pendingOrders = $allOrders->take(5);
        }

        // Récupérer le chiffre d'affaires total
        $totalSales = Commandes::whereIn('statut', ['livre', 'expedie'])
            ->sum('total');

        // Formater le chiffre d'affaires
        $formattedTotalSales = number_format($totalSales, 2, ',', ' ') . ' FCFA';

        // Compter le nombre de nouveaux clients du mois
        $newCustomers = \App\Models\User::where('created_at', '>=', now()->subMonth())
            ->where('role', 'customer')
            ->count();

        // Compter les produits en stock
        $productsInStock = \App\Models\Produits::sum('stock');

        // Calculer les ventes mensuelles pour les 6 derniers mois
        $monthlySales = [];
        $months = collect();
        
        for ($i = 5; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();
            $monthKey = $startDate->format('Y-m');
            
            $total = Commandes::whereIn('statut', ['livre', 'expedie'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total');
                
            $monthlySales[$monthKey] = (float) $total;
            
            $months->push([
                'value' => $monthKey,
                'label' => $startDate->locale('fr')->monthName . ' ' . $startDate->year,
                'selected' => $i === 0
            ]);
        }
        
        // Récupérer les données de vente pour le graphique
        $currentMonthSales = $this->getMonthlySalesData(0); // Mois en cours
        $previousMonthSales = $this->getMonthlySalesData(1); // Mois précédent
        
        // Journal de débogage
        \Log::info('Données de vente du mois en cours:', $currentMonthSales);
        \Log::info('Données de vente du mois précédent:', $previousMonthSales);

        // Préparer les données pour la vue
        return view('admin.dashboard', [
            'totalSales' => $formattedTotalSales,
            'monthlySales' => $monthlySales,
            'months' => $months,
            'newCustomers' => $newCustomers,
            'totalOrders' => $totalOrders,
            'productsInStock' => number_format($productsInStock, 0, ',', ' '),
            'commandes' => $pendingOrders,
            'currentMonthSales' => $currentMonthSales,
            'previousMonthSales' => $previousMonthSales,
            'pendingOrders' => $pendingOrdersCount,
            'processingOrders' => $processingOrdersCount,
            'shippedOrders' => $shippedOrdersCount,
            'deliveredOrders' => $deliveredOrdersCount,
            'cancelledOrders' => $cancelledOrdersCount,
            'recentOrders' => Commandes::with('user')
                ->whereIn('statut', ['livre', 'expedie'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function($commande) {
                    return [
                        'id' => '#' . $commande->id,
                        'customer' => $commande->user->name,
                        'amount' => number_format($commande->total, 2, ',', ' ') . ' FCFA',
                        'status' => ucfirst(str_replace('_', ' ', $commande->statut)),
                        'date' => $commande->created_at->format('d/m/Y H:i')
                    ];
                })->toArray(),
            'popularProducts' => \App\Models\Produits::withCount(['commandes as sales_count' => function($query) {
                    $query->whereIn('statut', ['livre', 'expedie']);
                }])
                ->orderBy('sales_count', 'desc')
                ->take(5)
                ->get()
                ->map(function($produit) {
                    $firstImage = null;
                    if (!empty($produit->images) && is_array($produit->images) && count($produit->images) > 0) {
                        $firstImage = is_array($produit->images[0]) ? $produit->images[0] : $produit->images[0];
                    }
                    
                    return [
                        'name' => $produit->name,
                        'sales' => $produit->sales_count . ' vente' . ($produit->sales_count > 1 ? 's' : ''),
                        'status' => $produit->stock > 0 ? 'En stock' : 'Rupture',
                        'image' => $firstImage ? (is_array($firstImage) ? asset('storage/' . $firstImage['url']) : asset('storage/' . $firstImage)) : 'assets/img/products/default.jpg'
                    ];
                })->toArray()
        ]);
    }

    /**
     * Récupère les données de ventes pour un mois spécifique (AJAX)
     *
     * @param  string  $month (format: Y-m)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSalesData($month)
    {
        // Convertir le mois en objet Carbon
        $date = \Carbon\Carbon::createFromFormat('Y-m', $month);
        
        // Récupérer les données du mois sélectionné
        $currentMonthSales = $this->getMonthlySalesDataForDate($date);
        
        // Récupérer les données du mois précédent
        $previousMonthSales = $this->getMonthlySalesDataForDate($date->copy()->subMonth());
        
        // Générer les jours du mois pour les labels
        $daysInMonth = $date->daysInMonth;
        $labels = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $labels[] = $day;
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'currentMonth' => [
                    'label' => $date->locale('fr')->monthName . ' ' . $date->year,
                    'data' => $currentMonthSales
                ],
                'previousMonth' => [
                    'label' => $date->copy()->subMonth()->locale('fr')->monthName . ' ' . $date->copy()->subMonth()->year,
                    'data' => $previousMonthSales
                ]
            ]
        ]);
    }
    
    /**
     * Récupère les données de vente pour un mois spécifique
     * 
     * @param  \Carbon\Carbon  $date
     * @return array
     */
    protected function getMonthlySalesDataForDate($date)
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        
        // Récupérer les ventes du mois
        $sales = Commandes::whereIn('statut', ['livre', 'expedie'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DAY(created_at) as day, SUM(total) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();
            
        // Créer un tableau pour tous les jours du mois
        $daysInMonth = $startDate->daysInMonth;
        $result = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $result[] = (float) ($sales[$day] ?? 0);
        }
        
        return $result;
    }
}
