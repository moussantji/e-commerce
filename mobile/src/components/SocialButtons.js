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
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";
import { GOOGLE_CLIENT_IDS } from "../config";

WebBrowser.maybeCompleteAuthSession();

const GOOGLE_ON = !!(
    GOOGLE_CLIENT_IDS.android ||
    GOOGLE_CLIENT_IDS.ios ||
    GOOGLE_CLIENT_IDS.web ||
    GOOGLE_CLIENT_IDS.expo
);

/** Bouton présentationnel Google. */
function GoogleFace({ busy, onPress }) {
    return (
        <TouchableOpacity style={styles.btn} onPress={onPress} disabled={busy}>
            {busy ? (
                <ActivityIndicator color="#111" />
            ) : (
                <>
                    <Ionicons name="logo-google" size={20} color="#EA4335" />
                    <Text style={styles.googleText}>Continuer avec Google</Text>
                </>
            )}
        </TouchableOpacity>
    );
}

/** Bouton Google réel (monté uniquement si configuré). */
function GoogleButton() {
    const { socialLogin } = useAuth();
    const [busy, setBusy] = useState(false);
    const [, response, promptAsync] = Google.useAuthRequest({
        androidClientId: GOOGLE_CLIENT_IDS.android || undefined,
        iosClientId: GOOGLE_CLIENT_IDS.ios || undefined,
        webClientId: GOOGLE_CLIENT_IDS.web || GOOGLE_CLIENT_IDS.expo || undefined,
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
        <GoogleFace
            busy={busy}
            onPress={() => {
                setBusy(true);
                promptAsync();
            }}
        />
    );
}

/** Bouton Google non configuré : rappel. */
function NotConfiguredButton() {
    return (
        <GoogleFace
            busy={false}
            onPress={() =>
                Alert.alert(
                    "À configurer",
                    "Renseignez le client Google dans mobile/src/config.js.",
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

            {GOOGLE_ON ? <GoogleButton /> : <NotConfiguredButton />}
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
