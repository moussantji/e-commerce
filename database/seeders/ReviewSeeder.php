<?php

namespace Database\Seeders;

use App\Models\AvisClient;
use App\Models\Produits;
use App\Models\User;
use App\Models\Commandes;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        
        // Récupérer les utilisateurs clients
        $users = User::where('role', 'customer')->get();
        if ($users->isEmpty()) {
            $this->command->warn('Aucun utilisateur client trouvé. Créez d\'abord des utilisateurs.');
            return;
        }
        
        // Récupérer les produits avec des commandes
        $products = Produits::has('commandes')->with('commandes.user')->get();
        if ($products->isEmpty()) {
            $this->command->warn('Aucun produit avec des commandes trouvé. Créez d\'abord des commandes.');
            return;
        }
        
        // Pour chaque produit, créer entre 0 et 5 avis
        foreach ($products as $product) {
            $reviewCount = $faker->numberBetween(0, 5);
            
            // Récupérer les utilisateurs qui ont acheté ce produit
            $buyers = $product->commandes->pluck('user')->unique();
            
            // Sélectionner aléatoirement des acheteurs pour laisser un avis
            $selectedBuyers = $buyers->random(min($reviewCount, $buyers->count()));
            
            foreach ($selectedBuyers as $user) {
                // Vérifier si l'utilisateur a déjà laissé un avis pour ce produit
                $existingReview = AvisClient::where('user_id', $user->id)
                    ->where('produits_id', $product->id)
                    ->exists();
                
                if (!$existingReview) {
                    // Générer une date d'achat aléatoire (dans les 6 derniers mois)
                    $purchaseDate = $faker->dateTimeBetween('-6 months', 'now');
                    
                    // Générer une date de publication (après la date d'achat)
                    $publishDate = (clone $purchaseDate)->modify('+' . $faker->numberBetween(1, 14) . ' days');
                    
                    // Générer une note (entre 1 et 5 étoiles, avec une distribution plus élevée vers les notes positives)
                    $rating = $this->generateRating($faker);
                    
                    // Générer un titre et un commentaire en fonction de la note
                    $reviewData = $this->generateReviewContent($rating, $faker);
                    
                    // Créer l'avis
                    AvisClient::create([
                        'user_id' => $user->id,
                        'produits_id' => $product->id,
                        'note' => $rating,
                        'commentaire' => $reviewData['commentaire'],
                        'nb_etoiles' => $rating,
                        'created_at' => $publishDate,
                        'updated_at' => $publishDate,
                    ]);
                }
            }
        }
        
        $this->command->info('Avis clients créés avec succès !');
    }
    
    /**
     * Génère une note avec une distribution plus élevée vers les notes positives.
     */
    protected function generateRating($faker)
    {
        // Distribution des probabilités pour chaque note (1 à 5)
        // Plus de poids pour les notes 4 et 5
        $weights = [10, 15, 20, 30, 25]; // Somme = 100%
        $random = $faker->numberBetween(1, array_sum($weights));
        
        $cumulative = 0;
        for ($i = 1; $i <= 5; $i++) {
            $cumulative += $weights[$i - 1];
            if ($random <= $cumulative) {
                return $i;
            }
        }
        
        return 5; // Valeur par défaut
    }
    
    /**
     * Génère un titre et un commentaire en fonction de la note.
     */
    protected function generateReviewContent($rating, $faker)
    {
        $titles = [
            1 => [
                'Déçu par ce produit',
                'Ne correspond pas à mes attentes',
                'Produit défectueux',
                'Je ne recommande pas',
                'Très mauvais achat'
            ],
            2 => [
                'Peu satisfait',
                'Bof...',
                'Peu convaincu',
                'Peut mieux faire',
                'Décevant'
            ],
            3 => [
                'Correct',
                'Moyen',
                'Sans plus',
                'Peut convenir',
                'Ni déçu ni ravi'
            ],
            4 => [
                'Très bon produit',
                'Je recommande',
                'Correspond à mes attentes',
                'Ravi de mon achat',
                'Excellent rapport qualité/prix'
            ],
            5 => [
                'Parfait !',
                'Je recommande vivement',
                'Au-delà de mes attentes',
                'Exceptionnel',
                'Le meilleur achat que j\'ai fait depuis longtemps'
            ]
        ];
        
        $comments = [
            1 => [
                'Je suis très déçu par ce produit qui ne correspond pas du tout à la description. La qualité est médiocre et je ne le recommande vraiment pas.',
                'Produit reçu cassé. Le SAV est injoignable. Je ne ferai plus jamais confiance à cette marque.',
                'Très mauvais rapport qualité/prix. Je m\'attendais à beaucoup mieux pour ce prix-là.',
                'Après seulement quelques jours d\'utilisation, le produit est tombé en panne. Service client peu réactif.',
                'Je ne comprends pas les avis positifs. Ce produit est tout simplement inutilisable tel qu\'il est.'
            ],
            2 => [
                'Le produit est correct mais pas exceptionnel. Je m\'attendais à mieux pour le prix.',
                'Fonctionne mais avec des limitations. Pas aussi performant qu\'annoncé.',
                'La qualité est moyenne. Ça fait l\'affaire mais je ne suis pas sûr que ça tienne dans le temps.',
                'Assez déçu par certains aspects, notamment la finition qui laisse à désirer.',
                'Pas convaincu par ce produit. Je ne pense pas racheter de cette marque.'
            ],
            3 => [
                'Produit correct sans plus. Rien d\'exceptionnel mais il remplit sa fonction.',
                'Ni déçu ni ravi. Ça fait le job mais sans plus.',
                'Moyen. Je m\'attendais à mieux mais c\'est utilisable.',
                'Le produit est correct pour le prix demandé. Rien d\'exceptionnel mais ça peut dépanner.',
                'Environnement correct mais quelques défauts qui gâchent le produit.'
            ],
            4 => [
                'Très bon produit, je recommande. Correspond parfaitement à mes attentes.',
                'Achat satisfaisant. Le produit est de bonne qualité et fonctionne très bien.',
                'Je suis ravi de mon achat. Rapport qualité/prix intéressant.',
                'Très content de ce produit qui répond parfaitement à mes besoins. Je recommande !',
                'Produit de qualité, livraison rapide. Je suis satisfait de mon achat.'
            ],
            5 => [
                'Tout simplement parfait ! Je recommande vivement ce produit de grande qualité.',
                'Meilleur achat de l\'année ! Le produit dépasse toutes mes attentes. Je suis plus que ravi !',
                'Exceptionnel ! La qualité est au rendez-vous. Je ne peux que recommander ce produit.',
                'Incroyable produit qui a changé ma vie. Je ne pourrais plus m\'en passer !',
                'Tout simplement le meilleur produit que j\'ai acheté depuis longtemps. Qualité irréprochable.'
            ]
        ];
        
        $title = $faker->randomElement($titles[$rating]);
        $comment = $faker->randomElement($comments[$rating]);
        
        // Ajouter éventuellement des détails supplémentaires au commentaire
        if ($faker->boolean(70)) {
            $additionalDetails = [
                ' La livraison a été rapide et le produit bien emballé.',
                ' Le service client a été réactif à mes questions.',
                ' Petit bémol sur les délais de livraison qui ont été plus longs qu\'annoncé.',
                ' Je recommande ce vendeur sérieux.',
                ' Je renouvellerai mon achat sans hésiter.',
                ' Dommage que l\'emballage ne soit pas plus écologique.',
                ' Petit plus pour la notice d\'utilisation très claire.',
                ' Je suis ravi de mon achat après plusieurs semaines d\'utilisation.',
                ' Le produit est encore plus beau en vrai que sur les photos.',
                ' Parfait pour un cadeau, l\'emballage cadeau était un vrai plus.'
            ];
            
            $comment .= $faker->randomElement($additionalDetails);
        }
        
        return [
            'titre' => $title,
            'commentaire' => $comment
        ];
    }
}
