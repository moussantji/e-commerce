import React, { useEffect, useState } from "react";
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    Alert,
    ActivityIndicator,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { NativeModules, TurboModuleRegistry } from "react-native";
import * as WebBrowser from "expo-web-browser";
import * as Google from "expo-auth-session/providers/google";
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";
import { GOOGLE_CLIENT_IDS } from "../config";

WebBrowser.maybeCompleteAuthSession();

// ⚠️ PAS d'import statique de @react-native-google-signin/google-signin :
// son code appelle TurboModuleRegistry.getEnforcing('RNGoogleSignin') DÈS
// SON CHARGEMENT, ce qui crashe l'app dans Expo Go (module natif absent),
// avant même tout try/catch. On le charge paresseusement ci-dessous.
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
    // require paresseux : si le natif est absent, l'évaluation du module
    // lève (getEnforcing) et on retombe proprement sur le flux web.
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
        offlineAccess: false,
    });
    googleConfigured = true;
}

async function signInWithNative(socialLogin) {
    const { GoogleSignin, statusCodes } = loadNativeGoogle();
    ensureGoogleConfigured(GoogleSignin);
    await GoogleSignin.hasPlayServices({
        showPlayServicesUpdateDialog: true,
    });
    const result = await GoogleSignin.signIn();
    // SDK v13+: { type, data: { idToken, ... } } ; anciennes: { idToken, ... }
    const idToken = result?.data?.idToken ?? result?.idToken;
    if (!idToken) {
        throw new Error("idToken introuvable");
    }
    await socialLogin("google", idToken);
    return { statusCodes };
}

export default function SocialButtons() {
    const { socialLogin } = useAuth();
    const [busy, setBusy] = useState(false);
    const [webFallbackArmed, setWebFallbackArmed] = useState(false);

    // Flux web (fallback quand le module natif est absent, ex: Expo Go).
    // Retourne un idToken utilisable par le backend comme le flux natif.
    const [webRequest, webResponse, webPromptAsync] =
        Google.useIdTokenAuthRequest({
            clientId: WEB_CLIENT_ID,
            webClientId: WEB_CLIENT_ID,
            androidClientId: GOOGLE_CLIENT_IDS.android || undefined,
            iosClientId: GOOGLE_CLIENT_IDS.ios || undefined,
        });

    // Traite la réponse du flux web (le navigateur peut faire
    // redémarrer l'app : on ne peut pas se fier au retour de promptAsync).
    useEffect(() => {
        if (!webFallbackArmed || !webResponse) return;
        (async () => {
            try {
                if (webResponse.type === "success") {
                    const idToken = webResponse.params?.id_token;
                    if (!idToken) {
                        throw new Error("idToken introuvable dans la réponse Google.");
                    }
                    await socialLogin("google", idToken);
                } else if (webResponse.type === "error") {
                    throw new Error(
                        webResponse.error?.message || "Flux Google interrompu.",
                    );
                }
                // cancel / dismiss : l'utilisateur a fermé le navigateur, silence.
            } catch (err) {
                const detail = apiError(err, "");
                Alert.alert(
                    "Connexion Google échouée",
                    detail ||
                        "Impossible de finaliser la connexion Google. Réessayez ou utilisez votre email et mot de passe.",
                );
            } finally {
                setWebFallbackArmed(false);
                setBusy(false);
            }
        })();
    }, [webFallbackArmed, webResponse]);

    // Flux web expo-auth-session : aucun module natif requis (Expo Go, web…).
    // Retourne true si le navigateur a été ouvert (réponse traitée dans le
    // useEffect), false sinon.
    const signInWithWeb = async () => {
        if (!webRequest) {
            Alert.alert(
                "Connexion Google",
                "Le flux Google est encore en préparation, réessayez dans un instant.",
            );
            return false;
        }
        setBusy(true);
        try {
            setWebFallbackArmed(true);
            await webPromptAsync();
            return true; // la réponse est traitée dans le useEffect ci-dessus
        } catch (webErr) {
            setWebFallbackArmed(false);
            setBusy(false);
            const detail = apiError(webErr, "");
            Alert.alert(
                "Connexion Google échouée",
                detail ||
                    "Impossible d'ouvrir la connexion Google. Réessayez ou utilisez votre email et mot de passe.",
            );
            return false;
        }
    };

    const onGoogle = async () => {
        if (!WEB_CLIENT_ID) {
            Alert.alert(
                "À configurer",
                "Renseignez le webClientId Google dans mobile/src/config.js.",
            );
            return;
        }
        // Pas de module natif (Expo Go, web…) : flux web direct, sans même
        // toucher au SDK natif → aucune erreur « RNGoogleSignin ».
        if (!isNativeGoogleAvailable()) {
            await signInWithWeb();
            return;
        }
        setBusy(true);
        let tookWebPath = false;
        let nativeStatusCodes = null;
        try {
            const { statusCodes } = await signInWithNative(socialLogin);
            nativeStatusCodes = statusCodes;
        } catch (e) {
            const msg = String(e?.message || "");
            const code = e?.code;
            const SC = nativeStatusCodes || {};
            if (code === SC.SIGN_IN_CANCELLED) {
                // annulé par l'utilisateur : rien à faire
            } else if (code === SC.IN_PROGRESS) {
                // une connexion est déjà en cours : on ignore
            } else if (code === SC.PLAY_SERVICES_NOT_AVAILABLE) {
                Alert.alert(
                    "Google Play Services",
                    "Google Play Services est indisponible ou doit être mis à jour sur cet appareil.",
                );
            } else if (msg.includes("RNGoogleSignin") || msg.includes("could not be found")) {
                // Sécurité : le natif a disparu entre le test et l'appel.
                // On bascule sur le flux web (le useEffect libère `busy`
                // si le navigateur s'ouvre, sinon signInWithWeb l'a fait).
                tookWebPath = await signInWithWeb();
                return;
            } else if (code === "DEVELOPER_ERROR" || String(code) === "10") {
                // Erreur de configuration Google la plus fréquente sur Android.
                Alert.alert(
                    "Configuration Google incomplète",
                    "La connexion Google n'est pas correctement configurée pour cette version de l'app " +
                        "(empreinte SHA-1 ou identifiant client manquant). Utilisez l'email/mot de passe en attendant.",
                );
            } else {
                // On affiche le message du serveur s'il existe, sinon un message clair.
                const detail = apiError(e, "");
                Alert.alert(
                    "Connexion Google échouée",
                    detail ||
                        "Impossible de finaliser la connexion Google. Réessayez ou utilisez votre email et mot de passe." +
                            (code ? `\n(code : ${code})` : ""),
                );
            }
        } finally {
            // Si on a basculé sur le flux web, c'est le useEffect qui
            // libère `busy` à la réception de la réponse Google.
            if (!tookWebPath) {
                setBusy(false);
            }
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
