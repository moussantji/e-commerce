import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    ScrollView,
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
import { useAuth } from "../context/AuthContext";
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

export default function SellerHubScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const { user } = useAuth();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [error, setError] = useState(null);

    const load = useCallback(async () => {
        setError(null);
        try {
            const { data: payload } = await api.get("/seller/dashboard");
            setData(payload);
        } catch (e) {
            if (e?.response?.status === 403) {
                setError("Cet espace est réservé aux vendeurs.");
            } else {
                setError(apiError(e));
            }
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

    const refresh = () => {
        setRefreshing(true);
        load();
    };

    const openProducts = (openCreate = false) => {
        navigation.navigate("SellerProducts", {
            resource: "seller-products",
            title: "Mes produits",
            basePath: "/seller/products",
            optionsPath: "/seller/options",
            ...(openCreate ? { openCreate: Date.now() } : {}),
        });
    };

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    if (error) {
        return (
            <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
                <Header insets={insets} navigation={navigation} />
                <ErrorState
                    title="Espace vendeur indisponible"
                    message={error}
                    onRetry={() => {
                        setLoading(true);
                        load();
                    }}
                />
            </View>
        );
    }

    const stats = data?.stats || {};
    const orders = data?.recent_orders || [];
    const topProducts = data?.top_products || [];
    const maxSold = Math.max(1, ...topProducts.map((p) => Number(p.sold) || 0));

    return (
        <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
            <Header insets={insets} navigation={navigation} />

            <ScrollView
                contentContainerStyle={{
                    padding: 12,
                    paddingBottom: insets.bottom + 30,
                }}
                refreshControl={
                    <RefreshControl
                        refreshing={refreshing}
                        onRefresh={refresh}
                        colors={[COLORS.primary]}
                    />
                }
            >
                <LinearGradient
                    colors={COLORS.gradient}
                    start={COLORS.gradientStart}
                    end={COLORS.gradientEnd}
                    style={styles.welcome}
                >
                    <View style={styles.welcomeIcon}>
                        <Ionicons name="storefront-outline" size={26} color="#fff" />
                    </View>
                    <View style={{ flex: 1 }}>
                        <Text style={styles.welcomeTitle}>
                            Bonjour {user?.name || "vendeur"}
                        </Text>
                        <Text style={styles.welcomeText}>
                            Gérez vos produits et suivez vos ventes.
                        </Text>
                    </View>
                </LinearGradient>

                <View style={styles.statsGrid}>
                    <StatCard
                        icon="cube-outline"
                        value={stats.products ?? 0}
                        label="Mes produits"
                        hint="dans mon catalogue"
                    />
                    <StatCard
                        icon="receipt-outline"
                        value={stats.orders ?? 0}
                        label="Commandes"
                        hint="contenant mes produits"
                    />
                    <StatCard
                        icon="wallet-outline"
                        value={formatPrice(stats.revenue || 0)}
                        label="Revenus"
                        hint="total de mes ventes"
                        valueStyle={styles.statRevenue}
                    />
                    <StatCard
                        icon="alert-circle-outline"
                        value={stats.low_stock ?? 0}
                        label="Stock bas"
                        hint="produits à ≤ 5 unités"
                        danger={Number(stats.low_stock) > 0}
                    />
                </View>

                <View style={styles.quickRow}>
                    <QuickAction
                        icon="cube-outline"
                        label="Mes produits"
                        onPress={() => openProducts()}
                    />
                    <QuickAction
                        icon="receipt-outline"
                        label="Commandes"
                        onPress={() => navigation.navigate("SellerOrders")}
                    />
                    <QuickAction
                        icon="add-circle-outline"
                        label="Ajouter"
                        onPress={() => openProducts(true)}
                    />
                </View>

                <View style={styles.sectionHead}>
                    <Text style={styles.sectionTitle}>Dernières commandes</Text>
                    <TouchableOpacity
                        onPress={() => navigation.navigate("SellerOrders")}
                        hitSlop={8}
                    >
                        <Text style={styles.seeAll}>Voir tout</Text>
                    </TouchableOpacity>
                </View>

                {orders.length === 0 ? (
                    <View style={styles.emptyCard}>
                        <Ionicons name="receipt-outline" size={34} color="#d1d5db" />
                        <Text style={styles.emptyText}>
                            Aucune commande reçue pour le moment.
                        </Text>
                    </View>
                ) : (
                    orders.map((order) => (
                        <TouchableOpacity
                            key={String(order.id)}
                            style={styles.orderCard}
                            activeOpacity={0.85}
                            onPress={() => navigation.navigate("SellerOrders")}
                        >
                            <View style={styles.orderHead}>
                                <Text style={styles.orderNumero} numberOfLines={1}>
                                    {order.numero}
                                </Text>
                                <View
                                    style={[
                                        styles.statusBadge,
                                        {
                                            backgroundColor:
                                                statusColor(order.statut) + "20",
                                        },
                                    ]}
                                >
                                    <Text
                                        style={[
                                            styles.statusText,
                                            { color: statusColor(order.statut) },
                                        ]}
                                    >
                                        {statusLabel(order)}
                                    </Text>
                                </View>
                            </View>
                            <Text style={styles.orderClient} numberOfLines={1}>
                                {order.client}
                            </Text>
                            <Text style={styles.orderItems} numberOfLines={2}>
                                {(order.items || [])
                                    .map((it) => `${it.name} ×${it.quantity}`)
                                    .join(" · ") || "—"}
                            </Text>
                            <View style={styles.orderFoot}>
                                <Text style={styles.orderDate}>{order.date}</Text>
                                <Text style={styles.orderAmount}>
                                    {formatPrice(order.seller_total ?? order.total)}
                                </Text>
                            </View>
                        </TouchableOpacity>
                    ))
                )}

                <View style={styles.sectionHead}>
                    <Text style={styles.sectionTitle}>Mes meilleures ventes</Text>
                </View>

                {topProducts.length === 0 ? (
                    <View style={styles.emptyCard}>
                        <Ionicons name="trending-up-outline" size={34} color="#d1d5db" />
                        <Text style={styles.emptyText}>
                            Aucune vente pour le moment.
                        </Text>
                    </View>
                ) : (
                    <View style={styles.topCard}>
                        {topProducts.map((product, index) => (
                            <View key={String(product.id)} style={styles.topRow}>
                                <View style={styles.topRank}>
                                    <Text style={styles.topRankText}>{index + 1}</Text>
                                </View>
                                <View style={{ flex: 1 }}>
                                    <Text style={styles.topName} numberOfLines={1}>
                                        {product.name}
                                    </Text>
                                    <View style={styles.barTrack}>
                                        <View
                                            style={[
                                                styles.barFill,
                                                {
                                                    width: `${Math.max(
                                                        4,
                                                        (Number(product.sold) || 0) /
                                                            maxSold *
                                                            100,
                                                    )}%`,
                                                },
                                            ]}
                                        />
                                    </View>
                                </View>
                                <Text style={styles.topSold}>
                                    {product.sold ?? 0}
                                </Text>
                            </View>
                        ))}
                    </View>
                )}
            </ScrollView>
        </View>
    );
}

function Header({ insets, navigation }) {
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
            <Text style={styles.headerTitle}>Espace vendeur</Text>
            <View style={{ width: 26 }} />
        </LinearGradient>
    );
}

function StatCard({ icon, value, label, hint, danger, valueStyle }) {
    return (
        <View style={styles.statCard}>
            <View style={styles.statTop}>
                <View style={[styles.statIcon, danger && styles.statIconDanger]}>
                    <Ionicons
                        name={icon}
                        size={18}
                        color={danger ? "#dc2626" : COLORS.primaryDark}
                    />
                </View>
                <Text
                    style={[
                        styles.statValue,
                        danger && { color: "#dc2626" },
                        valueStyle,
                    ]}
                    numberOfLines={1}
                >
                    {value}
                </Text>
            </View>
            <Text style={styles.statLabel}>{label}</Text>
            <Text style={styles.statHint}>{hint}</Text>
        </View>
    );
}

function QuickAction({ icon, label, onPress }) {
    return (
        <TouchableOpacity
            style={styles.quick}
            activeOpacity={0.8}
            onPress={onPress}
        >
            <Ionicons name={icon} size={22} color={COLORS.primaryDark} />
            <Text style={styles.quickLabel}>{label}</Text>
        </TouchableOpacity>
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
        justifyContent: "space-between",
        paddingHorizontal: 12,
        paddingBottom: 14,
    },
    headerTitle: { fontSize: 17, fontWeight: "800", color: "#fff" },
    welcome: {
        flexDirection: "row",
        alignItems: "center",
        gap: 12,
        borderRadius: RADIUS.lg,
        padding: 16,
    },
    welcomeIcon: {
        width: 48,
        height: 48,
        borderRadius: 24,
        backgroundColor: "rgba(255,255,255,0.18)",
        alignItems: "center",
        justifyContent: "center",
    },
    welcomeTitle: { color: "#fff", fontSize: 16, fontWeight: "800" },
    welcomeText: { color: "rgba(255,255,255,0.85)", fontSize: 12.5, marginTop: 3 },
    statsGrid: {
        flexDirection: "row",
        flexWrap: "wrap",
        justifyContent: "space-between",
        marginTop: 12,
        rowGap: 10,
    },
    statCard: {
        width: "48.5%",
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 13,
        minHeight: 108,
    },
    statTop: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        gap: 6,
    },
    statIcon: {
        width: 32,
        height: 32,
        borderRadius: 16,
        backgroundColor: COLORS.soft,
        alignItems: "center",
        justifyContent: "center",
    },
    statIconDanger: { backgroundColor: "#fee2e2" },
    statValue: { fontSize: 19, fontWeight: "800", color: COLORS.text, flexShrink: 1 },
    statRevenue: { fontSize: 15 },
    statLabel: { fontSize: 13, fontWeight: "800", color: COLORS.text, marginTop: 9 },
    statHint: { fontSize: 10.5, color: COLORS.textLight, marginTop: 3 },
    quickRow: { flexDirection: "row", gap: 10, marginTop: 12 },
    quick: {
        flex: 1,
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        paddingVertical: 13,
        alignItems: "center",
        gap: 6,
    },
    quickLabel: { fontSize: 11.5, fontWeight: "700", color: COLORS.text },
    sectionHead: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginTop: 20,
        marginBottom: 10,
        paddingHorizontal: 2,
    },
    sectionTitle: { fontSize: 15.5, fontWeight: "800", color: COLORS.text },
    seeAll: { color: COLORS.primaryDark, fontWeight: "700", fontSize: 12.5 },
    emptyCard: {
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 24,
        alignItems: "center",
        gap: 8,
    },
    emptyText: {
        color: COLORS.textLight,
        fontSize: 13,
        textAlign: "center",
    },
    orderCard: {
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 13,
        marginBottom: 8,
    },
    orderHead: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        gap: 8,
    },
    orderNumero: {
        flex: 1,
        fontSize: 13.5,
        fontWeight: "800",
        color: COLORS.text,
    },
    statusBadge: {
        borderRadius: RADIUS.pill,
        paddingHorizontal: 8,
        paddingVertical: 3,
    },
    statusText: { fontSize: 10, fontWeight: "800" },
    orderClient: {
        fontSize: 13,
        fontWeight: "600",
        color: COLORS.text,
        marginTop: 7,
    },
    orderItems: {
        fontSize: 11.5,
        color: COLORS.textLight,
        marginTop: 4,
        lineHeight: 16,
    },
    orderFoot: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginTop: 9,
    },
    orderDate: { fontSize: 11, color: COLORS.muted },
    orderAmount: { fontSize: 13, fontWeight: "800", color: COLORS.primaryDark },
    topCard: {
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 13,
        gap: 13,
    },
    topRow: { flexDirection: "row", alignItems: "center", gap: 10 },
    topRank: {
        width: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: COLORS.soft,
        alignItems: "center",
        justifyContent: "center",
    },
    topRankText: { fontSize: 11, fontWeight: "800", color: COLORS.primaryDark },
    topName: { fontSize: 12.5, fontWeight: "700", color: COLORS.text },
    barTrack: {
        height: 6,
        borderRadius: 3,
        backgroundColor: "#f0eefc",
        marginTop: 6,
        overflow: "hidden",
    },
    barFill: {
        height: 6,
        borderRadius: 3,
        backgroundColor: COLORS.primary,
    },
    topSold: {
        minWidth: 24,
        textAlign: "right",
        fontSize: 12,
        fontWeight: "800",
        color: COLORS.primaryDark,
    },
});
