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
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { useCart } from "../context/CartContext";
import { useAuth } from "../context/AuthContext";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const RECO_FILTERS = [
    { key: "all", label: "Tout", icon: null },
    { key: "deals", label: "Bons plans", icon: "flame-outline" },
    { key: "fav", label: "Favoris fréquents", icon: "cart-outline" },
];
const RECO_PARAMS = {
    all: {},
    deals: { on_sale: 1 },
    fav: { featured: 1 },
};

/** Carte de recommandation (hauteur variable + bouton ajout panier). */
function RecoCard({ item, onPress, onAdd }) {
    const [ar, setAr] = useState(0.85);
    const discount = item.sale_price
        ? Math.round((1 - item.sale_price / item.price) * 100)
        : 0;
    return (
        <TouchableOpacity style={styles.recoCard} activeOpacity={0.9} onPress={onPress}>
            <Image
                source={{ uri: item.image }}
                style={[styles.recoImg, { aspectRatio: ar }]}
                resizeMode="cover"
                onLoad={(e) => {
                    const s = e?.nativeEvent?.source;
                    if (s?.width && s?.height) setAr(s.width / s.height);
                }}
            />
            <View style={styles.recoBody}>
                <Text style={styles.recoName} numberOfLines={2}>
                    {item.name}
                </Text>
                {item.rating_count ? (
                    <Text style={styles.bestseller} numberOfLines={1}>
                        🔥 {item.rating_count}+ vendus
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

export default function CartScreen({ navigation }) {
    const { cart, loading, refresh, update, remove, add } = useCart();
    const { user, token } = useAuth();
    const insets = useSafeAreaInsets();

    const [recos, setRecos] = useState([]);
    const [recoFilter, setRecoFilter] = useState("all");
    const [recoLoading, setRecoLoading] = useState(false);

    const city =
        user?.ville || user?.city || user?.region || user?.pays || null;

    const loadRecos = useCallback(async (filter) => {
        setRecoLoading(true);
        try {
            const { data } = await api.get("/products", {
                params: { per_page: 12, ...(RECO_PARAMS[filter] || {}) },
            });
            setRecos(data.data ?? []);
        } catch (e) {
            setRecos([]);
        } finally {
            setRecoLoading(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            refresh();
            loadRecos(recoFilter);
        }, [refresh, loadRecos, recoFilter]),
    );

    const selectReco = (key) => {
        if (key === recoFilter) return;
        setRecoFilter(key);
        loadRecos(key);
    };

    const changeQty = async (item, delta) => {
        const q = item.quantity + delta;
        if (q < 1) return;
        try {
            await update(item.product_id, q);
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        }
    };

    const addReco = async (p) => {
        try {
            await add(p.id, 1);
        } catch (e) {
            Alert.alert("Erreur", apiError(e));
        }
    };

    const goDetail = (p) =>
        navigation.navigate("ProductDetail", { id: p.id, name: p.name });

    const isEmpty = cart.items.length === 0;
    const leftCol = recos.filter((_, i) => i % 2 === 0);
    const rightCol = recos.filter((_, i) => i % 2 === 1);

    const Header = (
        <View style={[styles.header, { paddingTop: insets.top + 10 }]}>
            <Text style={styles.headerTitle}>Panier</Text>
            <TouchableOpacity style={styles.location} activeOpacity={0.7}>
                <Ionicons name="location-outline" size={15} color={COLORS.textLight} />
                <Text style={styles.locationText}>
                    {city ? `Livrer à ${city}` : "Adresse de livraison"}
                </Text>
                <Ionicons name="chevron-forward" size={14} color={COLORS.textLight} />
            </TouchableOpacity>
        </View>
    );

    const Recommendations = (
        <View style={styles.recoSection}>
            <Text style={styles.recoTitle}>✦ Complétez votre panier ✦</Text>

            <View style={styles.chipsRow}>
                {RECO_FILTERS.map((f) => {
                    const on = recoFilter === f.key;
                    return (
                        <TouchableOpacity
                            key={f.key}
                            style={[styles.chip, on && styles.chipOn]}
                            onPress={() => selectReco(f.key)}
                            activeOpacity={0.8}
                        >
                            {f.icon && (
                                <Ionicons
                                    name={f.icon}
                                    size={14}
                                    color={on ? "#fff" : COLORS.text}
                                />
                            )}
                            <Text
                                style={[styles.chipText, on && styles.chipTextOn]}
                            >
                                {f.label}
                            </Text>
                        </TouchableOpacity>
                    );
                })}
            </View>

            {recoLoading ? (
                <ActivityIndicator
                    color={COLORS.primary}
                    style={{ marginTop: 24 }}
                />
            ) : (
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
            )}
        </View>
    );

    return (
        <View style={styles.container}>
            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={{ paddingBottom: isEmpty ? 24 : 160 }}
            >
                {Header}

                {isEmpty ? (
                    <>
                        <View style={styles.emptyCard}>
                            <Ionicons
                                name="cart-outline"
                                size={54}
                                color="#cbd5e1"
                            />
                            <View style={{ flex: 1 }}>
                                <Text style={styles.emptyTitle}>
                                    Votre panier est vide
                                </Text>
                                <Text style={styles.emptySub}>
                                    Parcourez nos articles et trouvez votre
                                    bonheur ✨
                                </Text>
                            </View>
                        </View>

                        <View style={styles.actionsRow}>
                            <TouchableOpacity
                                style={[styles.actionBtn, styles.actionOutline]}
                                onPress={() => navigation.navigate("Catégories")}
                                activeOpacity={0.85}
                            >
                                <Text style={styles.actionOutlineText}>
                                    Achetez par catégorie
                                </Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                style={[styles.actionBtn, styles.actionDark]}
                                onPress={() => navigation.navigate("Accueil")}
                                activeOpacity={0.85}
                            >
                                <Text style={styles.actionDarkText}>
                                    {token ? "Continuer mes achats" : "Me connecter"}
                                </Text>
                            </TouchableOpacity>
                        </View>

                        {Recommendations}
                    </>
                ) : (
                    <View style={{ padding: 12, gap: 12 }}>
                        {cart.items.map((item, idx) => (
                            <View
                                key={String(item.id ?? item.product_id ?? idx)}
                                style={styles.row}
                            >
                                <Image
                                    source={{ uri: item.image }}
                                    style={styles.image}
                                />
                                <View style={{ flex: 1 }}>
                                    <Text style={styles.name} numberOfLines={2}>
                                        {item.name}
                                    </Text>
                                    <Text style={styles.price}>
                                        {formatPrice(item.unit_price)}
                                    </Text>
                                    <View style={styles.qtyRow}>
                                        <TouchableOpacity
                                            style={styles.qtyBtn}
                                            onPress={() => changeQty(item, -1)}
                                        >
                                            <Text style={styles.qtySign}>−</Text>
                                        </TouchableOpacity>
                                        <Text style={styles.qty}>
                                            {item.quantity}
                                        </Text>
                                        <TouchableOpacity
                                            style={styles.qtyBtn}
                                            onPress={() => changeQty(item, 1)}
                                        >
                                            <Text style={styles.qtySign}>+</Text>
                                        </TouchableOpacity>
                                        <TouchableOpacity
                                            style={styles.remove}
                                            onPress={() =>
                                                remove(item.product_id)
                                            }
                                        >
                                            <Text style={styles.removeText}>
                                                Retirer
                                            </Text>
                                        </TouchableOpacity>
                                    </View>
                                </View>
                                <Text style={styles.lineTotal}>
                                    {formatPrice(item.line_total)}
                                </Text>
                            </View>
                        ))}
                    </View>
                )}
            </ScrollView>

            {!isEmpty && (
                <View
                    style={[
                        styles.footer,
                        { paddingBottom: insets.bottom + 12 },
                    ]}
                >
                    <View style={styles.totalRow}>
                        <Text style={styles.totalLabel}>
                            Total ({cart.count} articles)
                        </Text>
                        <Text style={styles.totalValue}>
                            {formatPrice(cart.total)}
                        </Text>
                    </View>
                    <TouchableOpacity
                        style={styles.checkout}
                        onPress={() =>
                            Alert.alert(
                                "Commande",
                                "Le paiement sera ajouté prochainement.",
                            )
                        }
                    >
                        <Text style={styles.checkoutText}>
                            Passer la commande
                        </Text>
                    </TouchableOpacity>
                </View>
            )}

            {loading && isEmpty && (
                <View style={styles.loaderOverlay} pointerEvents="none">
                    <ActivityIndicator color={COLORS.primary} />
                </View>
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.bg },
    header: { backgroundColor: "#fff", paddingHorizontal: 16, paddingBottom: 12 },
    headerTitle: { fontSize: 22, fontWeight: "900", color: COLORS.text },
    location: { flexDirection: "row", alignItems: "center", gap: 3, marginTop: 4 },
    locationText: { color: COLORS.textLight, fontSize: 13 },

    emptyCard: {
        flexDirection: "row",
        alignItems: "center",
        gap: 14,
        backgroundColor: "#fff",
        paddingHorizontal: 16,
        paddingVertical: 18,
    },
    emptyTitle: { fontSize: 16, fontWeight: "800", color: COLORS.text },
    emptySub: { color: COLORS.textLight, fontSize: 13, marginTop: 3 },

    actionsRow: {
        flexDirection: "row",
        gap: 12,
        paddingHorizontal: 16,
        paddingBottom: 18,
        backgroundColor: "#fff",
    },
    actionBtn: {
        flex: 1,
        height: 46,
        borderRadius: RADIUS.sm,
        alignItems: "center",
        justifyContent: "center",
    },
    actionOutline: { borderWidth: 1.5, borderColor: COLORS.text },
    actionOutlineText: { color: COLORS.text, fontWeight: "700", fontSize: 13.5 },
    actionDark: { backgroundColor: "#1f2937" },
    actionDarkText: { color: "#fff", fontWeight: "700", fontSize: 13.5 },

    recoSection: { paddingTop: 18 },
    recoTitle: {
        textAlign: "center",
        fontSize: 15,
        fontWeight: "800",
        color: COLORS.text,
        marginBottom: 12,
    },
    chipsRow: {
        flexDirection: "row",
        justifyContent: "center",
        gap: 10,
        paddingHorizontal: 12,
        marginBottom: 14,
    },
    chip: {
        flexDirection: "row",
        alignItems: "center",
        gap: 5,
        paddingHorizontal: 14,
        paddingVertical: 8,
        borderRadius: RADIUS.pill,
        backgroundColor: "#fff",
        borderWidth: 1,
        borderColor: COLORS.border,
    },
    chipOn: { backgroundColor: "#1f2937", borderColor: "#1f2937" },
    chipText: { color: COLORS.text, fontWeight: "600", fontSize: 12.5 },
    chipTextOn: { color: "#fff" },

    recoGrid: { flexDirection: "row", paddingHorizontal: 12, gap: 12 },
    recoColumn: { flex: 1, gap: 12 },
    recoCard: {
        backgroundColor: "#fff",
        borderRadius: 12,
        overflow: "hidden",
        elevation: 1,
    },
    recoImg: { width: "100%", backgroundColor: "#e5e7eb" },
    recoBody: { padding: 8 },
    recoName: { fontSize: 12.5, color: COLORS.text, lineHeight: 17 },
    bestseller: { fontSize: 11, color: COLORS.accent, fontWeight: "700", marginTop: 4 },
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

    // Articles du panier (état non vide)
    row: {
        flexDirection: "row",
        backgroundColor: "#fff",
        borderRadius: 14,
        padding: 10,
        gap: 10,
        elevation: 1,
    },
    image: { width: 70, height: 70, borderRadius: 10, backgroundColor: "#e5e7eb" },
    name: { fontWeight: "600", color: "#111827" },
    price: { color: "#6b7280", marginTop: 2, fontSize: 12 },
    qtyRow: { flexDirection: "row", alignItems: "center", marginTop: 8, gap: 8 },
    qtyBtn: {
        width: 30,
        height: 30,
        borderRadius: 8,
        backgroundColor: "#eef2ff",
        justifyContent: "center",
        alignItems: "center",
    },
    qtySign: { fontSize: 18, color: COLORS.primaryDark, fontWeight: "800" },
    qty: { minWidth: 24, textAlign: "center", fontWeight: "700" },
    remove: { marginLeft: 8 },
    removeText: { color: "#dc2626", fontSize: 12, fontWeight: "600" },
    lineTotal: { fontWeight: "800", color: "#111827", alignSelf: "center" },
    footer: {
        position: "absolute",
        bottom: 0,
        left: 0,
        right: 0,
        padding: 16,
        backgroundColor: "#fff",
        borderTopWidth: 1,
        borderTopColor: "#eee",
    },
    totalRow: {
        flexDirection: "row",
        justifyContent: "space-between",
        marginBottom: 12,
    },
    totalLabel: { color: "#6b7280", fontSize: 15 },
    totalValue: { fontWeight: "900", fontSize: 20, color: "#111827" },
    checkout: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 16,
        alignItems: "center",
    },
    checkoutText: { color: "#fff", fontWeight: "800", fontSize: 16 },
    loaderOverlay: { position: "absolute", top: 120, alignSelf: "center" },
});
