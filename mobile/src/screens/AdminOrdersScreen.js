import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    ScrollView,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
    RefreshControl,
    Modal,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

// Statuts sélectionnables + libellés + couleur
const STATUSES = [
    { key: "en_attente", label: "En attente de paiement", color: "#f59e0b" },
    { key: "paiement_declare", label: "Paiement à vérifier", color: "#f59e0b" },
    { key: "payee", label: "Payée", color: "#6d28d9" },
    { key: "traitement", label: "En préparation", color: "#0ea5e9" },
    { key: "expedie", label: "Expédiée", color: "#2563eb" },
    { key: "livre", label: "Livrée", color: "#16a34a" },
    { key: "annule", label: "Annulée", color: "#dc2626" },
];

// Filtres du haut (Tous + statuts)
const FILTERS = [{ key: "", label: "Toutes" }, ...STATUSES.map((s) => ({ key: s.key, label: s.label }))];

const statusMeta = (key) => STATUSES.find((s) => s.key === key) || { label: key, color: "#6b7280" };

export default function AdminOrdersScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const [orders, setOrders] = useState([]);
    const [filter, setFilter] = useState("");
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [selected, setSelected] = useState(null); // commande en cours de modification
    const [saving, setSaving] = useState(false);

    const load = useCallback(
        async (status = filter) => {
            try {
                const res = await api.get("/admin/orders", {
                    params: status ? { status } : {},
                });
                setOrders(res.data.data ?? []);
            } catch (e) {
                if (e?.response?.status === 403) {
                    Alert.alert("Accès refusé", "Réservé aux administrateurs.");
                    navigation.goBack();
                } else {
                    Alert.alert("Erreur", apiError(e));
                }
            } finally {
                setLoading(false);
                setRefreshing(false);
            }
        },
        [filter, navigation],
    );

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const changeFilter = (key) => {
        setFilter(key);
        setLoading(true);
        load(key);
    };

    const updateStatus = async (statut) => {
        if (!selected) return;
        setSaving(true);
        try {
            await api.post(`/admin/orders/${selected.id}/status`, { statut });
            setSelected(null);
            load();
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

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
                <Text style={styles.headerTitle}>Gestion des commandes</Text>
                <View style={{ width: 26 }} />
            </LinearGradient>

            {/* Filtres par statut */}
            <View style={{ maxHeight: 52 }}>
                <ScrollView
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    contentContainerStyle={styles.filterRow}
                >
                    {FILTERS.map((f) => {
                        const on = filter === f.key;
                        return (
                            <TouchableOpacity
                                key={f.key || "all"}
                                style={[styles.chip, on && styles.chipOn]}
                                onPress={() => changeFilter(f.key)}
                            >
                                <Text style={[styles.chipText, on && styles.chipTextOn]}>{f.label}</Text>
                            </TouchableOpacity>
                        );
                    })}
                </ScrollView>
            </View>

            <ScrollView
                contentContainerStyle={{ padding: 12, paddingBottom: 30 }}
                refreshControl={
                    <RefreshControl
                        refreshing={refreshing}
                        onRefresh={() => {
                            setRefreshing(true);
                            load();
                        }}
                        colors={[COLORS.primary]}
                    />
                }
            >
                {orders.length === 0 ? (
                    <View style={styles.empty}>
                        <Ionicons name="receipt-outline" size={48} color="#d1d5db" />
                        <Text style={styles.emptyText}>Aucune commande</Text>
                    </View>
                ) : (
                    orders.map((o) => {
                        const meta = statusMeta(o.statut);
                        return (
                            <View key={o.id} style={styles.card}>
                                <View style={styles.cardHead}>
                                    <Text style={styles.numero}>{o.numero}</Text>
                                    <Text style={styles.amount}>{formatPrice(o.total)}</Text>
                                </View>
                                <Text style={styles.client}>{o.client}</Text>
                                <Text style={styles.date}>{o.date}</Text>

                                <View style={styles.cardFooter}>
                                    <View style={[styles.badge, { backgroundColor: meta.color + "22" }]}>
                                        <Text style={[styles.badgeText, { color: meta.color }]}>
                                            {o.statut_label || meta.label}
                                        </Text>
                                    </View>
                                    <TouchableOpacity
                                        style={styles.editBtn}
                                        onPress={() => setSelected(o)}
                                    >
                                        <Ionicons name="create-outline" size={16} color="#fff" />
                                        <Text style={styles.editText}>Modifier l'état</Text>
                                    </TouchableOpacity>
                                </View>
                            </View>
                        );
                    })
                )}
            </ScrollView>

            {/* Modal de changement de statut */}
            <Modal
                visible={!!selected}
                transparent
                animationType="slide"
                onRequestClose={() => setSelected(null)}
            >
                <View style={styles.modalOverlay}>
                    <View style={styles.modalCard}>
                        <View style={styles.modalHeader}>
                            <Text style={styles.modalTitle}>
                                Changer l'état · {selected?.numero}
                            </Text>
                            <TouchableOpacity onPress={() => setSelected(null)} hitSlop={10}>
                                <Ionicons name="close" size={24} color={COLORS.text} />
                            </TouchableOpacity>
                        </View>
                        <Text style={styles.modalHint}>
                            Le client sera notifié (email + notification) du changement.
                        </Text>
                        {saving ? (
                            <ActivityIndicator color={COLORS.primary} style={{ paddingVertical: 20 }} />
                        ) : (
                            STATUSES.map((s) => {
                                const active = selected?.statut === s.key;
                                return (
                                    <TouchableOpacity
                                        key={s.key}
                                        style={[styles.statusRow, active && styles.statusRowActive]}
                                        onPress={() => updateStatus(s.key)}
                                        disabled={active}
                                    >
                                        <View style={[styles.dot, { backgroundColor: s.color }]} />
                                        <Text style={styles.statusLabel}>{s.label}</Text>
                                        {active && (
                                            <Ionicons name="checkmark" size={18} color={COLORS.primary} />
                                        )}
                                    </TouchableOpacity>
                                );
                            })
                        )}
                    </View>
                </View>
            </Modal>
        </View>
    );
}

const styles = StyleSheet.create({
    center: { flex: 1, justifyContent: "center", alignItems: "center", backgroundColor: COLORS.bg },
    header: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingHorizontal: 12,
        paddingBottom: 14,
    },
    headerTitle: { fontSize: 17, fontWeight: "800", color: "#fff" },
    filterRow: { paddingHorizontal: 12, paddingVertical: 8, gap: 8 },
    chip: {
        paddingHorizontal: 14,
        paddingVertical: 8,
        borderRadius: RADIUS.pill,
        backgroundColor: "#fff",
    },
    chipOn: { backgroundColor: COLORS.primaryDark },
    chipText: { fontWeight: "700", color: COLORS.text, fontSize: 12.5 },
    chipTextOn: { color: "#fff" },
    empty: { alignItems: "center", paddingTop: 60, gap: 12 },
    emptyText: { color: COLORS.textLight, fontSize: 14 },
    card: { backgroundColor: "#fff", borderRadius: RADIUS.md, padding: 14, marginBottom: 10 },
    cardHead: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
    numero: { fontSize: 15, fontWeight: "800", color: COLORS.text },
    amount: { fontSize: 16, fontWeight: "900", color: COLORS.accent },
    client: { fontSize: 13.5, color: COLORS.textLight, marginTop: 4 },
    date: { fontSize: 11.5, color: "#9ca3af", marginTop: 2 },
    cardFooter: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        marginTop: 12,
    },
    badge: { paddingHorizontal: 10, paddingVertical: 5, borderRadius: RADIUS.pill },
    badgeText: { fontSize: 12, fontWeight: "800" },
    editBtn: {
        flexDirection: "row",
        alignItems: "center",
        gap: 5,
        backgroundColor: COLORS.primaryDark,
        paddingHorizontal: 12,
        paddingVertical: 8,
        borderRadius: RADIUS.md,
    },
    editText: { color: "#fff", fontWeight: "700", fontSize: 12.5 },
    modalOverlay: { flex: 1, backgroundColor: "rgba(0,0,0,0.45)", justifyContent: "flex-end" },
    modalCard: {
        backgroundColor: "#fff",
        borderTopLeftRadius: 20,
        borderTopRightRadius: 20,
        padding: 18,
        paddingBottom: 30,
    },
    modalHeader: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
    modalTitle: { fontSize: 16, fontWeight: "800", color: COLORS.text },
    modalHint: { fontSize: 12, color: COLORS.textLight, marginTop: 4, marginBottom: 10 },
    statusRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 12,
        paddingVertical: 13,
        borderBottomWidth: 1,
        borderBottomColor: "#f1f1f1",
    },
    statusRowActive: { opacity: 0.5 },
    dot: { width: 12, height: 12, borderRadius: 6 },
    statusLabel: { flex: 1, fontSize: 14.5, fontWeight: "600", color: COLORS.text },
});
