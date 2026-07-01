import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    ScrollView,
    Image,
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
    { key: "all", label: "Tout" },
    { key: "en_attente", label: "À payer", match: ["en_attente"] },
    { key: "verif", label: "En vérification", match: ["paiement_declare"] },
    { key: "route", label: "En cours", match: ["payee", "traitement", "expedie"] },
    { key: "livre", label: "Livré", match: ["livre"] },
    { key: "annule", label: "Annulé", match: ["annule"] },
];

const STATUS_LABELS = {
    en_attente: "En attente de paiement",
    paiement_declare: "Paiement en vérification",
    payee: "Payée — en préparation",
    traitement: "En préparation",
    expedie: "Expédiée",
    livre: "Livrée",
    annule: "Commande annulée",
};

export default function OrdersScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [error, setError] = useState(null);
    const [tab, setTab] = useState("all");

    const load = useCallback(async () => {
        setError(null);
        try {
            const { data } = await api.get("/orders", {
                params: { per_page: 30 },
            });
            setOrders(data.data ?? []);
        } catch (e) {
            setError(apiError(e));
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const activeTab = TABS.find((t) => t.key === tab);
    const filtered = orders.filter(
        (o) => tab === "all" || (activeTab?.match || []).includes(o.statut),
    );

    const removeOrder = (order) => {
        Alert.alert("Supprimer", "Retirer cette commande de la liste ?", [
            { text: "Annuler", style: "cancel" },
            {
                text: "Supprimer",
                style: "destructive",
                onPress: () =>
                    setOrders((prev) => prev.filter((o) => o.id !== order.id)),
            },
        ]);
    };

    const renderOrder = (order) => {
        const items = order.items ?? [];
        const cancelled = order.statut === "annule";
        return (
            <TouchableOpacity
                key={String(order.id)}
                style={styles.card}
                activeOpacity={0.9}
                onPress={() =>
                    navigation.navigate("OrderDetail", { id: order.id })
                }
            >
                {/* En-tête : numéro + statut */}
                <View style={styles.cardHead}>
                    <Text style={styles.vendor} numberOfLines={1}>
                        Commande N° {order.numero}
                    </Text>
                    <Text
                        style={[
                            styles.statusTag,
                            cancelled && { color: "#9ca3af" },
                        ]}
                    >
                        {STATUS_LABELS[order.statut] || order.statut_label}
                    </Text>
                </View>

                {/* Articles */}
                {items.length === 0 ? (
                    <Text style={styles.noItems}>
                        {order.items_count ?? 0} article(s)
                    </Text>
                ) : (
                    items.map((it, idx) => (
                        <View key={`${order.id}-${idx}`} style={styles.itemRow}>
                            <View>
                                <Image
                                    source={{ uri: it.image }}
                                    style={styles.itemImg}
                                />
                                {cancelled && (
                                    <View style={styles.canceledOverlay}>
                                        <Text style={styles.canceledText}>
                                            Annulé
                                        </Text>
                                    </View>
                                )}
                            </View>
                            <View style={styles.itemInfo}>
                                <Text style={styles.itemName} numberOfLines={2}>
                                    {it.name}
                                </Text>
                                <Text style={styles.logistics}>
                                    Méthodes logistiques : Standard
                                </Text>
                                <Text style={styles.qty}>x{it.quantity}</Text>
                            </View>
                            <Text style={styles.itemPrice}>
                                {formatPrice(it.line_total)}
                            </Text>
                        </View>
                    ))
                )}

                {/* Raison d'annulation */}
                {cancelled && (
                    <Text style={styles.cancelReason}>
                        Annulé par le système, délai de paiement de la commande
                        dépassé.
                    </Text>
                )}

                {/* Pied : total + action */}
                <View style={styles.divider} />
                <View style={styles.cardFoot}>
                    <Text style={styles.footTotal}>
                        Total : {formatPrice(order.total)}
                    </Text>

                    {order.statut === "en_attente" ? (
                        <TouchableOpacity
                            activeOpacity={0.85}
                            onPress={() =>
                                navigation.navigate("Payment", {
                                    orderId: order.id,
                                    numero: order.numero,
                                    total: order.total,
                                })
                            }
                        >
                            <LinearGradient
                                colors={COLORS.gradient}
                                start={COLORS.gradientStart}
                                end={COLORS.gradientEnd}
                                style={styles.payBtn}
                            >
                                <Text style={styles.payText}>
                                    PAYEZ MAINTENANT
                                </Text>
                            </LinearGradient>
                        </TouchableOpacity>
                    ) : cancelled ? (
                        <TouchableOpacity
                            style={styles.deleteBtn}
                            onPress={() => removeOrder(order)}
                            activeOpacity={0.8}
                        >
                            <Text style={styles.deleteText}>Supprimer</Text>
                        </TouchableOpacity>
                    ) : (
                        <TouchableOpacity
                            style={styles.trackBtn}
                            activeOpacity={0.8}
                            onPress={() =>
                                navigation.navigate("OrderDetail", { id: order.id })
                            }
                        >
                            <Text style={styles.trackText}>
                                Suivre la commande
                            </Text>
                        </TouchableOpacity>
                    )}
                </View>
            </TouchableOpacity>
        );
    };

    return (
        <View style={styles.container}>
            {/* En-tête */}
            <View style={[styles.header, { paddingTop: insets.top + 8 }]}>
                <View style={styles.headerIcon} />
                <Text style={styles.headerTitle}>Ma Commande</Text>
                <TouchableOpacity style={styles.headerIcon}>
                    <Ionicons name="funnel-outline" size={20} color={COLORS.text} />
                </TouchableOpacity>
            </View>

            {/* Onglets de statut */}
            <View style={styles.tabsWrap}>
                <ScrollView
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    contentContainerStyle={styles.tabs}
                >
                    {TABS.map((t) => {
                        const on = tab === t.key;
                        return (
                            <TouchableOpacity
                                key={t.key}
                                style={styles.tab}
                                onPress={() => setTab(t.key)}
                            >
                                <Text
                                    style={[
                                        styles.tabText,
                                        on && styles.tabTextOn,
                                    ]}
                                >
                                    {t.label}
                                </Text>
                                {on && <View style={styles.tabUnderline} />}
                            </TouchableOpacity>
                        );
                    })}
                </ScrollView>
            </View>

            {loading ? (
                <View style={styles.center}>
                    <ActivityIndicator size="large" color={COLORS.primary} />
                </View>
            ) : error ? (
                <View style={styles.center}>
                    <Text style={styles.errorText}>{error}</Text>
                    <TouchableOpacity style={styles.retry} onPress={load}>
                        <Text style={styles.retryText}>Réessayer</Text>
                    </TouchableOpacity>
                </View>
            ) : (
                <ScrollView
                    showsVerticalScrollIndicator={false}
                    contentContainerStyle={{ padding: 12, gap: 12, paddingBottom: 24 }}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={() => {
                                setRefreshing(true);
                                load();
                            }}
                            colors={[COLORS.primary]}
                            tintColor={COLORS.primary}
                        />
                    }
                >
                    {filtered.length === 0 ? (
                        <View style={styles.emptyWrap}>
                            <Ionicons
                                name="document-text-outline"
                                size={64}
                                color="#d1d5db"
                            />
                            <Text style={styles.emptyText}>Vide ici :-(</Text>
                        </View>
                    ) : (
                        filtered.map(renderOrder)
                    )}
                </ScrollView>
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.bg },
    center: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        padding: 24,
    },
    header: {
        flexDirection: "row",
        alignItems: "center",
        paddingHorizontal: 12,
        paddingBottom: 10,
        backgroundColor: "#fff",
    },
    headerIcon: { width: 40, height: 32, alignItems: "center", justifyContent: "center" },
    headerTitle: {
        flex: 1,
        textAlign: "center",
        fontSize: 18,
        fontWeight: "800",
        color: COLORS.text,
    },
    tabsWrap: {
        backgroundColor: "#fff",
        borderBottomWidth: 1,
        borderBottomColor: "#f0f0f0",
    },
    tabs: { gap: 18, paddingHorizontal: 16, paddingBottom: 6 },
    tab: { alignItems: "center" },
    tabText: { fontSize: 13.5, color: "#6b7280", fontWeight: "600", paddingBottom: 6 },
    tabTextOn: { color: COLORS.primaryDark, fontWeight: "800" },
    tabUnderline: {
        height: 3,
        width: 22,
        borderRadius: 3,
        backgroundColor: COLORS.primaryDark,
    },
    emptyWrap: { alignItems: "center", paddingVertical: 60, gap: 12 },
    emptyText: { color: "#6b7280", fontSize: 15 },

    card: { backgroundColor: "#fff", borderRadius: 14, padding: 14, elevation: 1 },
    cardHead: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: 10,
    },
    vendor: { fontWeight: "800", color: COLORS.text, flex: 1, marginRight: 8 },
    statusTag: { color: COLORS.primaryDark, fontWeight: "700", fontSize: 12.5 },
    noItems: { color: "#6b7280", fontSize: 13 },
    itemRow: { flexDirection: "row", gap: 10, marginBottom: 12 },
    itemImg: { width: 72, height: 72, borderRadius: 8, backgroundColor: "#e5e7eb" },
    canceledOverlay: {
        position: "absolute",
        width: 72,
        height: 72,
        borderRadius: 8,
        backgroundColor: "rgba(0,0,0,0.45)",
        alignItems: "center",
        justifyContent: "center",
    },
    canceledText: { color: "#fff", fontWeight: "700", fontSize: 12 },
    itemInfo: { flex: 1 },
    itemName: { fontSize: 13.5, color: COLORS.text, fontWeight: "500", lineHeight: 18 },
    logistics: { fontSize: 11.5, color: "#9ca3af", marginTop: 4 },
    qty: { fontSize: 12.5, color: "#6b7280", marginTop: 4 },
    itemPrice: { fontWeight: "800", color: COLORS.text, fontSize: 14 },
    cancelReason: { color: "#dc2626", fontSize: 12.5, marginTop: 2, lineHeight: 18 },
    divider: { height: 1, backgroundColor: "#f3f4f6", marginVertical: 12 },
    cardFoot: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
    },
    footTotal: { fontWeight: "800", color: COLORS.text, fontSize: 14 },
    payBtn: {
        borderRadius: RADIUS.pill,
        paddingHorizontal: 22,
        paddingVertical: 10,
    },
    payText: { color: "#fff", fontWeight: "800", fontSize: 13 },
    deleteBtn: {
        borderWidth: 1,
        borderColor: COLORS.border,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 22,
        paddingVertical: 9,
    },
    deleteText: { color: COLORS.text, fontWeight: "700", fontSize: 13 },
    trackBtn: {
        borderWidth: 1,
        borderColor: COLORS.primaryDark,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 18,
        paddingVertical: 9,
    },
    trackText: { color: COLORS.primaryDark, fontWeight: "700", fontSize: 13 },
    errorText: { color: "#b91c1c", textAlign: "center", marginBottom: 16 },
    retry: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 10,
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    retryText: { color: "#fff", fontWeight: "700" },
});
