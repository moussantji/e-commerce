import React, { useCallback, useEffect, useState } from "react";
import {
    View,
    Text,
    FlatList,
    Image,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
    ActivityIndicator,
    RefreshControl,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const FILTERS = [
    { key: "for_you", label: "Pour vous", icon: null },
    { key: "new", label: "Nouveautés", icon: "sparkles-outline" },
    { key: "deals", label: "Promos", icon: "pricetag-outline" },
    { key: "best", label: "Top ventes", icon: "flame-outline" },
];

export default function HomeScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const [categories, setCategories] = useState([]);
    const [flash, setFlash] = useState([]);
    const [products, setProducts] = useState([]);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [activeCat, setActiveCat] = useState(null);
    const [activeFilter, setActiveFilter] = useState("for_you");

    const loadAll = useCallback(async () => {
        try {
            const [cats, deals, grid] = await Promise.all([
                api.get("/categories"),
                api.get("/products", { params: { featured: 1, per_page: 10 } }),
                api.get("/products", { params: { per_page: 10, page: 1 } }),
            ]);
            setCategories(cats.data.data ?? []);
            setFlash(deals.data.data ?? []);
            setProducts(grid.data.data ?? []);
            setPage(grid.data.meta?.current_page ?? 1);
            setLastPage(grid.data.meta?.last_page ?? 1);
        } catch (e) {
            // silencieux : l'UI affiche l'état vide
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    }, []);

    useEffect(() => {
        loadAll();
    }, [loadAll]);

    const loadMore = async () => {
        if (loading || page >= lastPage) return;
        try {
            const next = page + 1;
            const { data } = await api.get("/products", {
                params: { per_page: 10, page: next },
            });
            setProducts((p) => [...p, ...(data.data ?? [])]);
            setPage(data.meta?.current_page ?? next);
        } catch (e) {
            /* ignore */
        }
    };

    const goDetail = (item) =>
        navigation.navigate("ProductDetail", { id: item.id, name: item.name });

    const Header = (
        <View>
            {/* Bannière promo en dégradé */}
            <LinearGradient
                colors={COLORS.gradient}
                start={COLORS.gradientStart}
                end={COLORS.gradientEnd}
                style={styles.banner}
            >
                <View style={{ flex: 1 }}>
                    <Text style={styles.bannerTag}>#MégaSoldes</Text>
                    <Text style={styles.bannerTitle}>ÉCONOMISEZ GROS 🎉</Text>
                    <Text style={styles.bannerSub}>
                        Jusqu'à -50% sur une sélection
                    </Text>
                    <TouchableOpacity
                        style={styles.bannerBtn}
                        onPress={() =>
                            navigation.navigate("ProductList", {
                                title: "Promotions",
                                featured: 1,
                            })
                        }
                    >
                        <Text style={styles.bannerBtnText}>
                            ACHETEZ MAINTENANT
                        </Text>
                    </TouchableOpacity>
                </View>
                <Ionicons
                    name="pricetags"
                    size={62}
                    color="rgba(255,255,255,0.9)"
                />
            </LinearGradient>

            {/* Barre infos : livraison + vente flash */}
            <View style={styles.infoBar}>
                <View style={styles.infoItem}>
                    <Ionicons
                        name="car-outline"
                        size={18}
                        color={COLORS.primary}
                    />
                    <View>
                        <Text style={styles.infoTitle}>Livraison offerte</Text>
                        <Text style={styles.infoSub}>Dès 25 000 FCFA</Text>
                    </View>
                </View>
                <View style={styles.infoDivider} />
                <View style={styles.infoItem}>
                    <Ionicons name="flash" size={18} color={COLORS.accent} />
                    <View>
                        <Text style={styles.infoTitle}>Vente Flash</Text>
                        <Text style={styles.infoSub}>Voir plus</Text>
                    </View>
                </View>
            </View>

            {/* Catégories horizontales en cercles */}
            {categories.length > 0 && (
                <View style={styles.catCard}>
                    <ScrollView
                        horizontal
                        showsHorizontalScrollIndicator={false}
                        contentContainerStyle={{ paddingHorizontal: 4 }}
                    >
                        {categories.map((c) => (
                            <TouchableOpacity
                                key={c.id}
                                style={styles.catItem}
                                onPress={() =>
                                    navigation.navigate("ProductList", {
                                        categoryId: c.id,
                                        title: c.name,
                                    })
                                }
                            >
                                <View style={styles.catCircle}>
                                    {c.image ? (
                                        <Image
                                            source={{ uri: c.image }}
                                            style={styles.catImg}
                                        />
                                    ) : (
                                        <Ionicons
                                            name="cube-outline"
                                            size={24}
                                            color={COLORS.primaryDark}
                                        />
                                    )}
                                </View>
                                <Text style={styles.catLabel} numberOfLines={1}>
                                    {c.name}
                                </Text>
                            </TouchableOpacity>
                        ))}
                    </ScrollView>
                </View>
            )}

            {/* Offres flash */}
            {flash.length > 0 && (
                <View style={styles.section}>
                    <View style={styles.sectionHead}>
                        <Text style={styles.sectionTitle}>🔥 Offres flash</Text>
                        <TouchableOpacity
                            onPress={() =>
                                navigation.navigate("ProductList", {
                                    title: "Offres flash",
                                    featured: 1,
                                })
                            }
                        >
                            <Text style={styles.seeAll}>Tout voir ›</Text>
                        </TouchableOpacity>
                    </View>
                    <ScrollView
                        horizontal
                        showsHorizontalScrollIndicator={false}
                        contentContainerStyle={{
                            paddingHorizontal: 12,
                            gap: 10,
                        }}
                    >
                        {flash.map((p) => (
                            <TouchableOpacity
                                key={p.id}
                                style={styles.flashCard}
                                onPress={() => goDetail(p)}
                            >
                                <Image
                                    source={{ uri: p.image }}
                                    style={styles.flashImg}
                                />
                                {p.sale_price ? (
                                    <View style={styles.flashBadge}>
                                        <Text style={styles.flashBadgeText}>
                                            PROMO
                                        </Text>
                                    </View>
                                ) : null}
                                <Text style={styles.flashPrice}>
                                    {formatPrice(p.sale_price ?? p.price)}
                                </Text>
                                <Text
                                    style={styles.flashName}
                                    numberOfLines={1}
                                >
                                    {p.name}
                                </Text>
                            </TouchableOpacity>
                        ))}
                    </ScrollView>
                </View>
            )}

            {/* Filtres */}
            <ScrollView
                horizontal
                showsHorizontalScrollIndicator={false}
                contentContainerStyle={styles.filterRow}
            >
                {FILTERS.map((f) => {
                    const on = activeFilter === f.key;
                    return (
                        <TouchableOpacity
                            key={f.key}
                            style={[styles.chip, on && styles.chipOn]}
                            onPress={() => setActiveFilter(f.key)}
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
            </ScrollView>
        </View>
    );

    const renderProduct = ({ item }) => (
        <TouchableOpacity style={styles.card} onPress={() => goDetail(item)}>
            <View>
                <Image source={{ uri: item.image }} style={styles.image} />
                {item.sale_price ? (
                    <View style={styles.badge}>
                        <Text style={styles.badgeText}>Promo</Text>
                    </View>
                ) : null}
                <View style={styles.heart}>
                    <Ionicons
                        name="heart-outline"
                        size={15}
                        color={COLORS.primaryDark}
                    />
                </View>
            </View>
            <View style={styles.cardBody}>
                <Text style={styles.name} numberOfLines={2}>
                    {item.name}
                </Text>
                <View style={styles.priceRow}>
                    <Text style={styles.price}>
                        {formatPrice(item.sale_price ?? item.price)}
                    </Text>
                    {item.sale_price ? (
                        <Text style={styles.oldPrice}>
                            {formatPrice(item.price)}
                        </Text>
                    ) : null}
                </View>
                <Text style={styles.rating}>
                    ⭐ {item.rating_avg ?? 0} ({item.rating_count ?? 0})
                </Text>
            </View>
        </TouchableOpacity>
    );

    return (
        <View style={styles.container}>
            <HomeTopBar navigation={navigation} insets={insets} />
            {loading ? (
                <View style={styles.center}>
                    <ActivityIndicator size="large" color={COLORS.primary} />
                </View>
            ) : (
                <FlatList
                    data={products}
                    keyExtractor={(i) => String(i.id)}
                    renderItem={renderProduct}
                    numColumns={2}
                    columnWrapperStyle={{ gap: 12, paddingHorizontal: 12 }}
                    contentContainerStyle={{ gap: 12, paddingBottom: 16 }}
                    ListHeaderComponent={Header}
                    onEndReached={loadMore}
                    onEndReachedThreshold={0.4}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={() => {
                                setRefreshing(true);
                                loadAll();
                            }}
                            colors={[COLORS.primary]}
                            tintColor={COLORS.primary}
                        />
                    }
                />
            )}
        </View>
    );
}

/** En-tête en dégradé : notifications (place réservée) + recherche + panier. */
function HomeTopBar({ navigation, insets }) {
    return (
        <LinearGradient
            colors={COLORS.gradient}
            start={COLORS.gradientStart}
            end={COLORS.gradientEnd}
            style={[styles.topBar, { paddingTop: insets.top + 8 }]}
        >
            {/* Espace notifications : cloche + badge */}
            <TouchableOpacity
                style={styles.iconBtn}
                onPress={() => navigation.navigate("Notifications")}
            >
                <Ionicons
                    name="notifications-outline"
                    size={24}
                    color="#fff"
                />
                <View style={styles.notifBadge}>
                    <Text style={styles.notifBadgeText}>2</Text>
                </View>
            </TouchableOpacity>

            <TouchableOpacity
                style={styles.search}
                onPress={() =>
                    navigation.navigate("ProductList", {
                        title: "Recherche",
                        focusSearch: true,
                    })
                }
            >
                <Ionicons name="search" size={18} color="#9ca3af" />
                <Text style={styles.searchPlaceholder}>
                    Rechercher un produit...
                </Text>
                <Ionicons name="camera-outline" size={20} color="#9ca3af" />
            </TouchableOpacity>

            <TouchableOpacity
                style={styles.iconBtn}
                onPress={() => navigation.navigate("Panier")}
            >
                <Ionicons name="bag-outline" size={24} color="#fff" />
            </TouchableOpacity>
        </LinearGradient>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.bg },
    center: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        paddingTop: 60,
    },
    topBar: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        paddingHorizontal: 12,
        paddingBottom: 12,
    },
    iconBtn: {
        width: 38,
        height: 38,
        alignItems: "center",
        justifyContent: "center",
    },
    notifBadge: {
        position: "absolute",
        top: 2,
        right: 2,
        minWidth: 16,
        height: 16,
        paddingHorizontal: 3,
        borderRadius: 8,
        backgroundColor: COLORS.badge,
        alignItems: "center",
        justifyContent: "center",
        borderWidth: 1.5,
        borderColor: COLORS.primary,
    },
    notifBadgeText: { color: "#fff", fontSize: 9, fontWeight: "800" },
    search: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#fff",
        borderRadius: 22,
        paddingHorizontal: 14,
        paddingVertical: 9,
    },
    searchPlaceholder: { flex: 1, color: "#9ca3af", fontSize: 14 },
    banner: {
        flexDirection: "row",
        alignItems: "center",
        margin: 12,
        borderRadius: RADIUS.lg,
        padding: 18,
    },
    bannerTag: { color: "#ffd6e7", fontWeight: "700", fontSize: 12 },
    bannerTitle: {
        color: "#fff",
        fontSize: 20,
        fontWeight: "900",
        marginTop: 2,
    },
    bannerSub: { color: "rgba(255,255,255,0.95)", marginTop: 4 },
    bannerBtn: {
        backgroundColor: "#fff",
        alignSelf: "flex-start",
        borderRadius: 20,
        paddingHorizontal: 16,
        paddingVertical: 7,
        marginTop: 12,
    },
    bannerBtnText: { color: COLORS.primaryDark, fontWeight: "800", fontSize: 12 },
    infoBar: {
        flexDirection: "row",
        backgroundColor: COLORS.soft,
        marginHorizontal: 12,
        borderRadius: RADIUS.md,
        padding: 12,
    },
    infoItem: { flex: 1, flexDirection: "row", alignItems: "center", gap: 8 },
    infoDivider: {
        width: 1,
        backgroundColor: COLORS.softBorder,
        marginHorizontal: 8,
    },
    infoTitle: { fontWeight: "700", color: COLORS.text, fontSize: 12.5 },
    infoSub: { color: COLORS.textLight, fontSize: 11 },
    catCard: {
        backgroundColor: "#fff",
        marginHorizontal: 12,
        marginTop: 12,
        borderRadius: RADIUS.lg,
        paddingVertical: 14,
        paddingHorizontal: 6,
    },
    catItem: { alignItems: "center", width: 72 },
    catCircle: {
        width: 56,
        height: 56,
        borderRadius: 28,
        backgroundColor: COLORS.soft,
        borderWidth: 1.5,
        borderColor: COLORS.softBorder,
        justifyContent: "center",
        alignItems: "center",
        overflow: "hidden",
    },
    catImg: { width: 56, height: 56 },
    catLabel: {
        fontSize: 11,
        color: "#374151",
        marginTop: 6,
        textAlign: "center",
    },
    section: { marginTop: 14 },
    sectionHead: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        paddingHorizontal: 12,
        marginBottom: 8,
    },
    sectionTitle: { fontSize: 16, fontWeight: "800", color: "#111827" },
    seeAll: { color: COLORS.primaryDark, fontWeight: "700" },
    flashCard: {
        width: 120,
        backgroundColor: "#fff",
        borderRadius: 12,
        padding: 8,
    },
    flashImg: {
        width: "100%",
        height: 100,
        borderRadius: 8,
        backgroundColor: "#e5e7eb",
    },
    flashBadge: {
        position: "absolute",
        top: 14,
        left: 14,
        backgroundColor: COLORS.accent,
        borderRadius: 6,
        paddingHorizontal: 6,
        paddingVertical: 1,
    },
    flashBadgeText: { color: "#fff", fontSize: 9, fontWeight: "800" },
    flashPrice: { color: COLORS.accent, fontWeight: "900", marginTop: 6 },
    flashName: { fontSize: 12, color: "#374151", marginTop: 2 },
    filterRow: {
        gap: 8,
        paddingHorizontal: 12,
        paddingTop: 16,
        paddingBottom: 4,
    },
    chip: {
        flexDirection: "row",
        alignItems: "center",
        gap: 5,
        paddingHorizontal: 16,
        paddingVertical: 8,
        borderRadius: RADIUS.pill,
        backgroundColor: "#fff",
        borderWidth: 1,
        borderColor: COLORS.border,
    },
    chipOn: {
        backgroundColor: COLORS.primaryDark,
        borderColor: COLORS.primaryDark,
    },
    chipText: { color: COLORS.text, fontWeight: "600", fontSize: 13 },
    chipTextOn: { color: "#fff" },
    card: {
        flex: 1,
        backgroundColor: "#fff",
        borderRadius: 14,
        overflow: "hidden",
        elevation: 2,
    },
    image: { width: "100%", height: 150, backgroundColor: "#e5e7eb" },
    badge: {
        position: "absolute",
        top: 8,
        left: 8,
        backgroundColor: COLORS.accent,
        borderRadius: 6,
        paddingHorizontal: 6,
        paddingVertical: 2,
    },
    badgeText: { color: "#fff", fontSize: 10, fontWeight: "700" },
    heart: {
        position: "absolute",
        top: 8,
        right: 8,
        width: 26,
        height: 26,
        borderRadius: 13,
        backgroundColor: "rgba(255,255,255,0.92)",
        alignItems: "center",
        justifyContent: "center",
    },
    cardBody: { padding: 10 },
    name: { fontSize: 13, fontWeight: "600", color: "#111827", minHeight: 34 },
    priceRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 6,
        marginTop: 4,
        flexWrap: "wrap",
    },
    price: { fontSize: 15, fontWeight: "800", color: COLORS.accent },
    oldPrice: {
        fontSize: 12,
        color: "#9ca3af",
        textDecorationLine: "line-through",
    },
    rating: { fontSize: 11, color: "#6b7280", marginTop: 4 },
});
