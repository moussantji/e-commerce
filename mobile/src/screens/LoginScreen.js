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

export default function LoginScreen({ navigation }) {
    const { login } = useAuth();
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [showPass, setShowPass] = useState(false);
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
                                name="bag-handle"
                                size={36}
                                color="#FF4500"
                            />
                        </View>
                        <Text style={styles.brandTitle}>Bienvenue 👋</Text>
                        <Text style={styles.brandSub}>
                            Connectez-vous pour voir les offres
                        </Text>
                    </View>

                    <View style={styles.card}>
                        <View style={styles.inputWrap}>
                            <Ionicons
                                name="mail-outline"
                                size={18}
                                color="#9ca3af"
                            />
                            <TextInput
                                style={styles.input}
                                placeholder="Email"
                                autoCapitalize="none"
                                keyboardType="email-address"
                                value={email}
                                onChangeText={setEmail}
                            />
                        </View>

                        <View style={styles.inputWrap}>
                            <Ionicons
                                name="lock-closed-outline"
                                size={18}
                                color="#9ca3af"
                            />
                            <TextInput
                                style={styles.input}
                                placeholder="Mot de passe"
                                secureTextEntry={!showPass}
                                value={password}
                                onChangeText={setPassword}
                            />
                            <TouchableOpacity
                                onPress={() => setShowPass((s) => !s)}
                            >
                                <Ionicons
                                    name={
                                        showPass
                                            ? "eye-off-outline"
                                            : "eye-outline"
                                    }
                                    size={18}
                                    color="#9ca3af"
                                />
                            </TouchableOpacity>
                        </View>

                        <TouchableOpacity
                            style={[styles.button, loading && styles.disabled]}
                            onPress={submit}
                            disabled={loading}
                        >
                            <Text style={styles.buttonText}>
                                {loading ? "Connexion..." : "Se connecter"}
                            </Text>
                        </TouchableOpacity>

                        <SocialButtons />

                        <TouchableOpacity
                            onPress={() => navigation.navigate("Register")}
                        >
                            <Text style={styles.link}>
                                Pas de compte ?{" "}
                                <Text style={styles.linkBold}>
                                    Créer un compte
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
    brand: { alignItems: "center", marginBottom: 24 },
    logoCircle: {
        width: 76,
        height: 76,
        borderRadius: 38,
        backgroundColor: "#fff",
        justifyContent: "center",
        alignItems: "center",
        marginBottom: 14,
        elevation: 4,
    },
    brandTitle: { fontSize: 26, fontWeight: "900", color: "#fff" },
    brandSub: { color: "rgba(255,255,255,0.95)", marginTop: 4 },
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
        backgroundColor: "#FF4500",
        borderRadius: 12,
        paddingVertical: 16,
        alignItems: "center",
        marginTop: 4,
    },
    disabled: { opacity: 0.6 },
    buttonText: { color: "#fff", fontWeight: "800", fontSize: 16 },
    link: { textAlign: "center", marginTop: 18, color: "#6b7280" },
    linkBold: { color: "#FF4500", fontWeight: "800" },
});
