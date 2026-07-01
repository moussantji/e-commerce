import * as SecureStore from "expo-secure-store";

const KEY = "read_notifications";
const MAX = 300;

/** Récupère l'ensemble des IDs de notifications déjà lues (persisté). */
export async function getReadIds() {
    try {
        const raw = await SecureStore.getItemAsync(KEY);
        return raw ? JSON.parse(raw) : [];
    } catch (e) {
        return [];
    }
}

/** Ajoute un/des IDs à l'ensemble des notifications lues. */
export async function addReadIds(ids) {
    const list = Array.isArray(ids) ? ids : [ids];
    try {
        const current = await getReadIds();
        const next = Array.from(new Set([...current, ...list.map(String)])).slice(-MAX);
        await SecureStore.setItemAsync(KEY, JSON.stringify(next));
        return next;
    } catch (e) {
        return [];
    }
}
