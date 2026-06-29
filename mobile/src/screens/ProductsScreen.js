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

export default function ProductsScreen({ navigation }) {
    const [products, setProducts] = useState([]);
    const [search, setSearch] = useState("");
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [loading, setLoading] = useState(false);
    const [refreshing, setRefreshing] = useState(false);
    const [error, setError] = useState(null);

    const load = useCallback(async (pageToLoad = 1, q = "") => {
        setLoading(true);
        setError(null);
        try {
            const { data } = await api.get("/products", {
                params: { page: pageToLoad, per_page: 10, search: q },
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
    }, []);

    useEffect(() => {
        load(1, "");
    }, [load]);

    // Recherche avec léger debounce
    useEffect(() => {
        const t = setTimeout(() => load(1, search), 400);
        return () => clearTimeout(t);
    }, [search, load]);

    const onRefresh = () => {
        setRefreshing(true);
        load(1, search);
    };

    const loadMore = () => {
        if (!loading && page < lastPage) load(page + 1, search);
    };

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
            <Image
                source={{ uri: item.image }}
                style={styles.image}
                resizeMode="cover"
            />
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

    if (error) {
        return (
            <View style={styles.center}>
                <Text style={styles.errorText}>{error}</Text>
                <TouchableOpacity
                    style={styles.retry}
                    onPress={() => load(1, search)}
                >
                    <Text style={styles.retryText}>Réessayer</Text>
                </TouchableOpacity>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <TextInput
                style={styles.searchBar}
                placeholder="🔍 Rechercher un produit..."
                value={search}
                onChangeText={setSearch}
            />
            <FlatList
                data={products}
                keyExtractor={(item) => String(item.id)}
                renderItem={renderItem}
                numColumns={2}
                columnWrapperStyle={{ gap: 12, paddingHorizontal: 12 }}
                contentContainerStyle={{ gap: 12, paddingVertical: 12 }}
                refreshControl={
                    <RefreshControl
                        refreshing={refreshing}
                        onRefresh={onRefresh}
                    />
                }
                onEndReached={loadMore}
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
                            color="#6366f1"
                        />
                    ) : null
                }
            />
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
    image: { width: "100%", height: 140, backgroundColor: "#e5e7eb" },
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
    price: { fontSize: 15, fontWeight: "800", color: "#6366f1" },
    oldPrice: {
        fontSize: 12,
        color: "#9ca3af",
        textDecorationLine: "line-through",
    },
    rating: { fontSize: 11, color: "#6b7280", marginTop: 4 },
    empty: { textAlign: "center", color: "#6b7280", marginTop: 40 },
    errorText: { color: "#b91c1c", textAlign: "center", marginBottom: 16 },
    retry: {
        backgroundColor: "#6366f1",
        borderRadius: 10,
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    retryText: { color: "#fff", fontWeight: "700" },
});
