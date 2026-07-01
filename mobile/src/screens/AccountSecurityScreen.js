import React, { useState } from "react";
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
    Alert,
    ActivityIndicator,
    KeyboardAvoidingView,
    Platform,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { COLORS, RADIUS } from "../theme";

function PasswordField({ label, value, onChangeText, placeholder }) {
    const [show, setShow] = useState(false);
    return (
        <View style={styles.field}>
            <Text style={styles.label}>{label}</Text>
            <View style={styles.inputRow}>
                <Ionicons name="lock-closed-outline" size={18} color={COLORS.textLight} />
                <TextInput
                    style={styles.input}
                    placeholder={placeholder}
                    placeholderTextColor="#9ca3af"
                    secureTextEntry={!show}
                    value={value}
                    onChangeText={onChangeText}
                    autoCapitalize="none"
                />
                <TouchableOpacity onPress={() => setShow((s) => !s)} hitSlop={8}>
                    <Ionicons name={show ? "eye-off-outline" : "eye-outline"} size={18} color="#9ca3af" />
                </TouchableOpacity>
            </View>
        </View>
    );
}

export default function AccountSecurityScreen({ navigation }) {
    const insets = useSafeAreaInsets();

    const [current, setCurrent] = useState("");
    const [next, setNext] = useState("");
    const [confirm, setConfirm] = useState("");
    const [saving, setSaving] = useState(false);

    const save = async () => {
        if (!current || !next) {
            Alert.alert("Champs requis", "Renseignez votre mot de passe actuel et le nouveau.");
            return;
        }
        if (next.length < 6) {
            Alert.alert("Trop court", "Le nouveau mot de passe doit faire au moins 6 caractères.");
            return;
        }
        if (next !== confirm) {
            Alert.alert("Confirmation", "Les mots de passe ne correspondent pas.");
            return;
        }
        setSaving(true);
        try {
            await api.put("/me/password", {
                current_password: current,
                password: next,
                password_confirmation: confirm,
            });
            Alert.alert("✅ Mis à jour", "Votre mot de passe a été changé.", [
                { text: "OK", onPress: () => navigation.goBack() },
            ]);
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setSaving(false);
        }
    };

    return (
        <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
            <LinearGradient
                colors={COLORS.gradient}
                start={COLORS.gradientStart}
                end={COLORS.gradientEnd}
                style={[styles.header, { paddingTop: insets.top + 8 }]}
            >
                <TouchableOpacity onPress={() => navigation.goBack()} hitSlop={10}>
                    <Ionicons name="chevron-back" size={26} color="#fff" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Sécurité du compte</Text>
                <View style={{ width: 26 }} />
            </LinearGradient>

            <KeyboardAvoidingView
                style={{ flex: 1 }}
                behavior={Platform.OS === "ios" ? "padding" : undefined}
            >
                <ScrollView contentContainerStyle={{ paddingBottom: 30 }} keyboardShouldPersistTaps="handled">
                    <Text style={styles.sectionTitle}>Changer le mot de passe</Text>
                    <View style={styles.card}>
                        <PasswordField
                            label="Mot de passe actuel"
                            value={current}
                            onChangeText={setCurrent}
                            placeholder="Mot de passe actuel"
                        />
                        <View style={styles.sep} />
                        <PasswordField
                            label="Nouveau mot de passe"
                            value={next}
                            onChangeText={setNext}
                            placeholder="Au moins 6 caractères"
                        />
                        <View style={styles.sep} />
                        <PasswordField
                            label="Confirmer le mot de passe"
                            value={confirm}
                            onChangeText={setConfirm}
                            placeholder="Retapez le nouveau mot de passe"
                        />
                    </View>

                    <Text style={styles.hint}>
                        Pour votre sécurité, vous serez déconnecté de vos autres appareils après le changement.
                    </Text>

                    <TouchableOpacity
                        style={[styles.saveBtn, saving && { opacity: 0.6 }]}
                        onPress={save}
                        disabled={saving}
                    >
                        {saving ? (
                            <ActivityIndicator color="#fff" />
                        ) : (
                            <Text style={styles.saveText}>Mettre à jour le mot de passe</Text>
                        )}
                    </TouchableOpacity>
                </ScrollView>
            </KeyboardAvoidingView>
        </View>
    );
}

const styles = StyleSheet.create({
    header: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingHorizontal: 12,
        paddingBottom: 14,
    },
    headerTitle: { fontSize: 17, fontWeight: "800", color: "#fff" },
    sectionTitle: {
        fontSize: 13,
        fontWeight: "700",
        color: COLORS.textLight,
        marginHorizontal: 16,
        marginTop: 16,
        marginBottom: 8,
    },
    card: { backgroundColor: "#fff", marginHorizontal: 12, borderRadius: RADIUS.lg, paddingHorizontal: 16 },
    field: { paddingVertical: 12 },
    sep: { height: 1, backgroundColor: "#f3f4f6" },
    label: { fontSize: 12, color: COLORS.textLight, fontWeight: "600", marginBottom: 4 },
    inputRow: { flexDirection: "row", alignItems: "center", gap: 10 },
    input: { flex: 1, fontSize: 15, color: COLORS.text, paddingVertical: 4 },
    hint: { color: COLORS.textLight, fontSize: 12, marginHorizontal: 16, marginTop: 12, lineHeight: 17 },
    saveBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 16,
        alignItems: "center",
        marginHorizontal: 12,
        marginTop: 20,
    },
    saveText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
