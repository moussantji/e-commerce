import * as FileSystem from "expo-file-system";

/**
 * Cache persistant des réponses GET publiques.
 *
 * Objectif : lorsque le téléphone n'a plus de connexion, l'application peut
 * continuer à afficher les produits, catégories et fiches déjà consultés
 * (ou préchargés) au lieu d'un écran d'erreur.
 *
 * Seules les données du catalogue sont stockées : jamais les données du compte
 * (commandes, portefeuille, favoris, notifications...).
 */

const CACHE_VERSION = 1;
const CACHE_DIR = `${FileSystem.documentDirectory || ""}api-cache-v${CACHE_VERSION}/`;
const MAX_AGE_MS = 30 * 24 * 60 * 60 * 1000; // 30 jours
const MAX_ENTRIES = 160;
const HAS_FILE_SYSTEM = Boolean(FileSystem.documentDirectory);

const memory = new Map();
let dirPromise = null;
let writeCount = 0;
let pruning = false;

function ensureDir() {
    if (!HAS_FILE_SYSTEM) return Promise.resolve();
    if (!dirPromise) {
        dirPromise = FileSystem.makeDirectoryAsync(CACHE_DIR, {
            intermediates: true,
        }).catch(() => {
            // Le dossier existe déjà ou n'est pas accessible : les lectures et
            // écritures échoueront proprement, le cache mémoire reste utilisable.
        });
    }
    return dirPromise;
}

function fileFor(key) {
    return `${CACHE_DIR}${key}.json`;
}

function remember(key, data, savedAt) {
    memory.set(key, { data, savedAt });
    while (memory.size > MAX_ENTRIES) {
        const oldest = memory.keys().next().value;
        memory.delete(oldest);
    }
}

/** Sérialisation stable : le même objet params donne toujours la même clé. */
function stableStringify(value) {
    if (value === undefined) return "undefined";
    if (value === null || typeof value !== "object") {
        return JSON.stringify(value);
    }
    if (Array.isArray(value)) {
        return `[${value
            .filter((item) => item !== undefined)
            .map(stableStringify)
            .join(",")}]`;
    }
    return `{${Object.keys(value)
        .filter((key) => value[key] !== undefined)
        .sort()
        .map((key) => `${JSON.stringify(key)}:${stableStringify(value[key])}`)
        .join(",")}}`;
}

/** Clé courte et stable pour un couple URL + paramètres. */
export function cacheKey(method, url, params, baseURL = "") {
    const raw = `${String(method || "get").toUpperCase()} ${baseURL}${url || ""}?${stableStringify(
        params || {},
    )}`;

    // FNV-1a 32 bits + longueur : suffisant pour distinguer les requêtes d'un
    // catalogue et garder des noms de fichiers courts.
    let hash = 2166136261;
    for (let i = 0; i < raw.length; i += 1) {
        hash ^= raw.charCodeAt(i);
        hash = Math.imul(hash, 16777619);
    }

    return `${(hash >>> 0).toString(16)}-${raw.length.toString(16)}`;
}

export async function readCache(key) {
    const cached = memory.get(key);
    if (cached && Date.now() - cached.savedAt <= MAX_AGE_MS) {
        return cached;
    }

    if (!HAS_FILE_SYSTEM) return null;

    try {
        await ensureDir();
        const path = fileFor(key);
        const info = await FileSystem.getInfoAsync(path);
        if (!info.exists) return null;

        const raw = await FileSystem.readAsStringAsync(path);
        const parsed = JSON.parse(raw);
        if (
            parsed?.version !== CACHE_VERSION ||
            typeof parsed.savedAt !== "number" ||
            Date.now() - parsed.savedAt > MAX_AGE_MS
        ) {
            FileSystem.deleteAsync(path, { idempotent: true }).catch(() => {});
            return null;
        }

        remember(key, parsed.data, parsed.savedAt);
        return { data: parsed.data, savedAt: parsed.savedAt };
    } catch (e) {
        return null;
    }
}

export async function writeCache(key, data) {
    const savedAt = Date.now();
    remember(key, data, savedAt);

    if (!HAS_FILE_SYSTEM) return;

    try {
        await ensureDir();
        await FileSystem.writeAsStringAsync(
            fileFor(key),
            JSON.stringify({ version: CACHE_VERSION, savedAt, data }),
        );

        writeCount += 1;
        if (writeCount % 20 === 0) {
            pruneCache();
        }
    } catch (e) {
        // Le cache mémoire suffit pour la session en cours.
    }
}

/** Supprime les plus anciennes entrées quand le cache devient trop gros. */
async function pruneCache() {
    if (!HAS_FILE_SYSTEM || pruning) return;
    pruning = true;

    try {
        await ensureDir();
        const files = await FileSystem.readDirectoryAsync(CACHE_DIR);
        if (files.length <= MAX_ENTRIES) return;

        const entries = await Promise.all(
            files.map(async (name) => {
                const path = `${CACHE_DIR}${name}`;
                const info = await FileSystem.getInfoAsync(path);
                return {
                    path,
                    modificationTime: info.modificationTime || 0,
                };
            }),
        );

        entries.sort((a, b) => a.modificationTime - b.modificationTime);
        const toDelete = entries.slice(0, entries.length - MAX_ENTRIES);
        await Promise.all(
            toDelete.map((entry) =>
                FileSystem.deleteAsync(entry.path, { idempotent: true }).catch(
                    () => {},
                ),
            ),
        );
    } catch (e) {
        // Nettoyage best-effort : on ne bloque jamais l'application.
    } finally {
        pruning = false;
    }
}

/** Utilisé par l'écran Paramètres > Vider le cache. */
export async function clearApiCache() {
    memory.clear();

    if (!HAS_FILE_SYSTEM) return;

    try {
        await FileSystem.deleteAsync(CACHE_DIR, { idempotent: true });
    } catch (e) {
        // ignore
    } finally {
        dirPromise = null;
    }
}
