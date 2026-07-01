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
import { useCart } from "../context/CartContext";
import { useAuth } from "../context/AuthContext";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";
import ScreenHeroHeader from "../components/ScreenHeroHeader";

const CART_HERO_IMAGE =
    "https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1080&q=80";

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
    const [defaultAddress, setDefaultAddress] = useState(null);
    const [coupon, setCoupon] = useState(null);
    const [couponInput, setCouponInput] = useState("");
    const [placing, setPlacing] = useState(false);

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

    const loadAddress = useCallback(async () => {
        try {
            const { data } = await api.get("/addresses");
            const list = data.data ?? [];
            setDefaultAddress(list.find((a) => a.is_default) || list[0] || null);
        } catch (e) {
            /* ignore */
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            refresh();
            loadRecos(recoFilter);
            loadAddress();
        }, [refresh, loadRecos, recoFilter, loadAddress]),
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

    const applyCoupon = async () => {
        const code = couponInput.trim();
        if (!code) return;
        try {
            const { data } = await api.post("/coupons/apply", {
                code,
                subtotal: cart.total,
            });
            setCoupon({ code: data.code, discount: data.discount });
            Alert.alert("Coupon appliqué", `Réduction : ${formatPrice(data.discount)}`);
        } catch (e) {
            setCoupon(null);
            Alert.alert("Code invalide", apiError(e));
        }
    };

    const removeCoupon = () => {
        setCoupon(null);
        setCouponInput("");
    };

    const checkout = async () => {
        if (placing) return;
        setPlacing(true);
        try {
            const { data } = await api.post("/orders", {
                address_id: defaultAddress?.id,
                coupon_code: coupon?.code,
            });
            const order = data.data ?? data;
            setCoupon(null);
            setCouponInput("");
            await refresh();
            Alert.alert("Commande passée", "Votre commande a bien été créée.", [
                {
                    text: "Voir la commande",
                    onPress: () =>
                        navigation.navigate("OrderDetail", { id: order.id }),
                },
                { text: "OK" },
            ]);
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setPlacing(false);
        }
    };

    const isEmpty = cart.items.length === 0;
    const leftCol = recos.filter((_, i) => i % 2 === 0);
    const rightCol = recos.filter((_, i) => i % 2 === 1);

    const Header = (
        <ScreenHeroHeader
            image={CART_HERO_IMAGE}
            height={130 + insets.top}
            overlayOpacity="medium"
            style={{ paddingTop: insets.top }}
        >
            <View style={styles.headerInner}>
                <View>
                    <Text style={styles.headerTitle}>Panier</Text>
                    <TouchableOpacity style={styles.location} activeOpacity={0.7}>
                        <Ionicons name="location-outline" size={15} color="rgba(255,255,255,0.85)" />
                        <Text style={styles.locationText}>
                            {city ? `Livrer à ${city}` : "Adresse de livraison"}
                        </Text>
                        <Ionicons name="chevron-forward" size={14} color="rgba(255,255,255,0.85)" />
                    </TouchableOpacity>
                </View>
                <View style={styles.cartBadgeCircle}>
                    <Ionicons name="bag" size={22} color="#fff" />
                    {cart.count > 0 && (
                        <View style={styles.cartBadgeDot}>
                            <Text style={styles.cartBadgeNum}>{cart.count}</Text>
                        </View>
                    )}
                </View>
            </View>
        </ScreenHeroHeader>
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

                        {/* Adresse de livraison */}
                        <TouchableOpacity
                            style={styles.addrCard}
                            activeOpacity={0.8}
                            onPress={() => navigation.navigate("Addresses")}
                        >
                            <Ionicons
                                name="location-outline"
                                size={20}
                                color={COLORS.primaryDark}
                            />
                            <View style={{ flex: 1 }}>
                                {defaultAddress ? (
                                    <>
                                        <Text style={styles.addrName}>
                                            {defaultAddress.nom} ·{" "}
                                            {defaultAddress.telephone}
                                        </Text>
                                        <Text
                                            style={styles.addrLine}
                                            numberOfLines={1}
                                        >
                                            {[
                                                defaultAddress.adresse,
                                                defaultAddress.ville,
                                                defaultAddress.pays,
                                            ]
                                                .filter(Boolean)
                                                .join(", ")}
                                        </Text>
                                    </>
                                ) : (
                                    <Text style={styles.addrName}>
                                        Ajouter une adresse de livraison
                                    </Text>
                                )}
                            </View>
                            <Ionicons
                                name="chevron-forward"
                                size={18}
                                color={COLORS.textLight}
                            />
                        </TouchableOpacity>

                        {/* Code promo */}
                        <View style={styles.couponCard}>
                            <Ionicons
                                name="pricetag-outline"
                                size={20}
                                color={COLORS.primaryDark}
                            />
                            {coupon ? (
                                <>
                                    <Text style={styles.couponApplied}>
                                        {coupon.code} · −
                                        {formatPrice(coupon.discount)}
                                    </Text>
                                    <TouchableOpacity onPress={removeCoupon}>
                                        <Text style={styles.couponRemove}>
                                            Retirer
                                        </Text>
                                    </TouchableOpacity>
                                </>
                            ) : (
                                <>
                                    <TextInput
                                        style={styles.couponInput}
                                        placeholder="Code promo"
                                        placeholderTextColor="#9ca3af"
                                        autoCapitalize="characters"
                                        value={couponInput}
                                        onChangeText={setCouponInput}
                                    />
                                    <TouchableOpacity
                                        style={styles.couponBtn}
                                        onPress={applyCoupon}
                                    >
                                        <Text style={styles.couponBtnText}>
                                            Appliquer
                                        </Text>
                                    </TouchableOpacity>
                                </>
                            )}
                        </View>
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
                    <View style={styles.sumLine}>
                        <Text style={styles.sumLabel}>Sous-total</Text>
                        <Text style={styles.sumVal}>
                            {formatPrice(cart.total)}
                        </Text>
                    </View>
                    {coupon ? (
                        <View style={styles.sumLine}>
                            <Text style={styles.sumLabel}>Réduction</Text>
                            <Text style={[styles.sumVal, { color: COLORS.accent }]}>
                                −{formatPrice(coupon.discount)}
                            </Text>
                        </View>
                    ) : null}
                    <View style={styles.totalRow}>
                        <Text style={styles.totalLabel}>
                            Total ({cart.count})
                        </Text>
                        <Text style={styles.totalValue}>
                            {formatPrice(
                                Math.max(0, cart.total - (coupon?.discount || 0)),
                            )}
                        </Text>
                    </View>
                    <TouchableOpacity
                        style={styles.checkout}
                        onPress={checkout}
                        disabled={placing}
                        activeOpacity={0.9}
                    >
                        <Text style={styles.checkoutText}>
                            {placing ? "Traitement..." : "Passer la commande"}
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
    headerInner: {
        flexDirection: "row",
        alignItems: "flex-end",
        justifyContent: "space-between",
    },
    headerTitle: { fontSize: 22, fontWeight: "900", color: "#fff" },
    location: { flexDirection: "row", alignItems: "center", gap: 3, marginTop: 4 },
    locationText: { color: "rgba(255,255,255,0.85)", fontSize: 13 },
    cartBadgeCircle: {
        width: 44,
        height: 44,
        borderRadius: 22,
        backgroundColor: "rgba(255,255,255,0.2)",
        alignItems: "center",
        justifyContent: "center",
    },
    cartBadgeDot: {
        position: "absolute",
        top: -2,
        right: -2,
        minWidth: 18,
        height: 18,
        borderRadius: 9,
        backgroundColor: COLORS.badge,
        alignItems: "center",
        justifyContent: "center",
        paddingHorizontal: 4,
    },
    cartBadgeNum: { color: "#fff", fontSize: 10, fontWeight: "800" },

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
    addrCard: {
        flexDirection: "row",
        alignItems: "center",
        gap: 10,
        backgroundColor: "#fff",
        borderRadius: 14,
        padding: 14,
        elevation: 1,
    },
    addrName: { fontWeight: "700", color: COLORS.text, fontSize: 13.5 },
    addrLine: { color: COLORS.textLight, fontSize: 12.5, marginTop: 3 },
    couponCard: {
        flexDirection: "row",
        alignItems: "center",
        gap: 10,
        backgroundColor: "#fff",
        borderRadius: 14,
        padding: 12,
        elevation: 1,
    },
    couponInput: {
        flex: 1,
        fontSize: 14,
        color: COLORS.text,
        paddingVertical: 6,
    },
    couponBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 16,
        paddingVertical: 8,
    },
    couponBtnText: { color: "#fff", fontWeight: "700", fontSize: 13 },
    couponApplied: { flex: 1, color: COLORS.text, fontWeight: "700", fontSize: 13.5 },
    couponRemove: { color: "#dc2626", fontWeight: "700", fontSize: 13 },
    sumLine: {
        flexDirection: "row",
        justifyContent: "space-between",
        marginBottom: 6,
    },
    sumLabel: { color: "#6b7280", fontSize: 13 },
    sumVal: { color: COLORS.text, fontSize: 13, fontWeight: "600" },
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
