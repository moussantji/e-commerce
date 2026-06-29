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
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";

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

    return (
        <KeyboardAvoidingView
            style={{ flex: 1, backgroundColor: "#fff" }}
            behavior={Platform.OS === "ios" ? "padding" : undefined}
        >
            <ScrollView
                contentContainerStyle={styles.container}
                keyboardShouldPersistTaps="handled"
            >
                <Text style={styles.title}>Créer un compte</Text>
                <Text style={styles.subtitle}>Rejoignez la boutique</Text>

                <TextInput
                    style={styles.input}
                    placeholder="Nom complet"
                    value={form.name}
                    onChangeText={set("name")}
                />
                <TextInput
                    style={styles.input}
                    placeholder="Email"
                    autoCapitalize="none"
                    keyboardType="email-address"
                    value={form.email}
                    onChangeText={set("email")}
                />
                <TextInput
                    style={styles.input}
                    placeholder="Mot de passe (min. 6)"
                    secureTextEntry
                    value={form.password}
                    onChangeText={set("password")}
                />
                <TextInput
                    style={styles.input}
                    placeholder="Confirmer le mot de passe"
                    secureTextEntry
                    value={form.password_confirmation}
                    onChangeText={set("password_confirmation")}
                />

                <TouchableOpacity
                    style={[styles.button, loading && styles.disabled]}
                    onPress={submit}
                    disabled={loading}
                >
                    <Text style={styles.buttonText}>
                        {loading ? "Création..." : "S'inscrire"}
                    </Text>
                </TouchableOpacity>

                <TouchableOpacity onPress={() => navigation.goBack()}>
                    <Text style={styles.link}>
                        Déjà un compte ?{" "}
                        <Text style={styles.linkBold}>Se connecter</Text>
                    </Text>
                </TouchableOpacity>
            </ScrollView>
        </KeyboardAvoidingView>
    );
}

const styles = StyleSheet.create({
    container: { flexGrow: 1, justifyContent: "center", padding: 24 },
    title: {
        fontSize: 26,
        fontWeight: "800",
        textAlign: "center",
        color: "#111827",
    },
    subtitle: {
        fontSize: 14,
        color: "#6b7280",
        textAlign: "center",
        marginBottom: 28,
    },
    input: {
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: 12,
        paddingHorizontal: 16,
        paddingVertical: 14,
        fontSize: 15,
        marginBottom: 14,
        backgroundColor: "#f9fafb",
    },
    button: {
        backgroundColor: "#6366f1",
        borderRadius: 12,
        paddingVertical: 16,
        alignItems: "center",
        marginTop: 6,
    },
    disabled: { opacity: 0.6 },
    buttonText: { color: "#fff", fontWeight: "700", fontSize: 16 },
    link: { textAlign: "center", marginTop: 20, color: "#6b7280" },
    linkBold: { color: "#6366f1", fontWeight: "700" },
});
