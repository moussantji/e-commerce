import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    FlatList,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    RefreshControl,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import ErrorState from "../components/ErrorState";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const STATUS_COLORS = {
    en_attente: "#f59e0b",
    paiement_declare: "#f59e0b",
    payee: "#6d28d9",
    traitement: "#0ea5e9",
    expedie: "#2563eb",
    livre: "#16a34a",
    annule: "#dc2626",
};

const STATUS_LABELS = {
    en_attente: "En attente de paiement",
    paiement_declare: "Paiement à vérifier",
    payee: "Payée",
    traitement: "En préparation",
    expedie: "Expédiée",
    livre: "Livrée",
    annule: "Annulée",
};

function statusColor(status) {
    return STATUS_COLORS[status] || "#6b7280";
}

function statusLabel(order) {
    return order.statut_label || STATUS_LABELS[order.statut] || order.statut || "—";
}

export default function SellerOrdersScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const [orders, setOrders] = useState([]);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [loadingMore, setLoadingMore] = useState(false);
    const [error, setError] = useState(null);

    const load = useCallback(async (nextPage = 1, append = false) => {
        if (append) setLoadingMore(true);
        else setLoading(true);
        setError(null);

        try {
            const { data } = await api.get("/seller/orders", {
                params: { page: nextPage, per_page: 15 },
            });
            const rows = data.data ?? [];
            setOrders((current) => (append ? [...current, ...rows] : rows));
            setPage(data.meta?.current_page ?? nextPage);
            setLastPage(data.meta?.last_page ?? 1);
            setTotal(data.meta?.total ?? rows.length);
        } catch (e) {
            if (e?.response?.status === 403) {
                setError("Cet espace est réservé aux vendeurs.");
            } else {
                setError(apiError(e));
            }
        } finally {
            setLoading(false);
            setRefreshing(false);
            setLoadingMore(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load(1, false);
        }, [load]),
    );

    const refresh = () => {
        setRefreshing(true);
        load(1, false);
    };

    const loadMore = () => {
        if (loading || loadingMore || page >= lastPage) return;
        load(page + 1, true);
    };

    if (loading && orders.length === 0) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    if (error && orders.length === 0) {
        return (
            <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
                <Header insets={insets} navigation={navigation} />
                <ErrorState
                    title="Commandes indisponibles"
                    message={error}
                    onRetry={() => load(1, false)}
                />
            </View>
        );
    }

    return (
        <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
            <Header insets={insets} navigation={navigation} count={total} />

            <FlatList
                data={orders}
                keyExtractor={(item) => String(item.id)}
                contentContainerStyle={{
                    padding: 12,
                    paddingBottom: insets.bottom + 30,
                    flexGrow: orders.length === 0 ? 1 : 0,
                }}
                refreshControl={
                    <RefreshControl
                        refreshing={refreshing}
                        onRefresh={refresh}
                        colors={[COLORS.primary]}
                    />
                }
                onEndReached={loadMore}
                onEndReachedThreshold={0.4}
                ListEmptyComponent={
                    <View style={styles.empty}>
                        <Ionicons
                            name="receipt-outline"
                            size={48}
                            color="#d1d5db"
                        />
                        <Text style={styles.emptyTitle}>Aucune commande reçue</Text>
                        <Text style={styles.emptyText}>
                            Les commandes contenant vos produits apparaîtront ici.
                        </Text>
                    </View>
                }
                ListFooterComponent={
                    loadingMore ? (
                        <ActivityIndicator
                            color={COLORS.primary}
                            style={{ marginVertical: 16 }}
                        />
                    ) : null
                }
                renderItem={({ item }) => <SellerOrderCard order={item} />}
            />
        </View>
    );
}

function Header({ insets, navigation, count }) {
    return (
        <LinearGradient
            colors={COLORS.gradient}
            start={COLORS.gradientStart}
            end={COLORS.gradientEnd}
            style={[styles.header, { paddingTop: insets.top + 8 }]}
        >
            <TouchableOpacity onPress={() => navigation.goBack()} hitSlop={10}>
                <Ionicons name="chevron-back" size={26} color="#fff" />
            </TouchableOpacity>
            <View style={{ flex: 1, alignItems: "center" }}>
                <Text style={styles.headerTitle}>Commandes reçues</Text>
                {count != null ? (
                    <Text style={styles.headerSub}>
                        {count} commande{count > 1 ? "s" : ""}
                    </Text>
                ) : null}
            </View>
            <View style={{ width: 26 }} />
        </LinearGradient>
    );
}

function SellerOrderCard({ order }) {
    const items = order.items || [];
    const sellerTotal = Number(order.seller_total ?? 0);
    const orderTotal = Number(order.total ?? 0);
    const showOrderTotal = Math.abs(orderTotal - sellerTotal) > 0.5;

    return (
        <View style={styles.card}>
            <View style={styles.cardHead}>
                <View style={{ flex: 1 }}>
                    <Text style={styles.numero} numberOfLines={1}>
                        {order.numero}
                    </Text>
                    <Text style={styles.date}>{order.date}</Text>
                </View>
                <View
                    style={[
                        styles.badge,
                        {
                            backgroundColor:
                                statusColor(order.statut) + "20",
                        },
                    ]}
                >
                    <Text
                        style={[
                            styles.badgeText,
                            { color: statusColor(order.statut) },
                        ]}
                    >
                        {statusLabel(order)}
                    </Text>
                </View>
            </View>

            <View style={styles.clientRow}>
                <View style={styles.clientIcon}>
                    <Ionicons name="person-outline" size={16} color={COLORS.primaryDark} />
                </View>
                <View style={{ flex: 1 }}>
                    <Text style={styles.clientName} numberOfLines={1}>
                        {order.client}
                    </Text>
                    {order.client_tel ? (
                        <Text style={styles.clientTel}>{order.client_tel}</Text>
                    ) : null}
                </View>
            </View>

            <View style={styles.items}>
                {items.length === 0 ? (
                    <Text style={styles.muted}>Aucun article de votre catalogue.</Text>
                ) : (
                    items.map((item, index) => (
                        <View
                            key={`${item.product_id || index}-${index}`}
                            style={styles.itemRow}
                        >
                            <View style={styles.qtyBadge}>
                                <Text style={styles.qtyText}>×{item.quantity}</Text>
                            </View>
                            <Text style={styles.itemName} numberOfLines={2}>
                                {item.name}
                            </Text>
                            <Text style={styles.itemTotal}>
                                {formatPrice(item.line_total)}
                            </Text>
                        </View>
                    ))
                )}
            </View>

            <View style={styles.footer}>
                <View>
                    <Text style={styles.footerLabel}>Votre part</Text>
                    <Text style={styles.footerAmount}>
                        {formatPrice(sellerTotal)}
                    </Text>
                </View>
                {showOrderTotal ? (
                    <View style={{ alignItems: "flex-end" }}>
                        <Text style={styles.footerLabel}>Total commande</Text>
                        <Text style={styles.footerTotal}>
                            {formatPrice(orderTotal)}
                        </Text>
                    </View>
                ) : null}
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    center: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        backgroundColor: COLORS.bg,
    },
    header: {
        flexDirection: "row",
        alignItems: "center",
        paddingHorizontal: 12,
        paddingBottom: 12,
    },
    headerTitle: { fontSize: 17, fontWeight: "800", color: "#fff" },
    headerSub: {
        fontSize: 10.5,
        color: "rgba(255,255,255,0.8)",
        marginTop: 1,
    },
    empty: {
        flex: 1,
        alignItems: "center",
        justifyContent: "center",
        paddingHorizontal: 24,
    },
    emptyTitle: {
        fontSize: 16,
        fontWeight: "800",
        color: COLORS.text,
        marginTop: 12,
    },
    emptyText: {
        fontSize: 13,
        color: COLORS.textLight,
        textAlign: "center",
        marginTop: 6,
        lineHeight: 19,
    },
    card: {
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 13,
        marginBottom: 10,
    },
    cardHead: {
        flexDirection: "row",
        alignItems: "flex-start",
        gap: 8,
    },
    numero: { fontSize: 13.5, fontWeight: "800", color: COLORS.text },
    date: { fontSize: 11, color: COLORS.muted, marginTop: 2 },
    badge: {
        borderRadius: RADIUS.pill,
        paddingHorizontal: 8,
        paddingVertical: 3,
        maxWidth: "52%",
    },
    badgeText: { fontSize: 10, fontWeight: "800", textAlign: "center" },
    clientRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: COLORS.bg,
        borderRadius: RADIUS.sm,
        padding: 9,
        marginTop: 10,
    },
    clientIcon: {
        width: 28,
        height: 28,
        borderRadius: 14,
        backgroundColor: COLORS.soft,
        alignItems: "center",
        justifyContent: "center",
    },
    clientName: { fontSize: 12.5, fontWeight: "700", color: COLORS.text },
    clientTel: { fontSize: 11, color: COLORS.textLight, marginTop: 1 },
    items: {
        borderTopWidth: 1,
        borderTopColor: COLORS.border,
        marginTop: 10,
        paddingTop: 8,
    },
    itemRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 9,
        paddingVertical: 5,
    },
    qtyBadge: {
        minWidth: 30,
        height: 22,
        borderRadius: 11,
        backgroundColor: COLORS.soft,
        alignItems: "center",
        justifyContent: "center",
        paddingHorizontal: 6,
    },
    qtyText: { fontSize: 10.5, fontWeight: "800", color: COLORS.primaryDark },
    itemName: {
        flex: 1,
        fontSize: 12.5,
        fontWeight: "600",
        color: COLORS.text,
        lineHeight: 17,
    },
    itemTotal: {
        fontSize: 12,
        fontWeight: "700",
        color: COLORS.text,
    },
    muted: { fontSize: 12, color: COLORS.textLight },
    footer: {
        flexDirection: "row",
        alignItems: "flex-end",
        justifyContent: "space-between",
        borderTopWidth: 1,
        borderTopColor: COLORS.border,
        marginTop: 8,
        paddingTop: 10,
    },
    footerLabel: { fontSize: 10.5, color: COLORS.textLight },
    footerAmount: {
        fontSize: 15,
        fontWeight: "800",
        color: COLORS.primaryDark,
        marginTop: 2,
    },
    footerTotal: {
        fontSize: 12,
        fontWeight: "700",
        color: COLORS.textLight,
        marginTop: 2,
    },
});
