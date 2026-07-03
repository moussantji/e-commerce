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
            if (e?.code === statusCodes?.SIGN_IN_CANCELLED) {
                // annulé par l'utilisateur : rien à faire
            } else if (e?.code === statusCodes?.PLAY_SERVICES_NOT_AVAILABLE) {
                Alert.alert("Google", "Google Play Services indisponible.");
            } else if (msg.includes("RNGoogleSignin")) {
                // Module natif absent : on est dans Expo Go, pas dans un vrai build.
                Alert.alert(
                    "Indisponible ici",
                    "La connexion Google nécessite l'application installée (APK / development build). Elle ne fonctionne pas dans Expo Go.",
                );
            } else {
                Alert.alert("Google", apiError(e) || "Connexion Google échouée.");
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
