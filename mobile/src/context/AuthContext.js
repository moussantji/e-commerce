import React, { createContext, useContext, useEffect, useState } from "react";
import * as SecureStore from "expo-secure-store";
import api, { setAuthToken } from "../api/client";

const AuthContext = createContext(null);
export const useAuth = () => useContext(AuthContext);

const TOKEN_KEY = "auth_token";

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState(null);
    const [loading, setLoading] = useState(true);

    // Restaure la session au démarrage
    useEffect(() => {
        (async () => {
            try {
                const stored = await SecureStore.getItemAsync(TOKEN_KEY);
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

    const persist = async (newToken, newUser) => {
        await SecureStore.setItemAsync(TOKEN_KEY, newToken);
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

    const socialLogin = async (provider, accessToken) => {
        const { data } = await api.post("/auth/social", {
            provider,
            access_token: accessToken,
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
                loading,
                login,
                register,
                socialLogin,
                logout,
                updateProfile,
                updateUserData,
            }}
        >
            {children}
        </AuthContext.Provider>
    );
}
