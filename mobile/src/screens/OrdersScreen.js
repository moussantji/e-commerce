import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    ScrollView,
    Image,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { useCart } from "../context/CartContext";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const STATUS_TABS = [
    { key: "all", label: "Toutes" },
    { key: "en_attente", label: "À payer" },
    { key: "traitement", label: "En cours" },
    { key: "expedie", label: "Expédié" },
    { key: "livre", label: "Avis" },
];

const STATUS_COLORS = {
    en_attente: "#f59e0b",
    traitement: "#3b82f6",
    expedie: "#8b5cf6",
    livre: "#22c55e",
    annule: "#ef4444",
};

/** Carte recommandation (hauteur variable + bouton ajout panier). */
function RecoCard({ item, onPress, onAdd }) {
    const [ar, setAr] = useState(0.85);
    const discount = item.sale_price
        ? Math.round((1 - item.sale_price / item.price) * 100)
        : 0;
    return (
        <TouchableOpacity style={styles.recoCard} activeOpacity={0.9} onPress={onPress}>
            <View>
                <Image
                    source={{ uri: item.image }}
                    style={[styles.recoImg, { aspectRatio: ar }]}
                    resizeMode="cover"
                    onLoad={(e) => {
                        const s = e?.nativeEvent?.source;
                        if (s?.width && s?.height) setAr(s.width / s.height);
                    }}
                />
                {item.sale_price ? (
                    <View style={styles.hot}>
                        <Text style={styles.hotText}>HOT</Text>
                    </View>
                ) : null}
            </View>
            <View style={styles.recoBody}>
                <Text style={styles.recoName} numberOfLines={2}>
                    {item.name}
                </Text>
                {item.rating_count ? (
                    <Text style={styles.sold}>
                        ⭐ {item.rating_avg ?? 0} · {item.rating_count}+ vendus
                    </Text>
                ) : null}
                <View style={styles.recoPriceRow}>
                    <Text style={styles.recoPrice}>
                        {formatPrice(item.sale_price ?? item.price)}
                    </Text>
                    {discount > 0 && (
                        <Text style={styles.discount}>-{discount}%</Text>
                    )}
                    <TouchableOpacity
                        style={styles.addBtn}
                        onPress={onAdd}
                        activeOpacity={0.8}
                    >
                        <Ionicons name="cart" size={16} color="#fff" />
                    </TouchableOpacity>
                </View>
            </View>
        </TouchableOpacity>
    );
}

export default function OrdersScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const { add } = useCart();

    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [status, setStatus] = useState("all");
    const [search, setSearch] = useState("");

    const [categories, setCategories] = useState([]);
    const [recos, setRecos] = useState([]);
    const [recoCat, setRecoCat] = useState("all");

    const load = useCallback(async () => {
        setError(null);
        try {
            const [o, c, r] = await Promise.all([
                api.get("/orders", { params: { per_page: 30 } }),
                api.get("/categories"),
                api.get("/products", { params: { per_page: 12 } }),
            ]);
            setOrders(o.data.data ?? []);
            setCategories(c.data.data ?? []);
            setRecos(r.data.data ?? []);
        } catch (e) {
            setError(apiError(e));
        } finally {
            setLoading(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const loadRecos = async (catId) => {
        setRecoCat(catId);
        try {
            const params = { per_page: 12 };
            if (catId !== "all") params.category_id = catId;
            const { data } = await api.get("/products", { params });
            setRecos(data.data ?? []);
        } catch (e) {
            setRecos([]);
        }
    };

    const addReco = async (p) => {
        try {
            await add(p.id, 1);
            Alert.alert("Ajouté", "Produit ajouté au panier");
        } catch (e) {
            Alert.alert("Erreur", apiError(e));
        }
    };

    const goDetail = (p) =>
        navigation.navigate("ProductDetail", { id: p.id, name: p.name });

    const filtered = orders.filter((o) => {
        const okStatus = status === "all" || o.statut === status;
        const okSearch =
            !search ||
            String(o.numero || "")
                .toLowerCase()
                .includes(search.toLowerCase());
        return okStatus && okSearch;
    });

    const recoTabs = [{ id: "all", name: "Tout" }, ...categories];
    const leftCol = recos.filter((_, i) => i % 2 === 0);
    const rightCol = recos.filter((_, i) => i % 2 === 1);

    return (
        <View style={styles.container}>
            {/* En-tête : recherche + filtre */}
            <View style={[styles.header, { paddingTop: insets.top + 8 }]}>
                <View style={styles.searchBar}>
                    <Ionicons name="search" size={18} color="#9ca3af" />
                    <TextInput
                        style={styles.searchInput}
                        placeholder="Rechercher mes commandes : n°..."
                        placeholderTextColor="#9ca3af"
                        value={search}
                        onChangeText={setSearch}
                    />
                </View>
                <TouchableOpacity style={styles.headerIcon}>
                    <Ionicons name="options-outline" size={22} color={COLORS.text} />
                </TouchableOpacity>
                <TouchableOpacity style={styles.headerIcon}>
                    <Ionicons name="ellipsis-horizontal" size={22} color={COLORS.text} />
                </TouchableOpacity>
            </View>

            {/* Onglets de statut */}
            <View style={styles.statusTabsWrap}>
                <ScrollView
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    contentContainerStyle={styles.statusTabs}
                >
                    {STATUS_TABS.map((t) => {
                        const on = status === t.key;
                        return (
                            <TouchableOpacity
                                key={t.key}
                                style={styles.statusTab}
                                onPress={() => setStatus(t.key)}
                            >
                                <Text
                                    style={[
                                        styles.statusTabText,
                                        on && styles.statusTabTextOn,
                                    ]}
                                >
                                    {t.label}
                                </Text>
                                {on && <View style={styles.statusUnderline} />}
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
                    contentContainerStyle={{ paddingBottom: 24 }}
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
                        <View style={{ padding: 12, gap: 12 }}>
                            {filtered.map((item) => (
                                <View key={String(item.id)} style={styles.card}>
                                    <View style={styles.cardHead}>
                                        <Text style={styles.numero}>
                                            Commande {item.numero}
                                        </Text>
                                        <View
                                            style={[
                                                styles.statusPill,
                                                {
                                                    backgroundColor:
                                                        (STATUS_COLORS[
                                                            item.statut
                                                        ] || "#6b7280") + "22",
                                                },
                                            ]}
                                        >
                                            <Text
                                                style={[
                                                    styles.statusText,
                                                    {
                                                        color:
                                                            STATUS_COLORS[
                                                                item.statut
                                                            ] || "#6b7280",
                                                    },
                                                ]}
                                            >
                                                {item.statut_label}
                                            </Text>
                                        </View>
                                    </View>
                                    <View style={styles.cardRow}>
                                        <Ionicons
                                            name="calendar-outline"
                                            size={15}
                                            color="#9ca3af"
                                        />
                                        <Text style={styles.meta}>
                                            {item.date}
                                        </Text>
                                        <Ionicons
                                            name="cube-outline"
                                            size={15}
                                            color="#9ca3af"
                                            style={{ marginLeft: 12 }}
                                        />
                                        <Text style={styles.meta}>
                                            {item.items_count ?? 0} article(s)
                                        </Text>
                                    </View>
                                    <View style={styles.cardFoot}>
                                        <Text style={styles.totalLabel}>
                                            Total
                                        </Text>
                                        <Text style={styles.total}>
                                            {formatPrice(item.total)}
                                        </Text>
                                    </View>
                                </View>
                            ))}
                        </View>
                    )}

                    {/* Bloc "introuvable" */}
                    <View style={styles.notFound}>
                        <Text style={styles.notFoundTitle}>
                            Vous ne trouvez pas votre commande ?
                        </Text>
                        <TouchableOpacity
                            style={styles.notFoundBtn}
                            activeOpacity={0.7}
                        >
                            <Text style={styles.notFoundText}>
                                Trouvez votre commande par vous-même
                            </Text>
                            <Ionicons
                                name="chevron-forward"
                                size={18}
                                color={COLORS.textLight}
                            />
                        </TouchableOpacity>
                    </View>

                    {/* Recommandations */}
                    <ScrollView
                        horizontal
                        showsHorizontalScrollIndicator={false}
                        contentContainerStyle={styles.recoTabs}
                    >
                        {recoTabs.map((c) => {
                            const on = recoCat === c.id;
                            return (
                                <TouchableOpacity
                                    key={String(c.id)}
                                    onPress={() => loadRecos(c.id)}
                                >
                                    <Text
                                        style={[
                                            styles.recoTab,
                                            on && styles.recoTabOn,
                                        ]}
                                    >
                                        {c.name}
                                    </Text>
                                    {on && <View style={styles.recoUnderline} />}
                                </TouchableOpacity>
                            );
                        })}
                    </ScrollView>

                    <View style={styles.recoGrid}>
                        <View style={styles.recoColumn}>
                            {leftCol.map((p) => (
                                <RecoCard
                                    key={p.id}
                                    item={p}
                                    onPress={() => goDetail(p)}
                                    onAdd={() => addReco(p)}
                                />
                            ))}
                        </View>
                        <View style={styles.recoColumn}>
                            {rightCol.map((p) => (
                                <RecoCard
                                    key={p.id}
                                    item={p}
                                    onPress={() => goDetail(p)}
                                    onAdd={() => addReco(p)}
                                />
                            ))}
                        </View>
                    </View>
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
        gap: 8,
        paddingHorizontal: 12,
        paddingBottom: 10,
        backgroundColor: "#fff",
    },
    searchBar: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#f3f4f6",
        borderRadius: RADIUS.pill,
        paddingHorizontal: 14,
        height: 38,
    },
    searchInput: { flex: 1, fontSize: 13.5, color: COLORS.text, padding: 0 },
    headerIcon: { padding: 4 },
    statusTabsWrap: {
        backgroundColor: "#fff",
        borderBottomWidth: 1,
        borderBottomColor: "#f0f0f0",
    },
    statusTabs: { gap: 20, paddingHorizontal: 16, paddingBottom: 4 },
    statusTab: { alignItems: "center" },
    statusTabText: { fontSize: 14, color: "#6b7280", fontWeight: "600", paddingBottom: 6 },
    statusTabTextOn: { color: COLORS.text, fontWeight: "800" },
    statusUnderline: {
        height: 3,
        width: 22,
        borderRadius: 3,
        backgroundColor: COLORS.primaryDark,
    },
    emptyWrap: { alignItems: "center", paddingVertical: 50, gap: 12 },
    emptyText: { color: "#6b7280", fontSize: 15 },
    card: { backgroundColor: "#fff", borderRadius: 14, padding: 14, elevation: 1 },
    cardHead: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
    },
    numero: { fontWeight: "700", color: "#111827", flex: 1, marginRight: 8 },
    statusPill: { borderRadius: 20, paddingHorizontal: 10, paddingVertical: 4 },
    statusText: { fontSize: 12, fontWeight: "700" },
    cardRow: {
        flexDirection: "row",
        alignItems: "center",
        marginTop: 10,
        gap: 4,
    },
    meta: { color: "#6b7280", fontSize: 13 },
    cardFoot: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        marginTop: 12,
        borderTopWidth: 1,
        borderTopColor: "#f3f4f6",
        paddingTop: 10,
    },
    totalLabel: { color: "#6b7280" },
    total: { color: COLORS.accent, fontWeight: "900", fontSize: 18 },
    notFound: {
        backgroundColor: "#fff",
        marginTop: 10,
        paddingHorizontal: 16,
        paddingVertical: 18,
    },
    notFoundTitle: {
        textAlign: "center",
        fontWeight: "800",
        color: COLORS.text,
        fontSize: 15,
        marginBottom: 12,
    },
    notFoundBtn: {
        flexDirection: "row",
        alignItems: "center",
        borderWidth: 1,
        borderColor: COLORS.border,
        borderRadius: RADIUS.sm,
        paddingHorizontal: 14,
        paddingVertical: 13,
    },
    notFoundText: { flex: 1, color: COLORS.text, fontSize: 13.5 },
    recoTabs: { gap: 18, paddingHorizontal: 16, paddingTop: 16, paddingBottom: 6 },
    recoTab: { fontSize: 14, color: "#6b7280", fontWeight: "600", paddingBottom: 5 },
    recoTabOn: { color: COLORS.text, fontWeight: "800" },
    recoUnderline: {
        height: 3,
        width: 18,
        borderRadius: 3,
        backgroundColor: COLORS.primaryDark,
        alignSelf: "flex-start",
    },
    recoGrid: { flexDirection: "row", paddingHorizontal: 12, gap: 12, marginTop: 4 },
    recoColumn: { flex: 1, gap: 12 },
    recoCard: {
        backgroundColor: "#fff",
        borderRadius: 12,
        overflow: "hidden",
        elevation: 1,
    },
    recoImg: { width: "100%", backgroundColor: "#e5e7eb" },
    hot: {
        position: "absolute",
        bottom: 0,
        left: 0,
        backgroundColor: COLORS.accent,
        paddingHorizontal: 8,
        paddingVertical: 2,
        borderTopRightRadius: 8,
    },
    hotText: { color: "#fff", fontSize: 10, fontWeight: "900" },
    recoBody: { padding: 8 },
    recoName: { fontSize: 12.5, color: COLORS.text, lineHeight: 17 },
    sold: { fontSize: 11, color: "#6b7280", marginTop: 4 },
    recoPriceRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 5,
        marginTop: 6,
    },
    recoPrice: { fontSize: 15, fontWeight: "900", color: COLORS.accent },
    discount: { fontSize: 11, color: COLORS.accent, fontWeight: "700" },
    addBtn: {
        marginLeft: "auto",
        width: 30,
        height: 30,
        borderRadius: 15,
        backgroundColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
    },
    errorText: { color: "#b91c1c", textAlign: "center", marginBottom: 16 },
    retry: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 10,
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    retryText: { color: "#fff", fontWeight: "700" },
});
