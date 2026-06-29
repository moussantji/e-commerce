import React, { useCallback, useEffect, useState } from "react";
import {
    View,
    Text,
    FlatList,
    TextInput,
    Image,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    RefreshControl,
} from "react-native";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";

const ORANGE = "#FF6A00";

export default function ProductListScreen({ route, navigation }) {
    const params = route.params || {};
    const [search, setSearch] = useState(params.search || "");
    const [products, setProducts] = useState([]);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [loading, setLoading] = useState(false);
    const [refreshing, setRefreshing] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        navigation.setOptions({ title: params.title || "Produits" });
    }, [navigation, params.title]);

    const load = useCallback(
        async (pageToLoad = 1, q = "") => {
            setLoading(true);
            setError(null);
            try {
                const { data } = await api.get("/products", {
                    params: {
                        page: pageToLoad,
                        per_page: 12,
                        search: q || undefined,
                        category_id: params.categoryId || undefined,
                        featured: params.featured || undefined,
                    },
                });
                setLastPage(data.meta?.last_page ?? 1);
                setPage(data.meta?.current_page ?? pageToLoad);
                setProducts((prev) =>
                    pageToLoad === 1 ? data.data : [...prev, ...data.data],
                );
            } catch (e) {
                setError(apiError(e));
            } finally {
                setLoading(false);
                setRefreshing(false);
            }
        },
        [params.categoryId, params.featured],
    );

    useEffect(() => {
        const t = setTimeout(() => load(1, search), 350);
        return () => clearTimeout(t);
    }, [search, load]);

    const renderItem = ({ item }) => (
        <TouchableOpacity
            style={styles.card}
            onPress={() =>
                navigation.navigate("ProductDetail", {
                    id: item.id,
                    name: item.name,
                })
            }
        >
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

    return (
        <View style={styles.container}>
            <TextInput
                style={styles.searchBar}
                placeholder="🔍 Rechercher..."
                value={search}
                onChangeText={setSearch}
                autoFocus={!!params.focusSearch}
            />
            {error ? (
                <View style={styles.center}>
                    <Text style={styles.errorText}>{error}</Text>
                    <TouchableOpacity
                        style={styles.retry}
                        onPress={() => load(1, search)}
                    >
                        <Text style={styles.retryText}>Réessayer</Text>
                    </TouchableOpacity>
                </View>
            ) : (
                <FlatList
                    data={products}
                    keyExtractor={(i) => String(i.id)}
                    renderItem={renderItem}
                    numColumns={2}
                    columnWrapperStyle={{ gap: 12, paddingHorizontal: 12 }}
                    contentContainerStyle={{ gap: 12, paddingVertical: 12 }}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={() => {
                                setRefreshing(true);
                                load(1, search);
                            }}
                            colors={[ORANGE]}
                        />
                    }
                    onEndReached={() => {
                        if (!loading && page < lastPage) load(page + 1, search);
                    }}
                    onEndReachedThreshold={0.4}
                    ListEmptyComponent={
                        !loading ? (
                            <Text style={styles.empty}>Aucun produit.</Text>
                        ) : null
                    }
                    ListFooterComponent={
                        loading ? (
                            <ActivityIndicator
                                style={{ margin: 16 }}
                                color={ORANGE}
                            />
                        ) : null
                    }
                />
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: "#f3f4f6" },
    center: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        padding: 24,
    },
    searchBar: {
        backgroundColor: "#fff",
        margin: 12,
        marginBottom: 0,
        borderRadius: 12,
        paddingHorizontal: 16,
        paddingVertical: 12,
        borderWidth: 1,
        borderColor: "#e5e7eb",
        fontSize: 15,
    },
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
    empty: { textAlign: "center", color: "#6b7280", marginTop: 40 },
    errorText: { color: "#b91c1c", textAlign: "center", marginBottom: 16 },
    retry: {
        backgroundColor: ORANGE,
        borderRadius: 10,
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    retryText: { color: "#fff", fontWeight: "700" },
});
