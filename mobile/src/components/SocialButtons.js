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
import * as WebBrowser from "expo-web-browser";
import * as Google from "expo-auth-session/providers/google";
import * as Facebook from "expo-auth-session/providers/facebook";
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";
import { GOOGLE_CLIENT_IDS, FACEBOOK_APP_ID } from "../config";

WebBrowser.maybeCompleteAuthSession();

// Constantes calculées une seule fois (pas de hook conditionnel à l'intérieur d'un composant)
const GOOGLE_ON = !!(
    GOOGLE_CLIENT_IDS.android ||
    GOOGLE_CLIENT_IDS.ios ||
    GOOGLE_CLIENT_IDS.web ||
    GOOGLE_CLIENT_IDS.expo
);
const FB_ON = !!FACEBOOK_APP_ID;

/** Bouton présentationnel (toujours affiché). */
function ProviderButton({ provider, busy, onPress }) {
    const isGoogle = provider === "google";
    return (
        <TouchableOpacity
            style={[styles.btn, isGoogle ? styles.google : styles.fb]}
            onPress={onPress}
            disabled={busy}
        >
            {busy ? (
                <ActivityIndicator color={isGoogle ? "#111" : "#fff"} />
            ) : (
                <>
                    <Ionicons
                        name={isGoogle ? "logo-google" : "logo-facebook"}
                        size={20}
                        color={isGoogle ? "#EA4335" : "#fff"}
                    />
                    <Text style={isGoogle ? styles.googleText : styles.fbText}>
                        {isGoogle ? "Google" : "Facebook"}
                    </Text>
                </>
            )}
        </TouchableOpacity>
    );
}

/** Bouton Google réel (monté UNIQUEMENT si configuré → pas de crash invariant). */
function GoogleButton() {
    const { socialLogin } = useAuth();
    const [busy, setBusy] = useState(false);
    const [, response, promptAsync] = Google.useAuthRequest({
        androidClientId: GOOGLE_CLIENT_IDS.android || undefined,
        iosClientId: GOOGLE_CLIENT_IDS.ios || undefined,
        webClientId:
            GOOGLE_CLIENT_IDS.web || GOOGLE_CLIENT_IDS.expo || undefined,
    });

    useEffect(() => {
        if (!response) return;
        if (response.type === "success") {
            (async () => {
                try {
                    await socialLogin(
                        "google",
                        response.authentication?.accessToken,
                    );
                } catch (e) {
                    Alert.alert("Google", apiError(e));
                } finally {
                    setBusy(false);
                }
            })();
        } else {
            setBusy(false);
        }
    }, [response]);

    return (
        <ProviderButton
            provider="google"
            busy={busy}
            onPress={() => {
                setBusy(true);
                promptAsync();
            }}
        />
    );
}

/** Bouton Facebook réel (monté UNIQUEMENT si configuré). */
function FacebookButton() {
    const { socialLogin } = useAuth();
    const [busy, setBusy] = useState(false);
    const [, response, promptAsync] = Facebook.useAuthRequest({
        clientId: FACEBOOK_APP_ID,
    });

    useEffect(() => {
        if (!response) return;
        if (response.type === "success") {
            (async () => {
                try {
                    await socialLogin(
                        "facebook",
                        response.authentication?.accessToken,
                    );
                } catch (e) {
                    Alert.alert("Facebook", apiError(e));
                } finally {
                    setBusy(false);
                }
            })();
        } else {
            setBusy(false);
        }
    }, [response]);

    return (
        <ProviderButton
            provider="facebook"
            busy={busy}
            onPress={() => {
                setBusy(true);
                promptAsync();
            }}
        />
    );
}

/** Bouton non configuré : affiche un rappel (aucun hook OAuth appelé). */
function NotConfiguredButton({ provider }) {
    const label = provider === "google" ? "Google" : "Facebook";
    return (
        <ProviderButton
            provider={provider}
            busy={false}
            onPress={() =>
                Alert.alert(
                    "À configurer",
                    `Renseignez les identifiants ${label} dans mobile/src/config.js (voir le README).`,
                )
            }
        />
    );
}

export default function SocialButtons() {
    return (
        <View>
            <View style={styles.divider}>
                <View style={styles.line} />
                <Text style={styles.or}>ou continuer avec</Text>
                <View style={styles.line} />
            </View>

            <View style={styles.row}>
                {GOOGLE_ON ? (
                    <GoogleButton />
                ) : (
                    <NotConfiguredButton provider="google" />
                )}
                {FB_ON ? (
                    <FacebookButton />
                ) : (
                    <NotConfiguredButton provider="facebook" />
                )}
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    divider: { flexDirection: "row", alignItems: "center", marginVertical: 18 },
    line: { flex: 1, height: 1, backgroundColor: "#e5e7eb" },
    or: { marginHorizontal: 10, color: "#9ca3af", fontSize: 12 },
    row: { flexDirection: "row", gap: 12 },
    btn: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 8,
        borderRadius: 12,
        paddingVertical: 13,
    },
    google: { backgroundColor: "#fff", borderWidth: 1, borderColor: "#e5e7eb" },
    googleText: { color: "#111827", fontWeight: "700" },
    fb: { backgroundColor: "#1877F2" },
    fbText: { color: "#fff", fontWeight: "700" },
});
