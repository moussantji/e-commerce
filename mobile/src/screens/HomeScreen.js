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
import api from "../api/client";
import { formatPrice } from "../utils";

const ORANGE = "#FF6A00";

export default function HomeScreen({ navigation }) {
    const [categories, setCategories] = useState([]);
    const [flash, setFlash] = useState([]);
    const [products, setProducts] = useState([]);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

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
            {/* Bannière promo */}
            <View style={styles.banner}>
                <View style={{ flex: 1 }}>
                    <Text style={styles.bannerTitle}>Méga soldes 🎉</Text>
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
                        <Text style={styles.bannerBtnText}>J'en profite</Text>
                    </TouchableOpacity>
                </View>
                <Ionicons
                    name="pricetags"
                    size={64}
                    color="rgba(255,255,255,0.85)"
                />
            </View>

            {/* Catégories horizontales */}
            {categories.length > 0 && (
                <ScrollView
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    style={styles.catRow}
                    contentContainerStyle={{ paddingHorizontal: 12 }}
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
                                        size={26}
                                        color={ORANGE}
                                    />
                                )}
                            </View>
                            <Text style={styles.catLabel} numberOfLines={1}>
                                {c.name}
                            </Text>
                        </TouchableOpacity>
                    ))}
                </ScrollView>
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

            <Text
                style={[
                    styles.sectionTitle,
                    { marginHorizontal: 12, marginTop: 6, marginBottom: 4 },
                ]}
            >
                Pour vous
            </Text>
        </View>
    );

    const renderProduct = ({ item }) => (
        <TouchableOpacity style={styles.card} onPress={() => goDetail(item)}>
            <Image source={{ uri: item.image }} style={styles.image} />
            {item.sale_price ? (
                <View style={styles.badge}>
                    <Text style={styles.badgeText}>Promo</Text>
                </View>
            ) : null}
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

    if (loading) {
        return (
            <View style={styles.container}>
                <SearchBar navigation={navigation} />
                <View style={styles.center}>
                    <ActivityIndicator size="large" color={ORANGE} />
                </View>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <SearchBar navigation={navigation} />
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
                        colors={[ORANGE]}
                    />
                }
            />
        </View>
    );
}

function SearchBar({ navigation }) {
    return (
        <View style={styles.searchHeader}>
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
            </TouchableOpacity>
            <TouchableOpacity
                style={styles.cartIcon}
                onPress={() => navigation.navigate("Panier")}
            >
                <Ionicons name="cart-outline" size={24} color="#fff" />
            </TouchableOpacity>
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: "#f3f4f6" },
    center: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        paddingTop: 60,
    },
    searchHeader: {
        flexDirection: "row",
        alignItems: "center",
        gap: 10,
        backgroundColor: ORANGE,
        paddingHorizontal: 12,
        paddingTop: 12,
        paddingBottom: 12,
    },
    search: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#fff",
        borderRadius: 22,
        paddingHorizontal: 16,
        paddingVertical: 10,
    },
    searchPlaceholder: { color: "#9ca3af", fontSize: 14 },
    cartIcon: { padding: 4 },
    banner: {
        flexDirection: "row",
        alignItems: "center",
        backgroundColor: "#FF8A3D",
        margin: 12,
        borderRadius: 16,
        padding: 18,
    },
    bannerTitle: { color: "#fff", fontSize: 20, fontWeight: "900" },
    bannerSub: { color: "rgba(255,255,255,0.95)", marginTop: 4 },
    bannerBtn: {
        backgroundColor: "#fff",
        alignSelf: "flex-start",
        borderRadius: 20,
        paddingHorizontal: 16,
        paddingVertical: 7,
        marginTop: 12,
    },
    bannerBtnText: { color: ORANGE, fontWeight: "800" },
    catRow: { marginBottom: 4 },
    catItem: { alignItems: "center", width: 72, marginRight: 4 },
    catCircle: {
        width: 56,
        height: 56,
        borderRadius: 28,
        backgroundColor: "#fff",
        justifyContent: "center",
        alignItems: "center",
        overflow: "hidden",
        elevation: 1,
    },
    catImg: { width: 56, height: 56 },
    catLabel: {
        fontSize: 11,
        color: "#374151",
        marginTop: 6,
        textAlign: "center",
    },
    section: { marginTop: 8 },
    sectionHead: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        paddingHorizontal: 12,
        marginBottom: 8,
    },
    sectionTitle: { fontSize: 16, fontWeight: "800", color: "#111827" },
    seeAll: { color: ORANGE, fontWeight: "600" },
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
    flashPrice: { color: ORANGE, fontWeight: "900", marginTop: 6 },
    flashName: { fontSize: 12, color: "#374151", marginTop: 2 },
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
        backgroundColor: "#22c55e",
        borderRadius: 6,
        paddingHorizontal: 6,
        paddingVertical: 2,
    },
    badgeText: { color: "#fff", fontSize: 10, fontWeight: "700" },
    cardBody: { padding: 10 },
    name: { fontSize: 13, fontWeight: "600", color: "#111827", minHeight: 34 },
    priceRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 6,
        marginTop: 4,
        flexWrap: "wrap",
    },
    price: { fontSize: 15, fontWeight: "800", color: ORANGE },
    oldPrice: {
        fontSize: 12,
        color: "#9ca3af",
        textDecorationLine: "line-through",
    },
    rating: { fontSize: 11, color: "#6b7280", marginTop: 4 },
});
