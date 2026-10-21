import React, { useState } from "react";
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    Alert,
    ActivityIndicator,
    NativeModules,
    TurboModuleRegistry,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import * as WebBrowser from "expo-web-browser";
import * as Linking from "expo-linking";
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";
import { GOOGLE_CLIENT_IDS, SITE_URL } from "../config";

// ⚠️ Pas d'import statique de @react-native-google-signin/google-signin :
// son code appelle TurboModuleRegistry.getEnforcing('RNGoogleSignin') dès
// son chargement, ce qui crashe l'app dans Expo Go (module natif absent).
function isNativeGoogleAvailable() {
    try {
        if (TurboModuleRegistry?.get?.("RNGoogleSignin")) return true;
        if (NativeModules?.RNGoogleSignin) return true;
        return false;
    } catch {
        return false;
    }
}

function loadNativeGoogle() {
    // eslint-disable-next-line no-undef
    const m = require("@react-native-google-signin/google-signin");
    return { GoogleSignin: m.GoogleSignin, statusCodes: m.statusCodes };
}

const WEB_CLIENT_ID = GOOGLE_CLIENT_IDS.web || GOOGLE_CLIENT_IDS.expo || "";

// Configuration paresseuse du SDK natif (jamais appelée dans Expo Go).
let googleConfigured = false;
function ensureGoogleConfigured(GoogleSignin) {
    if (googleConfigured) return;
    GoogleSignin.configure({
        webClientId: WEB_CLIENT_ID,
        iosClientId: GOOGLE_CLIENT_IDS.ios || undefined,
        offlineAccess: false,
    });
    googleConfigured = true;
}

/**
 * Connexion native (development build / APK).
 * Retourne false si l'utilisateur annule.
 */
async function signInWithNative(socialLogin) {
    const { GoogleSignin } = loadNativeGoogle();
    ensureGoogleConfigured(GoogleSignin);
    await GoogleSignin.hasPlayServices({
        showPlayServicesUpdateDialog: true,
    });

    const result = await GoogleSignin.signIn();

    if (result?.type === "cancelled") {
        return false;
    }

    // SDK v13+: { type, data: { idToken, ... } } ; anciennes: { idToken, ... }
    const idToken = result?.data?.idToken ?? result?.idToken;
    if (!idToken) {
        throw new Error("idToken introuvable");
    }

    await socialLogin("google", idToken);
    return true;
}

/**
 * Flux navigateur via le backend (Expo Go, web, ou secours du natif).
 * Le backend fait l'échange OAuth avec le client web et renvoie un code
 * court à l'app, échangé ensuite contre un token Sanctum.
 */
async function signInWithBackend(socialExchange) {
    const returnUrl = Linking.createURL("auth");
    const startUrl =
        `${SITE_URL}/auth/google/redirect?mobile=1&return_to=` +
        encodeURIComponent(returnUrl);

    const result = await WebBrowser.openAuthSessionAsync(startUrl, returnUrl);

    if (result.type !== "success") {
        return false; // annulé / fermé par l'utilisateur
    }

    const parsed = Linking.parse(result.url);
    if (parsed.queryParams?.error) {
        throw new Error("La connexion Google a été refusée ou a échoué.");
    }

    const code = parsed.queryParams?.code;
    if (!code) {
        throw new Error("Code de connexion Google manquant.");
    }

    await socialExchange(String(code));
    return true;
}

export default function SocialButtons() {
    const { socialLogin, socialExchange } = useAuth();
    const [busy, setBusy] = useState(false);

    const onGoogle = async () => {
        if (busy) return;
        setBusy(true);

        let statusCodes = {};
        try {
            if (isNativeGoogleAvailable() && WEB_CLIENT_ID) {
                statusCodes = loadNativeGoogle().statusCodes || {};

                try {
                    await signInWithNative(socialLogin);
                    return;
                } catch (nativeError) {
                    const code = nativeError?.code;

                    // Annulation ou opération déjà en cours : on ne relance pas
                    // un second écran de connexion.
                    if (
                        code === statusCodes.SIGN_IN_CANCELLED ||
                        code === statusCodes.IN_PROGRESS
                    ) {
                        return;
                    }

                    // DEVELOPER_ERROR, Play Services absent, module mal
                    // configuré… : on bascule sur le flux navigateur.
                }
            }

            await signInWithBackend(socialExchange);
        } catch (e) {
            Alert.alert(
                "Connexion Google échouée",
                apiError(
                    e,
                    "Impossible de finaliser la connexion Google. Réessayez ou utilisez votre email et mot de passe.",
                ),
            );
        } finally {
            setBusy(false);
        }
    };

    return (
        <View>
            <View style={styles.divider}>
                <View style={styles.line} />
                <Text style={styles.or}>ou continuer avec</Text>
                <View style={styles.line} />
            </View>

            <TouchableOpacity style={styles.btn} onPress={onGoogle} disabled={busy}>
                {busy ? (
                    <ActivityIndicator color="#111" />
                ) : (
                    <>
                        <Ionicons name="logo-google" size={20} color="#EA4335" />
                        <Text style={styles.googleText}>Continuer avec Google</Text>
                    </>
                )}
            </TouchableOpacity>
        </View>
    );
}

const styles = StyleSheet.create({
    divider: { flexDirection: "row", alignItems: "center", marginVertical: 18 },
    line: { flex: 1, height: 1, backgroundColor: "#e5e7eb" },
    or: { marginHorizontal: 10, color: "#9ca3af", fontSize: 12 },
    btn: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 8,
        borderRadius: 12,
        paddingVertical: 13,
        backgroundColor: "#fff",
        borderWidth: 1,
        borderColor: "#e5e7eb",
    },
    googleText: { color: "#111827", fontWeight: "700" },
});
