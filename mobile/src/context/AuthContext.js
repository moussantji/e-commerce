import React, { createContext, useContext, useEffect, useState } from "react";
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
                    setAuthToken(stored);
                    setToken(stored);
                    const { data } = await api.get("/me");
                    setUser(data.user);
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
        try {
            await SecureStore.setItemAsync(ONBOARDED_KEY, "1");
        } catch (e) {
            /* ignore */
        }
        setHasOnboarded(true);
    };

    // Permet, depuis le tout premier écran de connexion, de continuer sans compte.
    const continueAsGuest = async () => {
        await markOnboarded();
    };

    const persist = async (newToken, newUser) => {
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

    const logout = async () => {
        try {
            await api.post("/logout");
        } catch (e) {
            // on déconnecte localement même si l'appel échoue
        }
        await SecureStore.deleteItemAsync(TOKEN_KEY);
        setAuthToken(null);
        setToken(null);
        setUser(null);
        // Recharge l'app (remontage de la navigation) pour vider tout état
        // résiduel (paniers, écrans protégés, caches d'écran...).
        setSessionKey((k) => k + 1);
    };

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
                logout,
                continueAsGuest,
                updateProfile,
                updateUserData,
            }}
        >
            {children}
        </AuthContext.Provider>
    );
}
