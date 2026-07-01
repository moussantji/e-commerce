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
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const TABS = [
    { key: "payments", label: "Commandes" },
    { key: "topups", label: "Rechargements" },
];

export default function AdminPaymentsScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const [tab, setTab] = useState("payments");
    const [payments, setPayments] = useState([]);
    const [topups, setTopups] = useState([]);
    const [summary, setSummary] = useState(null);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [actioning, setActioning] = useState(null);

    const load = useCallback(async () => {
        try {
            const [s, p, t] = await Promise.all([
                api.get("/admin/summary"),
                api.get("/admin/payments"),
                api.get("/admin/topups"),
            ]);
            setSummary(s.data);
            setPayments(p.data.data ?? []);
            setTopups(t.data.data ?? []);
        } catch (e) {
            if (e?.response?.status === 403) {
                Alert.alert("Accès refusé", "Réservé aux administrateurs.");
                navigation.goBack();
            }
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    }, [navigation]);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const act = async (kind, id, action) => {
        const key = `${kind}-${id}-${action}`;
        setActioning(key);
        try {
            const base = kind === "payment" ? "payments" : "topups";
            await api.post(`/admin/${base}/${id}/${action}`);
            load();
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setActioning(null);
        }
    };

    const confirmReject = (kind, id) => {
        Alert.alert("Rejeter", "Confirmer le rejet de ce paiement ?", [
            { text: "Annuler", style: "cancel" },
            { text: "Rejeter", style: "destructive", onPress: () => act(kind, id, "reject") },
        ]);
    };

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    const list = tab === "payments" ? payments : topups;
    const kind = tab === "payments" ? "payment" : "topup";

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
                <Text style={styles.headerTitle}>Espace vendeur</Text>
                <View style={{ width: 26 }} />
            </LinearGradient>

            {/* Résumé */}
            {summary && (
                <View style={styles.summaryRow}>
                    <View style={styles.summaryCard}>
                        <Text style={styles.summaryNum}>{summary.pending_payments}</Text>
                        <Text style={styles.summaryLabel}>Paiements</Text>
                    </View>
                    <View style={styles.summaryCard}>
                        <Text style={styles.summaryNum}>{summary.pending_topups}</Text>
                        <Text style={styles.summaryLabel}>Rechargements</Text>
                    </View>
                    <View style={styles.summaryCard}>
                        <Text style={styles.summaryNum}>{summary.orders_today}</Text>
                        <Text style={styles.summaryLabel}>Cmd. aujourd'hui</Text>
                    </View>
                </View>
            )}

            {/* Onglets */}
            <View style={styles.tabBar}>
                {TABS.map((t) => {
                    const on = tab === t.key;
                    const count = t.key === "payments" ? payments.length : topups.length;
                    return (
                        <TouchableOpacity
                            key={t.key}
                            style={[styles.tab, on && styles.tabOn]}
                            onPress={() => setTab(t.key)}
                        >
                            <Text style={[styles.tabText, on && styles.tabTextOn]}>
                                {t.label} ({count})
                            </Text>
                        </TouchableOpacity>
                    );
                })}
            </View>

            <ScrollView
                contentContainerStyle={{ padding: 12, paddingBottom: 30 }}
                refreshControl={
                    <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} colors={[COLORS.primary]} />
                }
            >
                {list.length === 0 ? (
                    <View style={styles.empty}>
                        <Ionicons name="checkmark-done-outline" size={48} color="#d1d5db" />
                        <Text style={styles.emptyText}>Rien à vérifier pour le moment</Text>
                    </View>
                ) : (
                    list.map((item) => (
                        <View key={item.id} style={styles.itemCard}>
                            <View style={styles.itemHead}>
                                <Text style={styles.itemClient}>{item.client}</Text>
                                <Text style={styles.itemAmount}>{formatPrice(item.amount)}</Text>
                            </View>
                            <Text style={styles.itemMeta}>
                                {tab === "payments"
                                    ? `Commande ${item.numero} · ${item.provider || ""}`
                                    : `${item.method || ""}`}
                                {item.phone ? ` · ${item.phone}` : ""}
                            </Text>
                            {item.reference ? (
                                <Text style={styles.itemRef}>Réf : {item.reference}</Text>
                            ) : null}
                            <Text style={styles.itemDate}>{item.date}</Text>

                            <View style={styles.actions}>
                                <TouchableOpacity
                                    style={[styles.btn, styles.reject]}
                                    onPress={() => confirmReject(kind, item.id)}
                                    disabled={!!actioning}
                                >
                                    <Text style={styles.rejectText}>Rejeter</Text>
                                </TouchableOpacity>
                                <TouchableOpacity
                                    style={[styles.btn, styles.confirm]}
                                    onPress={() => act(kind, item.id, "confirm")}
                                    disabled={!!actioning}
                                >
                                    {actioning === `${kind}-${item.id}-confirm` ? (
                                        <ActivityIndicator color="#fff" size="small" />
                                    ) : (
                                        <Text style={styles.confirmText}>Confirmer</Text>
                                    )}
                                </TouchableOpacity>
                            </View>
                        </View>
                    ))
                )}
            </ScrollView>
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
    summaryRow: { flexDirection: "row", gap: 10, padding: 12 },
    summaryCard: {
        flex: 1,
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        paddingVertical: 14,
        alignItems: "center",
    },
    summaryNum: { fontSize: 22, fontWeight: "900", color: COLORS.primaryDark },
    summaryLabel: { fontSize: 11, color: COLORS.textLight, marginTop: 2 },
    tabBar: { flexDirection: "row", paddingHorizontal: 12, gap: 8 },
    tab: {
        flex: 1,
        paddingVertical: 10,
        borderRadius: RADIUS.pill,
        backgroundColor: "#fff",
        alignItems: "center",
    },
    tabOn: { backgroundColor: COLORS.primaryDark },
    tabText: { fontWeight: "700", color: COLORS.text, fontSize: 13 },
    tabTextOn: { color: "#fff" },
    empty: { alignItems: "center", paddingTop: 60, gap: 12 },
    emptyText: { color: COLORS.textLight, fontSize: 14 },
    itemCard: { backgroundColor: "#fff", borderRadius: RADIUS.md, padding: 14, marginBottom: 10 },
    itemHead: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
    itemClient: { fontSize: 15, fontWeight: "800", color: COLORS.text },
    itemAmount: { fontSize: 16, fontWeight: "900", color: COLORS.accent },
    itemMeta: { fontSize: 13, color: COLORS.textLight, marginTop: 4 },
    itemRef: { fontSize: 12.5, color: "#374151", marginTop: 3 },
    itemDate: { fontSize: 11.5, color: "#9ca3af", marginTop: 3 },
    actions: { flexDirection: "row", gap: 10, marginTop: 12 },
    btn: { flex: 1, paddingVertical: 11, borderRadius: RADIUS.md, alignItems: "center" },
    reject: { borderWidth: 1.5, borderColor: "#dc2626" },
    rejectText: { color: "#dc2626", fontWeight: "700" },
    confirm: { backgroundColor: COLORS.primaryDark },
    confirmText: { color: "#fff", fontWeight: "800" },
});
