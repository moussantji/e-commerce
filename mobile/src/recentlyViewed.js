import * as SecureStore from "expo-secure-store";

const KEY = "recently_viewed";
const MAX = 20;

/** Enregistre un produit consulté (en tête, sans doublon). */
export async function addRecentlyViewed(product) {
    if (!product?.id) return;
    try {
        const raw = await SecureStore.getItemAsync(KEY);
        const list = raw ? JSON.parse(raw) : [];
        const entry = {
            id: product.id,
            name: product.name,
            image: product.image,
            price: product.price,
            sale_price: product.sale_price ?? null,
        };
        const next = [entry, ...list.filter((p) => p.id !== product.id)].slice(0, MAX);
        await SecureStore.setItemAsync(KEY, JSON.stringify(next));
    } catch (e) {
        /* ignore */
    }
}

/** Retourne la liste des produits vus récemment. */
export async function getRecentlyViewed() {
    try {
        const raw = await SecureStore.getItemAsync(KEY);
        return raw ? JSON.parse(raw) : [];
    } catch (e) {
        return [];
    }
}

/** Vide l'historique. */
export async function clearRecentlyViewed() {
    try {
        await SecureStore.deleteItemAsync(KEY);
    } catch (e) {
        /* ignore */
    }
}
