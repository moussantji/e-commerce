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
} from "react-native";
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";

export default function LoginScreen({ navigation }) {
    const { login } = useAuth();
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [loading, setLoading] = useState(false);

    const submit = async () => {
        if (!email || !password) {
            Alert.alert("Champs requis", "Renseignez email et mot de passe.");
            return;
        }
        setLoading(true);
        try {
            await login(email.trim(), password);
        } catch (e) {
            Alert.alert(
                "Connexion échouée",
                apiError(e, "Identifiants incorrects"),
            );
        } finally {
            setLoading(false);
        }
    };

    return (
        <KeyboardAvoidingView
            style={styles.container}
            behavior={Platform.OS === "ios" ? "padding" : undefined}
        >
            <Text style={styles.logo}>🛍️</Text>
            <Text style={styles.title}>Connexion</Text>
            <Text style={styles.subtitle}>Accédez à votre compte</Text>

            <TextInput
                style={styles.input}
                placeholder="Email"
                autoCapitalize="none"
                keyboardType="email-address"
                value={email}
                onChangeText={setEmail}
            />
            <TextInput
                style={styles.input}
                placeholder="Mot de passe"
                secureTextEntry
                value={password}
                onChangeText={setPassword}
            />

            <TouchableOpacity
                style={[styles.button, loading && styles.disabled]}
                onPress={submit}
                disabled={loading}
            >
                <Text style={styles.buttonText}>
                    {loading ? "Connexion..." : "Se connecter"}
                </Text>
            </TouchableOpacity>

            <TouchableOpacity onPress={() => navigation.navigate("Register")}>
                <Text style={styles.link}>
                    Pas de compte ?{" "}
                    <Text style={styles.linkBold}>Créer un compte</Text>
                </Text>
            </TouchableOpacity>
        </KeyboardAvoidingView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: "center",
        padding: 24,
        backgroundColor: "#fff",
    },
    logo: { fontSize: 48, textAlign: "center", marginBottom: 8 },
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
