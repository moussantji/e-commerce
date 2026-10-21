import { Image as ExpoImage } from "expo-image";
import api from "./client";

/**
 * Préchauffe le cache hors ligne avec les pages publiques les plus consultées :
 * accueil, catégories, bons plans, filtres, moyens de paiement/livraison et
 * fiches des premiers produits. Les images sont également préchargées dans le
 * cache disque d'expo-image.
 */

const PRELOAD_INTERVAL_MS = 30 * 60 * 1000; // 30 minutes
let lastPreloadAt = 0;
let running = false;

function uniqueById(products) {
    const seen = new Set();
    return (products || []).filter((product) => {
        if (!product?.id || seen.has(product.id)) return false;
        seen.add(product.id);
        return true;
    });
}

function uniqueUrls(urls) {
    return [...new Set((urls || []).filter(Boolean))];
}

async function mapLimit(items, limit, worker) {
    const queue = [...items];
    const runners = Array.from(
        { length: Math.min(limit, queue.length) },
        async () => {
            while (queue.length) {
                const item = queue.shift();
                await worker(item);
            }
        },
    );
    await Promise.all(runners);
}

async function prefetchProductDetails(products, limit = 6) {
    const ids = uniqueById(products)
        .slice(0, limit)
        .map((product) => product.id);

    await mapLimit(ids, 3, async (id) => {
        try {
            const { data } = await api.get(`/products/${id}`);
            const product = data?.data;
            if (product) {
                await ExpoImage.prefetch(
                    uniqueUrls([product.image, ...(product.images || [])]),
                    "memory-disk",
                ).catch(() => {});
            }
        } catch (e) {
            // Produit indisponible ou hors ligne : le cache existant reste utilisé.
        }
    });
}

export async function preloadCatalog({ force = false } = {}) {
    if (running) return;
    if (!force && Date.now() - lastPreloadAt < PRELOAD_INTERVAL_MS) return;

    running = true;
    let success = false;

    try {
        const responses = await Promise.all([
            // Mêmes paramètres que HomeScreen pour que le cache soit réutilisé.
            api.get("/products", { params: { per_page: 10, page: 1 } }),
            api.get("/products", { params: { featured: 1, per_page: 10 } }),
            api.get("/categories"),
            // Mêmes paramètres que l'onglet « Promos » de DealsScreen.
            api.get("/products", {
                params: {
                    on_sale: 1,
                    sort: "latest",
                    page: 1,
                    per_page: 20,
                },
            }),
        ]);

        const [home, featured, categories, deals] = responses;
        // Si une réponse vient du cache, on est hors ligne : on ne verrouille
        // pas le préchargement, il sera retenté au prochain retour au premier
        // plan une fois la connexion revenue.
        success = !responses.some((response) => response.fromCache);

        const products = uniqueById([
            ...(home.data?.data ?? []),
            ...(featured.data?.data ?? []),
            ...(deals.data?.data ?? []),
        ]);

        const categoryList = categories.data?.data ?? [];
        const imageUrls = uniqueUrls([
            ...products.map((product) => product.image),
            ...categoryList.flatMap((category) => [
                category.image,
                ...(category.banner_images || []),
            ]),
        ]).slice(0, 30);

        ExpoImage.prefetch(imageUrls, "memory-disk").catch(() => {});

        // Les fiches produits sont préchargées en petit nombre pour ne pas
        // saturer l'API au démarrage.
        await prefetchProductDetails(products, 6);

        // Autres vues publiques préchargées pour être disponibles hors ligne.
        await Promise.allSettled([
            api.get("/products", { params: { sort: "popular", per_page: 18 } }),
            // Vue par défaut de ProductListScreen.
            api.get("/products", {
                params: { page: 1, per_page: 12, sort: "latest" },
            }),
            api.get("/products/filters"),
            api.get("/search/suggestions"),
            api.get("/payment-methods"),
            api.get("/shipping-methods"),
        ]);
    } catch (e) {
        // Hors ligne ou serveur momentanément indisponible : on réessaiera au
        // prochain passage de l'application au premier plan.
    } finally {
        running = false;
        if (success) lastPreloadAt = Date.now();
    }
}
