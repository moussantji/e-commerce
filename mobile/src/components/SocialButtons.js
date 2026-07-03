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
import {
    GoogleSignin,
    statusCodes,
} from "@react-native-google-signin/google-signin";
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";
import { GOOGLE_CLIENT_IDS } from "../config";

const WEB_CLIENT_ID = GOOGLE_CLIENT_IDS.web || GOOGLE_CLIENT_IDS.expo || "";

// Configuration paresseuse : le SDK natif n'existe PAS dans Expo Go.
// On configure au 1er clic, dans un try/catch, pour ne jamais crasher l'app
// au démarrage si le module natif est absent (« RNGoogleSignin could not be found »).
let googleConfigured = false;
function ensureGoogleConfigured() {
    if (googleConfigured) return;
    GoogleSignin.configure({
        webClientId: WEB_CLIENT_ID,
        offlineAccess: false,
    });
    googleConfigured = true;
}

export default function SocialButtons() {
    const { socialLogin } = useAuth();
    const [busy, setBusy] = useState(false);

    const onGoogle = async () => {
        if (!WEB_CLIENT_ID) {
            Alert.alert(
                "À configurer",
                "Renseignez le webClientId Google dans mobile/src/config.js.",
            );
            return;
        }
        setBusy(true);
        try {
            ensureGoogleConfigured();
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
        } catch (e) {
            const msg = String(e?.message || "");
            const code = e?.code;
            if (code === statusCodes?.SIGN_IN_CANCELLED) {
                // annulé par l'utilisateur : rien à faire
            } else if (code === statusCodes?.IN_PROGRESS) {
                // une connexion est déjà en cours : on ignore
            } else if (code === statusCodes?.PLAY_SERVICES_NOT_AVAILABLE) {
                Alert.alert(
                    "Google Play Services",
                    "Google Play Services est indisponible ou doit être mis à jour sur cet appareil.",
                );
            } else if (msg.includes("RNGoogleSignin") || msg.includes("could not be found")) {
                // Module natif absent : on est dans Expo Go, pas dans un vrai build.
                Alert.alert(
                    "Indisponible dans Expo Go",
                    "La connexion Google nécessite l'application installée (APK ou development build). Elle ne fonctionne pas dans Expo Go.",
                );
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
