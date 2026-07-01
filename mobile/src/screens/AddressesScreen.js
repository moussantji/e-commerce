import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    ScrollView,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
    Modal,
    Switch,
    KeyboardAvoidingView,
    Platform,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { COLORS, RADIUS } from "../theme";

const EMPTY = {
    nom: "",
    telephone: "",
    adresse: "",
    ville: "",
    region: "",
    pays: "Mali",
    code_postal: "",
    is_default: false,
};

export default function AddressesScreen() {
    const insets = useSafeAreaInsets();
    const [addresses, setAddresses] = useState([]);
    const [loading, setLoading] = useState(true);
    const [modal, setModal] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState(EMPTY);
    const [saving, setSaving] = useState(false);

    const load = useCallback(async () => {
        try {
            const { data } = await api.get("/addresses");
            setAddresses(data.data ?? []);
        } catch (e) {
            /* silencieux */
        } finally {
            setLoading(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const openAdd = () => {
        setEditing(null);
        setForm(EMPTY);
        setModal(true);
    };
    const openEdit = (a) => {
        setEditing(a);
        setForm({ ...EMPTY, ...a });
        setModal(true);
    };

    const setField = (k, v) => setForm((f) => ({ ...f, [k]: v }));

    const save = async () => {
        if (!form.nom || !form.telephone || !form.adresse || !form.ville) {
            Alert.alert(
                "Champs requis",
                "Nom, téléphone, adresse et ville sont obligatoires.",
            );
            return;
        }
        setSaving(true);
        try {
            if (editing) {
                await api.put(`/addresses/${editing.id}`, form);
            } else {
                await api.post("/addresses", form);
            }
            setModal(false);
            await load();
        } catch (e) {
            Alert.alert("Erreur", apiError(e));
        } finally {
            setSaving(false);
        }
    };

    const remove = (a) => {
        Alert.alert("Supprimer", "Supprimer cette adresse ?", [
            { text: "Annuler", style: "cancel" },
            {
                text: "Supprimer",
                style: "destructive",
                onPress: async () => {
                    try {
                        await api.delete(`/addresses/${a.id}`);
                        load();
                    } catch (e) {
                        Alert.alert("Erreur", apiError(e));
                    }
                },
            },
        ]);
    };

    const setDefault = async (a) => {
        try {
            await api.post(`/addresses/${a.id}/default`);
            load();
        } catch (e) {
            Alert.alert("Erreur", apiError(e));
        }
    };

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    const FIELDS = [
        { k: "nom", label: "Nom complet", kb: "default" },
        { k: "telephone", label: "Téléphone", kb: "phone-pad" },
        { k: "adresse", label: "Adresse (rue, porte...)", kb: "default" },
        { k: "ville", label: "Ville", kb: "default" },
        { k: "region", label: "Région (optionnel)", kb: "default" },
        { k: "pays", label: "Pays", kb: "default" },
        { k: "code_postal", label: "Code postal (optionnel)", kb: "default" },
    ];

    return (
        <View style={styles.container}>
            <ScrollView contentContainerStyle={{ padding: 12, gap: 12, paddingBottom: 90 }}>
                {addresses.length === 0 ? (
                    <View style={styles.empty}>
                        <Ionicons name="location-outline" size={56} color="#d1d5db" />
                        <Text style={styles.emptyText}>Aucune adresse enregistrée</Text>
                    </View>
                ) : (
                    addresses.map((a) => (
                        <View key={String(a.id)} style={styles.card}>
                            <View style={styles.cardTop}>
                                <Text style={styles.name}>{a.nom}</Text>
                                <Text style={styles.phone}>{a.telephone}</Text>
                                {a.is_default && (
                                    <View style={styles.defaultPill}>
                                        <Text style={styles.defaultText}>Par défaut</Text>
                                    </View>
                                )}
                            </View>
                            <Text style={styles.addr}>
                                {[a.adresse, a.ville, a.region, a.pays]
                                    .filter(Boolean)
                                    .join(", ")}
                            </Text>
                            <View style={styles.cardActions}>
                                {!a.is_default && (
                                    <TouchableOpacity
                                        style={styles.actionBtn}
                                        onPress={() => setDefault(a)}
                                    >
                                        <Ionicons
                                            name="star-outline"
                                            size={16}
                                            color={COLORS.primaryDark}
                                        />
                                        <Text style={styles.actionText}>Par défaut</Text>
                                    </TouchableOpacity>
                                )}
                                <TouchableOpacity
                                    style={styles.actionBtn}
                                    onPress={() => openEdit(a)}
                                >
                                    <Ionicons
                                        name="create-outline"
                                        size={16}
                                        color={COLORS.primaryDark}
                                    />
                                    <Text style={styles.actionText}>Modifier</Text>
                                </TouchableOpacity>
                                <TouchableOpacity
                                    style={styles.actionBtn}
                                    onPress={() => remove(a)}
                                >
                                    <Ionicons name="trash-outline" size={16} color="#dc2626" />
                                    <Text style={[styles.actionText, { color: "#dc2626" }]}>
                                        Supprimer
                                    </Text>
                                </TouchableOpacity>
                            </View>
                        </View>
                    ))
                )}
            </ScrollView>

            {/* Bouton ajouter */}
            <View style={[styles.footer, { paddingBottom: insets.bottom + 12 }]}>
                <TouchableOpacity style={styles.addBtn} onPress={openAdd} activeOpacity={0.9}>
                    <Ionicons name="add" size={20} color="#fff" />
                    <Text style={styles.addText}>Ajouter une adresse</Text>
                </TouchableOpacity>
            </View>

            {/* Formulaire modal */}
            <Modal visible={modal} animationType="slide" transparent onRequestClose={() => setModal(false)}>
                <View style={styles.modalRoot}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === "ios" ? "padding" : undefined}
                        style={styles.sheet}
                    >
                        <View style={styles.sheetHead}>
                            <Text style={styles.sheetTitle}>
                                {editing ? "Modifier l'adresse" : "Nouvelle adresse"}
                            </Text>
                            <TouchableOpacity onPress={() => setModal(false)}>
                                <Ionicons name="close" size={24} color={COLORS.text} />
                            </TouchableOpacity>
                        </View>
                        <ScrollView keyboardShouldPersistTaps="handled">
                            {FIELDS.map((f) => (
                                <View key={f.k} style={styles.field}>
                                    <Text style={styles.fieldLabel}>{f.label}</Text>
                                    <TextInput
                                        style={styles.input}
                                        value={String(form[f.k] ?? "")}
                                        onChangeText={(v) => setField(f.k, v)}
                                        keyboardType={f.kb}
                                        placeholderTextColor="#9ca3af"
                                    />
                                </View>
                            ))}
                            <View style={styles.switchRow}>
                                <Text style={styles.fieldLabel}>
                                    Définir comme adresse par défaut
                                </Text>
                                <Switch
                                    value={form.is_default}
                                    onValueChange={(v) => setField("is_default", v)}
                                    trackColor={{ true: COLORS.primary }}
                                />
                            </View>
                            <TouchableOpacity
                                style={styles.saveBtn}
                                onPress={save}
                                disabled={saving}
                                activeOpacity={0.9}
                            >
                                <Text style={styles.saveText}>
                                    {saving ? "Enregistrement..." : "Enregistrer"}
                                </Text>
                            </TouchableOpacity>
                            <View style={{ height: 20 }} />
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </Modal>
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.bg },
    center: { flex: 1, justifyContent: "center", alignItems: "center", backgroundColor: COLORS.bg },
    empty: { alignItems: "center", paddingTop: 70, gap: 12 },
    emptyText: { color: COLORS.textLight, fontSize: 14 },
    card: { backgroundColor: "#fff", borderRadius: RADIUS.md, padding: 14, elevation: 1 },
    cardTop: { flexDirection: "row", alignItems: "center", gap: 10 },
    name: { fontWeight: "800", color: COLORS.text, fontSize: 15 },
    phone: { color: COLORS.textLight, fontSize: 13 },
    defaultPill: {
        marginLeft: "auto",
        backgroundColor: COLORS.soft,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 8,
        paddingVertical: 3,
    },
    defaultText: { color: COLORS.primaryDark, fontSize: 10.5, fontWeight: "700" },
    addr: { color: COLORS.textLight, fontSize: 13, marginTop: 8, lineHeight: 19 },
    cardActions: {
        flexDirection: "row",
        gap: 18,
        marginTop: 12,
        borderTopWidth: 1,
        borderTopColor: "#f3f4f6",
        paddingTop: 10,
    },
    actionBtn: { flexDirection: "row", alignItems: "center", gap: 4 },
    actionText: { color: COLORS.primaryDark, fontSize: 12.5, fontWeight: "600" },
    footer: {
        position: "absolute",
        bottom: 0,
        left: 0,
        right: 0,
        padding: 12,
        backgroundColor: "#fff",
        borderTopWidth: 1,
        borderTopColor: "#eee",
    },
    addBtn: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 8,
        backgroundColor: COLORS.primaryDark,
        borderRadius: RADIUS.pill,
        paddingVertical: 15,
    },
    addText: { color: "#fff", fontWeight: "800", fontSize: 15 },
    modalRoot: { flex: 1, backgroundColor: "rgba(0,0,0,0.45)", justifyContent: "flex-end" },
    sheet: {
        backgroundColor: "#fff",
        borderTopLeftRadius: RADIUS.lg,
        borderTopRightRadius: RADIUS.lg,
        paddingHorizontal: 16,
        paddingTop: 16,
        maxHeight: "88%",
    },
    sheetHead: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: 10,
    },
    sheetTitle: { fontSize: 17, fontWeight: "800", color: COLORS.text },
    field: { marginBottom: 12 },
    fieldLabel: { fontSize: 13, color: COLORS.textLight, marginBottom: 6 },
    input: {
        backgroundColor: "#f3f4f6",
        borderRadius: RADIUS.sm,
        paddingHorizontal: 14,
        paddingVertical: 12,
        fontSize: 15,
        color: COLORS.text,
    },
    switchRow: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginVertical: 6,
    },
    saveBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: RADIUS.pill,
        paddingVertical: 15,
        alignItems: "center",
        marginTop: 14,
    },
    saveText: { color: "#fff", fontWeight: "800", fontSize: 15 },
});
