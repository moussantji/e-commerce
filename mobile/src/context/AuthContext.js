import React, {
    createContext,
    useCallback,
    useContext,
    useEffect,
    useRef,
    useState,
} from "react";
import * as SecureStore from "expo-secure-store";
import api, { setAuthToken } from "../api/client";

const AuthContext = createContext(null);
export const useAuth = () => useContext(AuthContext);

const TOKEN_KEY = "auth_token";
// Marqueur : l'utilisateur a déjà connu l'écran de connexion au moins une fois.
// Une fois posé, il n'est PAS effacé à la déconnexion → l'app passe en mode
// invité (comportement Kikuu : on ne bloque plus sur le mur de connexion).
const ONBOARDED_KEY = "has_onboarded";

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState(null);
    const [hasOnboarded, setHasOnboarded] = useState(false);
    const [loading, setLoading] = useState(true);
    // Incrémenté à la déconnexion pour forcer un remontage complet de l'app
    // (= « rechargement ») afin de repartir d'un état propre.
    const [sessionKey, setSessionKey] = useState(0);
    // Empêche une réponse /me tardive de réinstaller un utilisateur après une
    // déconnexion ou un changement de compte.
    const authRequestRef = useRef(0);

    // Restaure la session au démarrage
    useEffect(() => {
        (async () => {
            try {
                const [stored, onboarded] = await Promise.all([
                    SecureStore.getItemAsync(TOKEN_KEY),
                    SecureStore.getItemAsync(ONBOARDED_KEY),
                ]);
                if (onboarded === "1") setHasOnboarded(true);
                if (stored) {
                    // Une session existante implique que l'utilisateur a déjà
                    // dépassé l'écran d'accueil : après déconnexion il doit
                    // pouvoir continuer à naviguer en mode invité.
                    setHasOnboarded(true);
                    SecureStore.setItemAsync(ONBOARDED_KEY, "1").catch(() => {});
                    setAuthToken(stored);
                    setToken(stored);
                    // Ne bloque pas le démarrage : le profil se charge en
                    // arrière-plan. Hors ligne, l'app affiche immédiatement les
                    // produits en cache avec la session locale conservée.
                    const requestId = ++authRequestRef.current;
                    api.get("/me", { timeout: 8000 })
                        .then(({ data }) => {
                            if (authRequestRef.current === requestId) {
                                setUser(data.user);
                            }
                        })
                        .catch(async (e) => {
                            if (authRequestRef.current !== requestId) return;
                            if (e?.response?.status === 401) {
                                await SecureStore.deleteItemAsync(TOKEN_KEY);
                                setAuthToken(null);
                                setToken(null);
                                setUser(null);
                            }
                            // Erreur réseau : on garde le token pour la suite.
                        });
                }
            } catch (e) {
                await SecureStore.deleteItemAsync(TOKEN_KEY);
                setAuthToken(null);
                setToken(null);
                setUser(null);
            } finally {
                setLoading(false);
            }
        })();
    }, []);

    const markOnboarded = async () => {
        // Mise à jour immédiate de l'UI : l'écriture disque peut attendre.
        setHasOnboarded(true);
        try {
            await SecureStore.setItemAsync(ONBOARDED_KEY, "1");
        } catch (e) {
            /* ignore */
        }
    };

    // Permet, depuis le tout premier écran de connexion, de continuer sans compte.
    const continueAsGuest = async () => {
        await markOnboarded();
    };

    const persist = async (newToken, newUser) => {
        authRequestRef.current += 1;
        await SecureStore.setItemAsync(TOKEN_KEY, newToken);
        await markOnboarded();
        setAuthToken(newToken);
        setToken(newToken);
        setUser(newUser);
    };

    const login = async (email, password) => {
        const { data } = await api.post("/login", { email, password });
        await persist(data.token, data.user);
        return data;
    };

    const register = async (payload) => {
        const { data } = await api.post("/register", payload);
        await persist(data.token, data.user);
        return data;
    };

    const socialLogin = async (provider, idToken) => {
        const { data } = await api.post("/auth/social", {
            provider,
            id_token: idToken,
        });
        await persist(data.token, data.user);
        return data;
    };

    /** Échange le code court du retour OAuth Google contre un token. */
    const socialExchange = async (code) => {
        const { data } = await api.post("/auth/social/exchange", { code });
        await persist(data.token, data.user);
        return data;
    };

    const logout = async () => {
        authRequestRef.current += 1;

        // Invalide le token côté serveur sans bloquer la déconnexion locale
        // (hors ligne, l'utilisateur ne doit pas attendre 30 secondes).
        const authorization = api.defaults.headers.common.Authorization;
        api.post("/logout", null, {
            timeout: 5000,
            headers: authorization ? { Authorization: authorization } : {},
        }).catch(() => {});

        // Déconnexion locale immédiate : l'app continue en mode invité.
        setAuthToken(null);
        setToken(null);
        setUser(null);
        // Recharge l'app (remontage de la navigation) pour vider tout état
        // résiduel (paniers, écrans protégés, caches d'écran...).
        setSessionKey((k) => k + 1);

        await Promise.allSettled([
            markOnboarded(),
            SecureStore.deleteItemAsync(TOKEN_KEY),
        ]);
    };

    /** Recharge le profil si la session a été restaurée hors ligne. */
    const refreshUser = useCallback(async () => {
        if (!token) return null;
        const requestId = authRequestRef.current;
        try {
            const { data } = await api.get("/me", { timeout: 8000 });
            if (authRequestRef.current !== requestId) return null;
            setUser(data.user);
            return data.user;
        } catch (e) {
            if (authRequestRef.current !== requestId) return null;
            if (e?.response?.status === 401) {
                await SecureStore.deleteItemAsync(TOKEN_KEY);
                setAuthToken(null);
                setToken(null);
                setUser(null);
            }
            return null;
        }
    }, [token]);

    // Met à jour le profil et le state utilisateur
    const updateProfile = async (payload) => {
        const { data } = await api.put("/me", payload);
        setUser(data.user);
        return data.user;
    };

    // Remplace les données utilisateur (ex: après upload d'avatar)
    const updateUserData = (newUser) => {
        setUser(newUser);
    };

    return (
        <AuthContext.Provider
            value={{
                user,
                token,
                hasOnboarded,
                loading,
                sessionKey,
                login,
                register,
                socialLogin,
                socialExchange,
                logout,
                continueAsGuest,
                refreshUser,
                updateProfile,
                updateUserData,
            }}
        >
            {children}
        </AuthContext.Provider>
    );
}
