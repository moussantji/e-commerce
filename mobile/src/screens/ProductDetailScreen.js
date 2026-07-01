import React, { useCallback, useEffect, useRef, useState } from "react";
import {
    View,
    Text,
    Image,
    ScrollView,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
    Dimensions,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { useCart } from "../context/CartContext";
import { useAuth } from "../context/AuthContext";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const { width: SCREEN_WIDTH } = Dimensions.get("window");

// Filtres de la section "Vous aimerez aussi" (façon SHEIN)
const RECO_FILTERS = [
    { key: "for_you", label: "Pour vous" },
    { key: "popular", label: "Populaires" },
    { key: "new", label: "Nouveautés" },
    { key: "deals", label: "Promos" },
];

const RECO_PARAMS = {
    for_you: {},
    popular: { sort: "popular" },
    new: { sort: "latest" },
    deals: { on_sale: 1 },
};

// Étoiles (pleines / demie / vides) comme sur le site
function Stars({ value = 0, size = 14 }) {
    const items = [];
    for (let i = 1; i <= 5; i++) {
        const filled = value >= i;
        const half = !filled && value >= i - 0.5;
        items.push(
            <Ionicons
                key={i}
                name={filled ? "star" : half ? "star-half" : "star-outline"}
                size={size}
                color="#f59e0b"
            />,
        );
    }
    return <View style={{ flexDirection: "row", gap: 1 }}>{items}</View>;
}

// Carte produit de la grille "Vous aimerez aussi"
function RecoCard({ item, navigation }) {
    const hasDiscount = item.sale_price && item.sale_price < item.price;
    const discount = hasDiscount
        ? Math.round(((item.price - item.sale_price) / item.price) * 100)
        : 0;
    return (
        <TouchableOpacity
            style={styles.recoCard}
            activeOpacity={0.9}
            onPress={() => navigation.push("ProductDetail", { id: item.id, name: item.name })}
        >
            <View>
                <Image source={{ uri: item.image }} style={styles.recoImg} />
                {hasDiscount ? (
                    <View style={styles.recoBadge}>
                        <Text style={styles.recoBadgeText}>-{discount}%</Text>
                    </View>
                ) : null}
            </View>
            <View style={styles.recoBody}>
                <Text style={styles.recoName} numberOfLines={2}>
                    {item.name}
                </Text>
                <View style={styles.recoPriceRow}>
                    <Text style={styles.recoPrice}>{formatPrice(item.sale_price ?? item.price)}</Text>
                    {hasDiscount ? (
                        <Text style={styles.recoOldPrice}>{formatPrice(item.price)}</Text>
                    ) : null}
                </View>
                {item.rating_count > 0 ? (
                    <Text style={styles.recoRating}>⭐ {item.rating_avg ?? 0} ({item.rating_count})</Text>
                ) : null}
            </View>
        </TouchableOpacity>
    );
}

export default function ProductDetailScreen({ route, navigation }) {
    const { id } = route.params;
    const insets = useSafeAreaInsets();
    const { add } = useCart();
    const { token } = useAuth();

    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [adding, setAdding] = useState(false);
    const [qty, setQty] = useState(1);
    const [tab, setTab] = useState("description");
    const [activeImg, setActiveImg] = useState(0);
    const [isFav, setIsFav] = useState(false);
    const [favLoading, setFavLoading] = useState(false);
    const galleryRef = useRef(null);

    // Section "Vous aimerez aussi" avec filtres + grille + pagination
    const [recoFilter, setRecoFilter] = useState("for_you");
    const [reco, setReco] = useState([]);
    const [recoLoading, setRecoLoading] = useState(false);
    const [recoPage, setRecoPage] = useState(1);
    const [recoHasMore, setRecoHasMore] = useState(true);
    const recoLoadingRef = useRef(false);

    useEffect(() => {
        (async () => {
            setLoading(true);
            try {
                const { data } = await api.get(`/products/${id}`);
                setProduct(data.data);
            } catch (e) {
                Alert.alert("Erreur", apiError(e));
            } finally {
                setLoading(false);
            }
        })();
    }, [id]);

    // État favori (si connecté)
    useEffect(() => {
        if (!token) return;
        (async () => {
            try {
                const { data } = await api.get(`/wishlist/${id}`);
                setIsFav(!!data.favorited);
            } catch (e) {
                /* ignore */
            }
        })();
    }, [id, token]);

    const toggleFav = async () => {
        if (!token) {
            Alert.alert("Connexion requise", "Connectez-vous pour gérer vos favoris.");
            return;
        }
        if (favLoading) return;
        setFavLoading(true);
        // Optimiste
        setIsFav((v) => !v);
        try {
            const { data } = await api.post(`/wishlist/${id}`);
            setIsFav(!!data.favorited);
        } catch (e) {
            setIsFav((v) => !v); // rollback
            Alert.alert("Impossible", apiError(e));
        } finally {
            setFavLoading(false);
        }
    };

    // --- Section "Vous aimerez aussi" ---
    const fetchReco = useCallback(
        async (filter, page, categoryId) => {
            const params = { per_page: 10, page, ...(RECO_PARAMS[filter] || {}) };
            // "Pour vous" : privilégie la même catégorie
            if (filter === "for_you" && categoryId) {
                params.category_id = categoryId;
            }
            const { data } = await api.get("/products", { params });
            return data;
        },
        [],
    );

    // (Re)charge la 1ère page quand le produit ou le filtre change
    useEffect(() => {
        if (!product) return;
        let cancelled = false;
        (async () => {
            setRecoLoading(true);
            try {
                const data = await fetchReco(recoFilter, 1, product.category?.id);
                if (cancelled) return;
                const list = (data.data ?? []).filter((p) => p.id !== product.id);
                setReco(list);
                setRecoPage(1);
                setRecoHasMore((data.meta?.current_page ?? 1) < (data.meta?.last_page ?? 1));
            } catch (e) {
                if (!cancelled) setReco([]);
            } finally {
                if (!cancelled) setRecoLoading(false);
            }
        })();
        return () => {
            cancelled = true;
        };
    }, [product?.id, recoFilter, fetchReco]);

    const loadMoreReco = async () => {
        if (recoLoadingRef.current || !recoHasMore || recoLoading || !product) return;
        recoLoadingRef.current = true;
        try {
            const next = recoPage + 1;
            const data = await fetchReco(recoFilter, next, product.category?.id);
            const list = (data.data ?? []).filter((p) => p.id !== product.id);
            setReco((prev) => {
                const seen = new Set(prev.map((x) => x.id));
                return [...prev, ...list.filter((x) => !seen.has(x.id))];
            });
            setRecoPage(next);
            setRecoHasMore((data.meta?.current_page ?? next) < (data.meta?.last_page ?? next));
        } catch (e) {
            /* ignore */
        } finally {
            recoLoadingRef.current = false;
        }
    };

    const onMainScroll = (e) => {
        const { contentOffset, contentSize, layoutMeasurement } = e.nativeEvent;
        if (contentOffset.y + layoutMeasurement.height >= contentSize.height - 500) {
            loadMoreReco();
        }
    };

    const addToCart = async () => {
        if (!token) {
            Alert.alert("Connexion requise", "Connectez-vous pour ajouter au panier.");
            return;
        }
        setAdding(true);
        try {
            await add(product.id, qty);
            // Redirige directement vers le panier (onglet dans Tabs)
            navigation.navigate("Tabs", { screen: "Panier" });
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setAdding(false);
        }
    };

    const submitReview = () => {
        if (!token) {
            Alert.alert("Connexion requise", "Connectez-vous pour laisser un avis.");
            return;
        }
        navigation.navigate("WriteReview", {
            productId: id,
            productName: product?.name,
        });
    };

    // Réception d'un nouvel avis renvoyé par l'écran WriteReview
    useEffect(() => {
        const newReview = route.params?.newReview;
        if (!newReview) return;
        setProduct((prev) => {
            if (!prev) return prev;
            if ((prev.reviews ?? []).some((r) => r.id === newReview.id)) return prev;
            const reviews = [newReview, ...(prev.reviews ?? [])];
            const count = (prev.rating_count ?? 0) + 1;
            const avg =
                ((prev.rating_avg ?? 0) * (prev.rating_count ?? 0) + (newReview.rating ?? 0)) / count;
            return {
                ...prev,
                reviews,
                rating_count: count,
                rating_avg: Math.round(avg * 10) / 10,
            };
        });
        setTab("reviews");
        navigation.setParams({ newReview: undefined });
    }, [route.params?.newReview]);

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }
    if (!product) {
        return (
            <View style={styles.center}>
                <Text>Produit introuvable.</Text>
            </View>
        );
    }

    const images = product.images?.length ? product.images : [product.image];
    const specs = product.specifications ?? [];
    const reviews = product.reviews ?? [];
    const colors = specs.filter((s) =>
        /couleur|color/i.test(s.type || s.name || ""),
    );

    const tabs = [
        { key: "description", label: "Description" },
        { key: "specs", label: "Spécifications" },
        { key: "reviews", label: `Avis (${product.rating_count ?? 0})` },
    ];

    return (
        <View style={{ flex: 1, backgroundColor: "#fff" }}>
            {/* Barre supérieure flottante */}
            <View style={[styles.topBar, { paddingTop: insets.top + 6 }]}>
                <TouchableOpacity style={styles.circleBtn} onPress={() => navigation.goBack()}>
                    <Ionicons name="chevron-back" size={22} color="#111" />
                </TouchableOpacity>
                <View style={{ flexDirection: "row", gap: 10 }}>
                    <TouchableOpacity style={styles.circleBtn} onPress={() => navigation.navigate("Tabs", { screen: "Panier" })}>
                        <Ionicons name="bag-outline" size={20} color="#111" />
                    </TouchableOpacity>
                    <TouchableOpacity style={styles.circleBtn} onPress={toggleFav}>
                        <Ionicons
                            name={isFav ? "heart" : "heart-outline"}
                            size={20}
                            color={isFav ? COLORS.badge : "#111"}
                        />
                    </TouchableOpacity>
                </View>
            </View>

            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={{ paddingBottom: 100 }}
                onScroll={onMainScroll}
                scrollEventThrottle={16}
            >
                {/* Galerie d'images */}
                <View>
                    <ScrollView
                        ref={galleryRef}
                        horizontal
                        pagingEnabled
                        showsHorizontalScrollIndicator={false}
                        onMomentumScrollEnd={(e) =>
                            setActiveImg(Math.round(e.nativeEvent.contentOffset.x / SCREEN_WIDTH))
                        }
                    >
                        {images.map((uri, i) => (
                            <Image key={i} source={{ uri }} style={styles.mainImage} resizeMode="cover" />
                        ))}
                    </ScrollView>
                    {images.length > 1 && (
                        <View style={styles.dots}>
                            {images.map((_, i) => (
                                <View key={i} style={[styles.dot, i === activeImg && styles.dotActive]} />
                            ))}
                        </View>
                    )}
                    {product.discount_percent > 0 && (
                        <View style={styles.discountFlag}>
                            <Text style={styles.discountFlagText}>-{product.discount_percent}%</Text>
                        </View>
                    )}
                </View>

                {/* Miniatures */}
                {images.length > 1 && (
                    <ScrollView
                        horizontal
                        showsHorizontalScrollIndicator={false}
                        contentContainerStyle={styles.thumbsRow}
                    >
                        {images.map((uri, i) => (
                            <TouchableOpacity
                                key={i}
                                onPress={() => {
                                    setActiveImg(i);
                                    galleryRef.current?.scrollTo({ x: i * SCREEN_WIDTH, animated: true });
                                }}
                            >
                                <Image
                                    source={{ uri }}
                                    style={[styles.thumb, i === activeImg && styles.thumbActive]}
                                />
                            </TouchableOpacity>
                        ))}
                    </ScrollView>
                )}

                <View style={styles.body}>
                    {/* Note */}
                    <View style={styles.ratingRow}>
                        <Stars value={product.rating_avg ?? 0} size={15} />
                        <Text style={styles.ratingText}>
                            {Number(product.rating_avg ?? 0).toFixed(1)} / 5 · {product.rating_count ?? 0} avis
                        </Text>
                    </View>

                    {/* Nom */}
                    <Text style={styles.name}>{product.name}</Text>

                    {/* Marque + SKU */}
                    <View style={styles.metaRow}>
                        {product.brand?.name ? (
                            <View style={styles.brandBadge}>
                                <Text style={styles.brandText}>{product.brand.name}</Text>
                            </View>
                        ) : null}
                        {product.sku ? <Text style={styles.sku}>SKU : {product.sku}</Text> : null}
                    </View>

                    {/* Prix */}
                    <View style={styles.priceRow}>
                        <Text style={styles.price}>{formatPrice(product.sale_price ?? product.price)}</Text>
                        {product.sale_price ? (
                            <>
                                <Text style={styles.oldPrice}>{formatPrice(product.price)}</Text>
                                <Text style={styles.discountText}>-{product.discount_percent}%</Text>
                            </>
                        ) : null}
                    </View>

                    {/* Stock */}
                    <Text style={[styles.stock, { color: product.in_stock ? "#16a34a" : "#dc2626" }]}>
                        {product.in_stock ? `En stock (${product.stock})` : "Rupture de stock"}
                    </Text>

                    {/* Couleurs */}
                    {colors.length > 0 && (
                        <View style={styles.colorSection}>
                            <Text style={styles.colorLabel}>
                                Couleur : <Text style={{ fontWeight: "400" }}>{colors.map((c) => c.value).join(", ")}</Text>
                            </Text>
                        </View>
                    )}

                    {/* Quantité */}
                    <View style={styles.qtySection}>
                        <Text style={styles.qtyLabel}>Quantité :</Text>
                        <View style={styles.qtyBox}>
                            <TouchableOpacity
                                style={styles.qtyBtn}
                                onPress={() => setQty((q) => Math.max(1, q - 1))}
                            >
                                <Ionicons name="remove" size={18} color={COLORS.primaryDark} />
                            </TouchableOpacity>
                            <Text style={styles.qtyValue}>{qty}</Text>
                            <TouchableOpacity
                                style={styles.qtyBtn}
                                onPress={() => setQty((q) => Math.min(product.stock || 99, q + 1))}
                            >
                                <Ionicons name="add" size={18} color={COLORS.primaryDark} />
                            </TouchableOpacity>
                        </View>
                    </View>

                    {/* Onglets */}
                    <View style={styles.tabBar}>
                        {tabs.map((t) => (
                            <TouchableOpacity
                                key={t.key}
                                style={[styles.tab, tab === t.key && styles.tabActive]}
                                onPress={() => setTab(t.key)}
                            >
                                <Text style={[styles.tabText, tab === t.key && styles.tabTextActive]}>
                                    {t.label}
                                </Text>
                            </TouchableOpacity>
                        ))}
                    </View>

                    {/* Contenu onglet */}
                    {tab === "description" && (
                        <Text style={styles.description}>
                            {product.description
                                ? String(product.description).replace(/<[^>]*>/g, "").trim()
                                : "Aucune description."}
                        </Text>
                    )}

                    {tab === "specs" && (
                        <View style={styles.specTable}>
                            {specs.length === 0 ? (
                                <Text style={styles.muted}>Aucune spécification disponible.</Text>
                            ) : (
                                specs.map((s, i) => (
                                    <View key={i} style={[styles.specRow, i % 2 === 0 && styles.specRowAlt]}>
                                        <Text style={styles.specName}>{s.name}</Text>
                                        <Text style={styles.specValue}>
                                            {s.value}
                                            {s.unite ? ` ${s.unite}` : ""}
                                        </Text>
                                    </View>
                                ))
                            )}
                        </View>
                    )}

                    {tab === "reviews" && (
                        <View>
                            {/* Résumé + bouton écrire un avis */}
                            <View style={styles.reviewsSummary}>
                                <View style={{ flexDirection: "row", alignItems: "center", gap: 8 }}>
                                    <Text style={styles.reviewsAvg}>
                                        {Number(product.rating_avg ?? 0).toFixed(1)}
                                        <Text style={styles.reviewsAvgMax}>/5</Text>
                                    </Text>
                                    <View>
                                        <Stars value={product.rating_avg ?? 0} size={14} />
                                        <Text style={styles.reviewsCount}>
                                            {product.rating_count ?? 0} note{(product.rating_count ?? 0) > 1 ? "s" : ""}
                                        </Text>
                                    </View>
                                </View>
                                <TouchableOpacity
                                    style={styles.writeBtn}
                                    onPress={submitReview}
                                >
                                    <Ionicons name="create-outline" size={16} color={COLORS.primaryDark} />
                                    <Text style={styles.writeBtnText}>Écrire un avis</Text>
                                </TouchableOpacity>
                            </View>

                            {reviews.length === 0 ? (
                                <Text style={styles.muted}>Aucun avis pour le moment.</Text>
                            ) : (
                                reviews.map((r) => (
                                    <View key={r.id} style={styles.review}>
                                        <View style={styles.reviewHead}>
                                            <Stars value={r.rating} size={13} />
                                            <Text style={styles.reviewAuthor}>par {r.author}</Text>
                                        </View>
                                        {!!r.date && <Text style={styles.reviewDate}>{r.date}</Text>}
                                        {!!r.comment && <Text style={styles.reviewComment}>{r.comment}</Text>}
                                        {r.images?.length > 0 && (
                                            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={{ marginTop: 6 }}>
                                                {r.images.map((uri, k) => (
                                                    <Image key={k} source={{ uri }} style={styles.reviewImg} />
                                                ))}
                                            </ScrollView>
                                        )}
                                        {r.response && (
                                            <View style={styles.reviewResponse}>
                                                <Ionicons name="arrow-undo" size={13} color={COLORS.primaryDark} />
                                                <View style={{ flex: 1 }}>
                                                    <Text style={styles.responseTitle}>Réponse du vendeur</Text>
                                                    <Text style={styles.responseMsg}>{r.response.message}</Text>
                                                </View>
                                            </View>
                                        )}
                                    </View>
                                ))
                            )}
                        </View>
                    )}

                    {/* Vous aimerez aussi — filtres + grille (façon SHEIN) */}
                    <View style={styles.similarSection}>
                        <Text style={styles.sectionTitle}>Vous aimerez aussi</Text>

                        {/* Filtres */}
                        <View style={styles.recoChips}>
                            {RECO_FILTERS.map((f) => {
                                const on = recoFilter === f.key;
                                return (
                                    <TouchableOpacity
                                        key={f.key}
                                        style={[styles.recoChip, on && styles.recoChipOn]}
                                        onPress={() => setRecoFilter(f.key)}
                                    >
                                        <Text style={[styles.recoChipText, on && styles.recoChipTextOn]}>
                                            {f.label}
                                        </Text>
                                    </TouchableOpacity>
                                );
                            })}
                        </View>

                        {/* Grille 2 colonnes */}
                        {recoLoading && reco.length === 0 ? (
                            <ActivityIndicator style={{ marginTop: 24 }} color={COLORS.primary} />
                        ) : reco.length === 0 ? (
                            <Text style={styles.muted}>Aucun produit à recommander.</Text>
                        ) : (
                            <View style={styles.recoGrid}>
                                <View style={styles.recoCol}>
                                    {reco.filter((_, i) => i % 2 === 0).map((p) => (
                                        <RecoCard key={p.id} item={p} navigation={navigation} />
                                    ))}
                                </View>
                                <View style={styles.recoCol}>
                                    {reco.filter((_, i) => i % 2 === 1).map((p) => (
                                        <RecoCard key={p.id} item={p} navigation={navigation} />
                                    ))}
                                </View>
                            </View>
                        )}

                        {recoHasMore && reco.length > 0 && (
                            <ActivityIndicator style={{ marginVertical: 16 }} color={COLORS.primary} />
                        )}
                    </View>
                </View>
            </ScrollView>

            {/* Barre d'action fixe */}
            <View style={[styles.footer, { paddingBottom: insets.bottom + 10 }]}>
                <TouchableOpacity style={styles.wishBtn} onPress={toggleFav}>
                    <Ionicons
                        name={isFav ? "heart" : "heart-outline"}
                        size={22}
                        color={isFav ? COLORS.badge : COLORS.primaryDark}
                    />
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.buttonWrap}
                    onPress={addToCart}
                    disabled={!product.in_stock || adding}
                    activeOpacity={0.85}
                >
                    <LinearGradient
                        colors={!product.in_stock || adding ? ["#cbd5e1", "#9ca3af"] : COLORS.gradient}
                        start={COLORS.gradientStart}
                        end={COLORS.gradientEnd}
                        style={styles.button}
                    >
                        <Ionicons name="cart" size={18} color="#fff" />
                        <Text style={styles.buttonText}>
                            {!product.in_stock ? "Indisponible" : adding ? "Ajout..." : "Ajouter au panier"}
                        </Text>
                    </LinearGradient>
                </TouchableOpacity>
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    center: { flex: 1, justifyContent: "center", alignItems: "center" },
    topBar: {
        position: "absolute",
        top: 0,
        left: 0,
        right: 0,
        zIndex: 20,
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingHorizontal: 12,
        paddingBottom: 6,
    },
    circleBtn: {
        width: 38,
        height: 38,
        borderRadius: 19,
        backgroundColor: "rgba(255,255,255,0.9)",
        alignItems: "center",
        justifyContent: "center",
    },
    mainImage: { width: SCREEN_WIDTH, height: SCREEN_WIDTH, backgroundColor: "#e5e7eb" },
    dots: {
        position: "absolute",
        bottom: 10,
        alignSelf: "center",
        flexDirection: "row",
        gap: 6,
    },
    dot: { width: 6, height: 6, borderRadius: 3, backgroundColor: "rgba(255,255,255,0.6)" },
    dotActive: { backgroundColor: "#fff", width: 18 },
    discountFlag: {
        position: "absolute",
        top: 90,
        left: 0,
        backgroundColor: COLORS.accent,
        paddingHorizontal: 10,
        paddingVertical: 4,
        borderTopRightRadius: 8,
        borderBottomRightRadius: 8,
    },
    discountFlagText: { color: "#fff", fontWeight: "800", fontSize: 13 },
    thumbsRow: { paddingHorizontal: 12, paddingVertical: 10, gap: 8 },
    thumb: {
        width: 54,
        height: 54,
        borderRadius: 8,
        backgroundColor: "#e5e7eb",
        borderWidth: 2,
        borderColor: "transparent",
    },
    thumbActive: { borderColor: COLORS.primaryDark },
    body: { paddingHorizontal: 16, paddingTop: 6 },
    ratingRow: { flexDirection: "row", alignItems: "center", gap: 8 },
    ratingText: { color: COLORS.primaryDark, fontWeight: "600", fontSize: 12.5 },
    name: { fontSize: 20, fontWeight: "800", color: "#111827", marginTop: 8, lineHeight: 26 },
    metaRow: { flexDirection: "row", alignItems: "center", gap: 10, marginTop: 8, flexWrap: "wrap" },
    brandBadge: {
        backgroundColor: "#16a34a",
        borderRadius: RADIUS.pill,
        paddingHorizontal: 10,
        paddingVertical: 3,
    },
    brandText: { color: "#fff", fontSize: 11, fontWeight: "700" },
    sku: { color: "#374151", fontSize: 12, fontWeight: "600" },
    priceRow: { flexDirection: "row", alignItems: "baseline", gap: 10, marginTop: 14, flexWrap: "wrap" },
    price: { fontSize: 26, fontWeight: "900", color: COLORS.accent },
    oldPrice: { fontSize: 15, color: "#9ca3af", textDecorationLine: "line-through" },
    discountText: { fontSize: 14, color: COLORS.accent, fontWeight: "800" },
    stock: { marginTop: 8, fontWeight: "700", fontSize: 13 },
    colorSection: { marginTop: 14 },
    colorLabel: { fontSize: 14, fontWeight: "700", color: COLORS.text },
    qtySection: { flexDirection: "row", alignItems: "center", gap: 16, marginTop: 16 },
    qtyLabel: { fontSize: 14, fontWeight: "700", color: COLORS.text },
    qtyBox: {
        flexDirection: "row",
        alignItems: "center",
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: RADIUS.pill,
        overflow: "hidden",
    },
    qtyBtn: {
        width: 38,
        height: 38,
        alignItems: "center",
        justifyContent: "center",
        backgroundColor: "#f3f4f6",
    },
    qtyValue: { minWidth: 40, textAlign: "center", fontWeight: "800", fontSize: 15, color: COLORS.text },
    tabBar: {
        flexDirection: "row",
        borderBottomWidth: 1,
        borderBottomColor: "#eee",
        marginTop: 22,
    },
    tab: { paddingVertical: 12, marginRight: 22, borderBottomWidth: 2, borderBottomColor: "transparent" },
    tabActive: { borderBottomColor: COLORS.primaryDark },
    tabText: { fontSize: 14, color: "#6b7280", fontWeight: "600" },
    tabTextActive: { color: COLORS.primaryDark, fontWeight: "800" },
    description: { marginTop: 14, color: "#374151", lineHeight: 21, fontSize: 13.5 },
    muted: { marginTop: 14, color: "#9ca3af", fontSize: 13 },
    specTable: { marginTop: 14, borderWidth: 1, borderColor: "#eee", borderRadius: RADIUS.sm, overflow: "hidden" },
    specRow: { flexDirection: "row", paddingVertical: 11, paddingHorizontal: 12 },
    specRowAlt: { backgroundColor: "#f9fafb" },
    specName: { flex: 0.42, fontWeight: "700", color: "#374151", fontSize: 12.5, textTransform: "uppercase" },
    specValue: { flex: 0.58, color: "#111827", fontSize: 13 },
    review: { paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: "#f3f4f6" },
    reviewHead: { flexDirection: "row", alignItems: "center", gap: 8 },
    reviewsSummary: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        backgroundColor: "#f9fafb",
        borderRadius: RADIUS.md,
        padding: 14,
        marginTop: 14,
    },
    reviewsAvg: { fontSize: 26, fontWeight: "900", color: COLORS.text },
    reviewsAvgMax: { fontSize: 13, color: "#9ca3af", fontWeight: "700" },
    reviewsCount: { fontSize: 12, color: COLORS.textLight, marginTop: 2 },
    writeBtn: {
        flexDirection: "row",
        alignItems: "center",
        gap: 5,
        borderWidth: 1.5,
        borderColor: COLORS.primaryDark,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 14,
        paddingVertical: 9,
    },
    writeBtnText: { color: COLORS.primaryDark, fontWeight: "700", fontSize: 13 },
    reviewSheet: {
        backgroundColor: "#fff",
        borderTopLeftRadius: 20,
        borderTopRightRadius: 20,
        paddingTop: 16,
        paddingHorizontal: 20,
    },
    reviewSheetHead: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginBottom: 16,
    },
    reviewSheetTitle: { fontSize: 17, fontWeight: "800", color: COLORS.text },
    starPicker: { flexDirection: "row", justifyContent: "center", gap: 8 },
    starHint: { textAlign: "center", color: COLORS.textLight, marginTop: 8, fontWeight: "600" },
    reviewInput: {
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: RADIUS.md,
        padding: 14,
        minHeight: 100,
        textAlignVertical: "top",
        fontSize: 14,
        color: COLORS.text,
        marginTop: 16,
    },
    submitBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 15,
        alignItems: "center",
        marginTop: 16,
    },
    submitText: { color: "#fff", fontWeight: "800", fontSize: 15 },
    reviewAuthor: { fontSize: 13, fontWeight: "700", color: COLORS.text },
    reviewDate: { fontSize: 11, color: "#9ca3af", marginTop: 3 },
    reviewComment: { fontSize: 13.5, color: "#374151", marginTop: 6, lineHeight: 19 },
    reviewImg: { width: 70, height: 70, borderRadius: 8, marginRight: 8, backgroundColor: "#e5e7eb" },
    reviewResponse: {
        flexDirection: "row",
        gap: 8,
        backgroundColor: COLORS.soft,
        borderRadius: RADIUS.sm,
        padding: 10,
        marginTop: 10,
    },
    responseTitle: { fontSize: 12.5, fontWeight: "800", color: COLORS.primaryDark },
    responseMsg: { fontSize: 12.5, color: "#374151", marginTop: 2 },
    similarSection: { marginTop: 26 },
    sectionTitle: { fontSize: 16, fontWeight: "800", color: "#111827", marginBottom: 12 },
    recoChips: { flexDirection: "row", flexWrap: "wrap", gap: 8, marginBottom: 14 },
    recoChip: {
        paddingHorizontal: 16,
        paddingVertical: 8,
        borderRadius: RADIUS.pill,
        backgroundColor: "#f3f4f6",
        borderWidth: 1,
        borderColor: "#eee",
    },
    recoChipOn: { backgroundColor: COLORS.primaryDark, borderColor: COLORS.primaryDark },
    recoChipText: { fontSize: 12.5, color: COLORS.text, fontWeight: "600" },
    recoChipTextOn: { color: "#fff" },
    recoGrid: { flexDirection: "row", gap: 10 },
    recoCol: { flex: 1, gap: 10 },
    recoCard: { backgroundColor: "#fff", borderRadius: 10, overflow: "hidden", elevation: 1 },
    recoImg: { width: "100%", aspectRatio: 0.85, backgroundColor: "#e5e7eb" },
    recoBadge: {
        position: "absolute",
        top: 6,
        left: 6,
        backgroundColor: "#ef4444",
        borderRadius: 4,
        paddingHorizontal: 6,
        paddingVertical: 2,
    },
    recoBadgeText: { color: "#fff", fontSize: 10, fontWeight: "800" },
    recoBody: { padding: 8 },
    recoName: { fontSize: 12.5, color: "#1f2937", lineHeight: 16, minHeight: 32 },
    recoPriceRow: { flexDirection: "row", alignItems: "center", gap: 5, marginTop: 4 },
    recoPrice: { fontSize: 15, fontWeight: "900", color: COLORS.accent },
    recoOldPrice: { fontSize: 11, color: "#9ca3af", textDecorationLine: "line-through" },
    recoRating: { fontSize: 11, color: "#6b7280", marginTop: 4 },
    footer: {
        flexDirection: "row",
        alignItems: "center",
        gap: 12,
        paddingHorizontal: 16,
        paddingTop: 10,
        borderTopWidth: 1,
        borderTopColor: "#f0f0f0",
        backgroundColor: "#fff",
    },
    wishBtn: {
        width: 50,
        height: 50,
        borderRadius: 14,
        borderWidth: 1.5,
        borderColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
    },
    buttonWrap: { flex: 1, borderRadius: 14, overflow: "hidden" },
    button: {
        flexDirection: "row",
        gap: 8,
        borderRadius: 14,
        paddingVertical: 15,
        alignItems: "center",
        justifyContent: "center",
    },
    buttonText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
