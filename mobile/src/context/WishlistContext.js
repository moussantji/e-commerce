import React, {
    createContext,
    useContext,
    useState,
    useCallback,
    useEffect,
} from "react";
import api from "../api/client";
import { useAuth } from "./AuthContext";

const WishlistContext = createContext(null);
export const useWishlist = () => useContext(WishlistContext);

export function WishlistProvider({ children }) {
    const { token } = useAuth();
    const [ids, setIds] = useState([]); // ids des produits favoris

    // Charge les favoris de l'utilisateur (au login / démarrage)
    const refresh = useCallback(async () => {
        if (!token) {
            setIds([]);
            return;
        }
        try {
            const { data } = await api.get("/wishlist");
            setIds((data.data ?? []).map((p) => p.id));
        } catch (e) {
            /* ignore */
        }
    }, [token]);

    useEffect(() => {
        refresh();
    }, [refresh]);

    const isFav = useCallback((productId) => ids.includes(productId), [ids]);

    // Bascule un favori (optimiste + API). Retourne le nouvel état.
    const toggle = useCallback(
        async (productId) => {
            if (!token) return null;
            const wasFav = ids.includes(productId);
            // Optimiste
            setIds((prev) =>
                wasFav ? prev.filter((x) => x !== productId) : [...prev, productId],
            );
            try {
                const { data } = await api.post(`/wishlist/${productId}`);
                setIds((prev) => {
                    const has = prev.includes(productId);
                    if (data.favorited && !has) return [...prev, productId];
                    if (!data.favorited && has) return prev.filter((x) => x !== productId);
                    return prev;
                });
                return data.favorited;
            } catch (e) {
                // rollback
                setIds((prev) =>
                    wasFav ? [...prev, productId] : prev.filter((x) => x !== productId),
                );
                throw e;
            }
        },
        [token, ids],
    );

    return (
        <WishlistContext.Provider value={{ ids, isFav, toggle, refresh }}>
            {children}
        </WishlistContext.Provider>
    );
}
