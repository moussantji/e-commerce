import React, { useState } from "react";
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    Alert,
    KeyboardAvoidingView,
    Platform,
    ScrollView,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";
import SocialButtons from "../components/SocialButtons";
import AuthBackground from "../components/AuthBackground";
import { COLORS } from "../theme";

export default function RegisterScreen({ navigation }) {
    const { register } = useAuth();
    const [form, setForm] = useState({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });
    const [loading, setLoading] = useState(false);

    const set = (k) => (v) => setForm((f) => ({ ...f, [k]: v }));

    const submit = async () => {
        if (!form.name || !form.email || !form.password) {
            Alert.alert(
                "Champs requis",
                "Nom, email et mot de passe sont obligatoires.",
            );
            return;
        }
        if (form.password !== form.password_confirmation) {
            Alert.alert(
                "Mot de passe",
                "Les mots de passe ne correspondent pas.",
            );
            return;
        }
        setLoading(true);
        try {
            await register(form);
        } catch (e) {
            Alert.alert("Inscription échouée", apiError(e));
        } finally {
            setLoading(false);
        }
    };

    const field = (key, placeholder, icon, opts = {}) => (
        <View style={styles.inputWrap}>
            <Ionicons name={icon} size={18} color="#9ca3af" />
            <TextInput
                style={styles.input}
                placeholder={placeholder}
                value={form[key]}
                onChangeText={set(key)}
                {...opts}
            />
        </View>
    );

    return (
        <AuthBackground>
            <KeyboardAvoidingView
                style={{ flex: 1 }}
                behavior={Platform.OS === "ios" ? "padding" : undefined}
            >
                <ScrollView
                    contentContainerStyle={styles.scroll}
                    keyboardShouldPersistTaps="handled"
                >
                    <View style={styles.brand}>
                        <View style={styles.logoCircle}>
                            <Ionicons
                                name="person-add"
                                size={32}
                                color={COLORS.primaryDark}
                            />
                        </View>
                        <Text style={styles.brandTitle}>Créer un compte</Text>
                        <Text style={styles.brandSub}>
                            Rejoignez la boutique en quelques secondes
                        </Text>
                    </View>

                    <View style={styles.card}>
                        {field("name", "Nom complet", "person-outline")}
                        {field("email", "Email", "mail-outline", {
                            autoCapitalize: "none",
                            keyboardType: "email-address",
                        })}
                        {field(
                            "password",
                            "Mot de passe (min. 6)",
                            "lock-closed-outline",
                            { secureTextEntry: true },
                        )}
                        {field(
                            "password_confirmation",
                            "Confirmer le mot de passe",
                            "lock-closed-outline",
                            { secureTextEntry: true },
                        )}

                        <TouchableOpacity
                            style={[styles.button, loading && styles.disabled]}
                            onPress={submit}
                            disabled={loading}
                        >
                            <Text style={styles.buttonText}>
                                {loading ? "Création..." : "S'inscrire"}
                            </Text>
                        </TouchableOpacity>

                        <SocialButtons />

                        <TouchableOpacity onPress={() => navigation.goBack()}>
                            <Text style={styles.link}>
                                Déjà un compte ?{" "}
                                <Text style={styles.linkBold}>
                                    Se connecter
                                </Text>
                            </Text>
                        </TouchableOpacity>
                    </View>
                </ScrollView>
            </KeyboardAvoidingView>
        </AuthBackground>
    );
}

const styles = StyleSheet.create({
    bg: { flex: 1 },
    scroll: { flexGrow: 1, justifyContent: "center", padding: 24 },
    brand: { alignItems: "center", marginBottom: 20 },
    logoCircle: {
        width: 70,
        height: 70,
        borderRadius: 35,
        backgroundColor: "#fff",
        justifyContent: "center",
        alignItems: "center",
        marginBottom: 12,
        elevation: 4,
    },
    brandTitle: { fontSize: 24, fontWeight: "900", color: "#fff" },
    brandSub: {
        color: "rgba(255,255,255,0.95)",
        marginTop: 4,
        textAlign: "center",
    },
    card: {
        backgroundColor: "#fff",
        borderRadius: 20,
        padding: 22,
        elevation: 6,
        shadowColor: "#000",
        shadowOpacity: 0.15,
        shadowRadius: 12,
        shadowOffset: { width: 0, height: 4 },
    },
    inputWrap: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: 12,
        paddingHorizontal: 14,
        marginBottom: 14,
        backgroundColor: "#f9fafb",
    },
    input: { flex: 1, paddingVertical: 14, fontSize: 15 },
    button: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 12,
        paddingVertical: 16,
        alignItems: "center",
        marginTop: 4,
    },
    disabled: { opacity: 0.6 },
    buttonText: { color: "#fff", fontWeight: "800", fontSize: 16 },
    link: { textAlign: "center", marginTop: 18, color: "#6b7280" },
    linkBold: { color: COLORS.primaryDark, fontWeight: "800" },
});
