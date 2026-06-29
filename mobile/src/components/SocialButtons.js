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
import * as Google from "expo-auth-session/providers/Google";
import * as Facebook from "expo-auth-session/providers/Facebook";
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";
import { GOOGLE_CLIENT_IDS, FACEBOOK_APP_ID } from "../config";

WebBrowser.maybeCompleteAuthSession();

export default function SocialButtons() {
    const { socialLogin } = useAuth();
    const [busy, setBusy] = useState(null);

    const [, gResponse, gPrompt] = Google.useAuthRequest({
        expoClientId: GOOGLE_CLIENT_IDS.expo || undefined,
        androidClientId: GOOGLE_CLIENT_IDS.android || undefined,
        iosClientId: GOOGLE_CLIENT_IDS.ios || undefined,
        webClientId: GOOGLE_CLIENT_IDS.web || undefined,
    });

    const [, fbResponse, fbPrompt] = Facebook.useAuthRequest({
        clientId: FACEBOOK_APP_ID || undefined,
    });

    const finish = async (provider, accessToken) => {
        if (!accessToken) {
            setBusy(null);
            return;
        }
        try {
            await socialLogin(provider, accessToken);
        } catch (e) {
            Alert.alert("Connexion sociale", apiError(e));
        } finally {
            setBusy(null);
        }
    };

    useEffect(() => {
        if (gResponse?.type === "success") {
            finish("google", gResponse.authentication?.accessToken);
        } else if (gResponse && gResponse.type !== "success") {
            setBusy(null);
        }
    }, [gResponse]);

    useEffect(() => {
        if (fbResponse?.type === "success") {
            finish("facebook", fbResponse.authentication?.accessToken);
        } else if (fbResponse && fbResponse.type !== "success") {
            setBusy(null);
        }
    }, [fbResponse]);

    const start = async (provider) => {
        const configured =
            provider === "google"
                ? Object.values(GOOGLE_CLIENT_IDS).some(Boolean)
                : !!FACEBOOK_APP_ID;

        if (!configured) {
            Alert.alert(
                "À configurer",
                `Renseignez les identifiants ${provider === "google" ? "Google" : "Facebook"} dans mobile/src/config.js (voir le README).`,
            );
            return;
        }
        setBusy(provider);
        try {
            if (provider === "google") await gPrompt();
            else await fbPrompt();
        } catch (e) {
            setBusy(null);
        }
    };

    return (
        <View>
            <View style={styles.divider}>
                <View style={styles.line} />
                <Text style={styles.or}>ou continuer avec</Text>
                <View style={styles.line} />
            </View>

            <View style={styles.row}>
                <TouchableOpacity
                    style={[styles.btn, styles.google]}
                    onPress={() => start("google")}
                    disabled={!!busy}
                >
                    {busy === "google" ? (
                        <ActivityIndicator color="#111" />
                    ) : (
                        <>
                            <Ionicons
                                name="logo-google"
                                size={20}
                                color="#EA4335"
                            />
                            <Text style={styles.googleText}>Google</Text>
                        </>
                    )}
                </TouchableOpacity>

                <TouchableOpacity
                    style={[styles.btn, styles.fb]}
                    onPress={() => start("facebook")}
                    disabled={!!busy}
                >
                    {busy === "facebook" ? (
                        <ActivityIndicator color="#fff" />
                    ) : (
                        <>
                            <Ionicons
                                name="logo-facebook"
                                size={20}
                                color="#fff"
                            />
                            <Text style={styles.fbText}>Facebook</Text>
                        </>
                    )}
                </TouchableOpacity>
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
