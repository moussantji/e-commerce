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
import { useAuth } from "../context/AuthContext";
import { apiError } from "../api/client";
import { COLORS, RADIUS } from "../theme";

const FIELDS = [
    { key: "name", label: "Nom", icon: "person-outline", placeholder: "Votre nom" },
    { key: "prenom", label: "Prénom", icon: "person-outline", placeholder: "Votre prénom" },
    { key: "email", label: "Email", icon: "mail-outline", placeholder: "email@exemple.com", keyboard: "email-address" },
    { key: "tel", label: "Téléphone", icon: "call-outline", placeholder: "+225 ...", keyboard: "phone-pad" },
    { key: "ville", label: "Ville", icon: "location-outline", placeholder: "Votre ville" },
    { key: "region", label: "Région", icon: "map-outline", placeholder: "Votre région" },
    { key: "pays", label: "Pays", icon: "flag-outline", placeholder: "Votre pays" },
    { key: "lieu_naiss", label: "Lieu de naissance", icon: "home-outline", placeholder: "Lieu de naissance" },
];

export default function EditProfileScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const { user, updateProfile } = useAuth();

    const [form, setForm] = useState({
        name: user?.name || "",
        prenom: user?.prenom || "",
        email: user?.email || "",
        tel: user?.tel || "",
        ville: user?.ville || "",
        region: user?.region || "",
        pays: user?.pays || "",
        lieu_naiss: user?.lieu_naiss || "",
    });
    const [saving, setSaving] = useState(false);

    const set = (key, value) => setForm((f) => ({ ...f, [key]: value }));

    const save = async () => {
        if (!form.name?.trim()) {
            Alert.alert("Nom requis", "Veuillez renseigner votre nom.");
            return;
        }
        setSaving(true);
        try {
            await updateProfile(form);
            Alert.alert("✅ Enregistré", "Votre profil a été mis à jour.", [
                { text: "OK", onPress: () => navigation.goBack() },
            ]);
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setSaving(false);
        }
    };

    const initial = (form.name || "?").charAt(0).toUpperCase();

    return (
        <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
            {/* En-tête */}
            <LinearGradient
                colors={COLORS.gradient}
                start={COLORS.gradientStart}
                end={COLORS.gradientEnd}
                style={[styles.header, { paddingTop: insets.top + 8 }]}
            >
                <TouchableOpacity onPress={() => navigation.goBack()} hitSlop={10}>
                    <Ionicons name="chevron-back" size={26} color="#fff" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Modifier le profil</Text>
                <View style={{ width: 26 }} />
            </LinearGradient>

            <KeyboardAvoidingView
                style={{ flex: 1 }}
                behavior={Platform.OS === "ios" ? "padding" : undefined}
            >
                <ScrollView contentContainerStyle={{ paddingBottom: 30 }} keyboardShouldPersistTaps="handled">
                    {/* Avatar */}
                    <View style={styles.avatarWrap}>
                        <View style={styles.avatar}>
                            <Text style={styles.avatarText}>{initial}</Text>
                        </View>
                        <TouchableOpacity
                            style={styles.avatarEdit}
                            onPress={() => Alert.alert("Photo de profil", "Bientôt disponible.")}
                        >
                            <Ionicons name="camera" size={16} color="#fff" />
                        </TouchableOpacity>
                    </View>

                    {/* Champs */}
                    <View style={styles.card}>
                        {FIELDS.map((f, i) => (
                            <View key={f.key} style={[styles.field, i < FIELDS.length - 1 && styles.fieldBorder]}>
                                <Text style={styles.label}>{f.label}</Text>
                                <View style={styles.inputRow}>
                                    <Ionicons name={f.icon} size={18} color={COLORS.textLight} />
                                    <TextInput
                                        style={styles.input}
                                        placeholder={f.placeholder}
                                        placeholderTextColor="#9ca3af"
                                        value={form[f.key]}
                                        onChangeText={(v) => set(f.key, v)}
                                        keyboardType={f.keyboard || "default"}
                                        autoCapitalize={f.key === "email" ? "none" : "sentences"}
                                    />
                                </View>
                            </View>
                        ))}
                    </View>
                </ScrollView>
            </KeyboardAvoidingView>

            {/* Bouton enregistrer */}
            <View style={[styles.footer, { paddingBottom: insets.bottom + 12 }]}>
                <TouchableOpacity
                    style={[styles.saveBtn, saving && { opacity: 0.6 }]}
                    onPress={save}
                    disabled={saving}
                >
                    {saving ? (
                        <ActivityIndicator color="#fff" />
                    ) : (
                        <Text style={styles.saveText}>Enregistrer</Text>
                    )}
                </TouchableOpacity>
            </View>
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
    avatarWrap: { alignSelf: "center", marginTop: 18, marginBottom: 10 },
    avatar: {
        width: 88,
        height: 88,
        borderRadius: 44,
        backgroundColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
    },
    avatarText: { color: "#fff", fontSize: 34, fontWeight: "900" },
    avatarEdit: {
        position: "absolute",
        right: -2,
        bottom: -2,
        width: 30,
        height: 30,
        borderRadius: 15,
        backgroundColor: COLORS.accent,
        alignItems: "center",
        justifyContent: "center",
        borderWidth: 2,
        borderColor: "#fff",
    },
    card: {
        backgroundColor: "#fff",
        marginHorizontal: 12,
        marginTop: 10,
        borderRadius: RADIUS.lg,
        paddingHorizontal: 16,
    },
    field: { paddingVertical: 12 },
    fieldBorder: { borderBottomWidth: 1, borderBottomColor: "#f3f4f6" },
    label: { fontSize: 12, color: COLORS.textLight, fontWeight: "600", marginBottom: 4 },
    inputRow: { flexDirection: "row", alignItems: "center", gap: 10 },
    input: { flex: 1, fontSize: 15, color: COLORS.text, paddingVertical: 4 },
    footer: {
        paddingHorizontal: 16,
        paddingTop: 10,
        backgroundColor: "#fff",
        borderTopWidth: 1,
        borderTopColor: "#f0f0f0",
    },
    saveBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 16,
        alignItems: "center",
    },
    saveText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
