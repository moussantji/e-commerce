import React, { useCallback, useEffect, useState } from "react";
import {
    View,
    Text,
    Image,
    TouchableOpacity,
    StyleSheet,
    FlatList,
    ActivityIndicator,
    RefreshControl,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";
import ScreenHeroHeader from "../components/ScreenHeroHeader";

const HERO_IMAGE =
    "https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1080&q=80";

const TABS = [
    { key: "on_sale", label: "Promos", icon: "pricetag" },
    { key: "new", label: "Nouveautés", icon: "sparkles" },
    { key: "popular", label: "Top ventes", icon: "flame" },
];

const TAB_PARAMS = {
    on_sale: { on_sale: 1, sort: "latest" },
    new: { sort: "latest" },
    popular: { sort: "popular" },
};

export default function DealsScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const [activeTab, setActiveTab] = useState("on_sale");
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);

    const fetchProducts = useCallback(
        async (pageNum = 1, refresh = false) => {
            try {
                if (pageNum === 1) setLoading(true);
                const params = { ...TAB_PARAMS[activeTab], page: pageNum, per_page: 20 };
                const res = await api.get("/products", { params });
                const data = res.data?.data || [];
                if (refresh || pageNum === 1) {
                    setProducts(data);
                } else {
                    setProducts((prev) => [...prev, ...data]);
                }
                setHasMore(data.length >= 20);
                setPage(pageNum);
            } catch (e) {
                console.warn("DealsScreen fetch error:", e);
            } finally {
                setLoading(false);
                setRefreshing(false);
            }
        },
        [activeTab]
    );

    useEffect(() => {
        fetchProducts(1, true);
    }, [fetchProducts]);

    const onRefresh = () => {
        setRefreshing(true);
        fetchProducts(1, true);
    };

    const onEndReached = () => {
        if (!loading && hasMore) {
            fetchProducts(page + 1);
        }
    };

    const renderProduct = ({ item }) => {
        const hasDiscount = item.sale_price && item.sale_price < item.price;
        const discount = hasDiscount
            ? Math.round(((item.price - item.sale_price) / item.price) * 100)
            : 0;

        return (
            <TouchableOpacity
                style={styles.card}
                activeOpacity={0.9}
                onPress={() =>
                    navigation.navigate("ProductDetail", {
                        id: item.id,
                        name: item.name,
                    })
                }
            >
                <View style={styles.imageContainer}>
                    <Image
                        source={{ uri: item.image }}
                        style={styles.productImage}
                        resizeMode="cover"
                    />
                    {hasDiscount && (
                        <View style={styles.discountBadge}>
                            <Text style={styles.discountText}>-{discount}%</Text>
                        </View>
                    )}
                </View>
                <View style={styles.cardBody}>
                    <Text style={styles.productName} numberOfLines={2}>
                        {item.name}
                    </Text>
                    <View style={styles.priceRow}>
                        <Text style={styles.price}>
                            {formatPrice(item.sale_price || item.price)}
                        </Text>
                        {hasDiscount && (
                            <Text style={styles.oldPrice}>
                                {formatPrice(item.price)}
                            </Text>
                        )}
                    </View>
                    {item.reviews_count > 0 && (
                        <View style={styles.ratingRow}>
                            <Ionicons name="star" size={12} color="#f59e0b" />
                            <Text style={styles.ratingText}>
                                {Number(item.reviews_avg || 0).toFixed(1)}
                            </Text>
                            <Text style={styles.reviewCount}>
                                ({item.reviews_count})
                            </Text>
                        </View>
                    )}
                </View>
            </TouchableOpacity>
        );
    };

    return (
        <View style={styles.container}>
            {/* Header hero avec photo + voile violet */}
            <ScreenHeroHeader
                image={HERO_IMAGE}
                height={140 + insets.top}
                overlayOpacity="medium"
                style={{ paddingTop: insets.top }}
            >
                <View style={styles.header}>
                    <View>
                        <Text style={styles.headerTitle}>Bons Plans</Text>
                        <Text style={styles.headerSub}>
                            Les meilleures offres du moment
                        </Text>
                    </View>
                    <TouchableOpacity
                        onPress={() => navigation.navigate("Notifications")}
                        style={styles.notifBtn}
                    >
                        <Ionicons name="notifications-outline" size={22} color="#fff" />
                    </TouchableOpacity>
                </View>
            </ScreenHeroHeader>

            {/* Tabs */}
            <View style={styles.tabBar}>
                {TABS.map((tab) => {
                    const isActive = activeTab === tab.key;
                    return (
                        <TouchableOpacity
                            key={tab.key}
                            style={[styles.tab, isActive && styles.tabActive]}
                            onPress={() => setActiveTab(tab.key)}
                        >
                            <Ionicons
                                name={isActive ? tab.icon : `${tab.icon}-outline`}
                                size={16}
                                color={isActive ? COLORS.primaryDark : "#6b7280"}
                            />
                            <Text
                                style={[
                                    styles.tabLabel,
                                    isActive && styles.tabLabelActive,
                                ]}
                            >
                                {tab.label}
                            </Text>
                        </TouchableOpacity>
                    );
                })}
            </View>

            {/* Product grid */}
            {loading && products.length === 0 ? (
                <View style={styles.loadingContainer}>
                    <ActivityIndicator size="large" color={COLORS.primary} />
                </View>
            ) : (
                <FlatList
                    data={products}
                    keyExtractor={(item) => `deal-${item.id}`}
                    renderItem={renderProduct}
                    numColumns={2}
                    columnWrapperStyle={styles.row}
                    contentContainerStyle={styles.listContent}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={onRefresh}
                            colors={[COLORS.primary]}
                        />
                    }
                    onEndReached={onEndReached}
                    onEndReachedThreshold={0.3}
                    ListEmptyComponent={
                        <View style={styles.emptyContainer}>
                            <Ionicons
                                name="pricetag-outline"
                                size={48}
                                color="#d1d5db"
                            />
                            <Text style={styles.emptyText}>
                                Aucune offre pour le moment
                            </Text>
                        </View>
                    }
                    ListFooterComponent={
                        hasMore && products.length > 0 ? (
                            <ActivityIndicator
                                style={{ marginVertical: 16 }}
                                color={COLORS.primary}
                            />
                        ) : null
                    }
                />
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: "#f9fafb",
    },
    header: {
        flexDirection: "row",
        alignItems: "flex-end",
        justifyContent: "space-between",
    },
    headerTitle: {
        fontSize: 22,
        fontWeight: "900",
        color: "#fff",
    },
    headerSub: {
        fontSize: 13,
        color: "rgba(255,255,255,0.85)",
        marginTop: 2,
    },
    notifBtn: {
        width: 38,
        height: 38,
        borderRadius: 19,
        backgroundColor: "rgba(255,255,255,0.2)",
        alignItems: "center",
        justifyContent: "center",
    },
    tabBar: {
        flexDirection: "row",
        backgroundColor: "#fff",
        paddingHorizontal: 8,
        paddingVertical: 8,
        borderBottomWidth: 1,
        borderBottomColor: "#f3f4f6",
    },
    tab: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        paddingVertical: 8,
        borderRadius: 20,
        gap: 4,
    },
    tabActive: {
        backgroundColor: COLORS.soft,
    },
    tabLabel: {
        fontSize: 12,
        fontWeight: "600",
        color: "#6b7280",
    },
    tabLabelActive: {
        color: COLORS.primaryDark,
    },
    loadingContainer: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
    },
    listContent: {
        paddingHorizontal: 8,
        paddingTop: 8,
        paddingBottom: 20,
    },
    row: {
        justifyContent: "space-between",
        paddingHorizontal: 4,
    },
    card: {
        width: "48%",
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        marginBottom: 10,
        overflow: "hidden",
        elevation: 2,
        shadowColor: "#000",
        shadowOpacity: 0.06,
        shadowRadius: 6,
        shadowOffset: { width: 0, height: 2 },
    },
    imageContainer: {
        position: "relative",
    },
    productImage: {
        width: "100%",
        aspectRatio: 0.85,
    },
    discountBadge: {
        position: "absolute",
        top: 6,
        left: 6,
        backgroundColor: "#ef4444",
        paddingHorizontal: 6,
        paddingVertical: 2,
        borderRadius: 4,
    },
    discountText: {
        color: "#fff",
        fontSize: 10,
        fontWeight: "700",
    },
    cardBody: {
        padding: 8,
    },
    productName: {
        fontSize: 12,
        color: "#1f2937",
        lineHeight: 16,
        marginBottom: 4,
    },
    priceRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 6,
    },
    price: {
        fontSize: 14,
        fontWeight: "800",
        color: COLORS.primaryDark,
    },
    oldPrice: {
        fontSize: 11,
        color: "#9ca3af",
        textDecorationLine: "line-through",
    },
    ratingRow: {
        flexDirection: "row",
        alignItems: "center",
        marginTop: 4,
        gap: 2,
    },
    ratingText: {
        fontSize: 11,
        fontWeight: "600",
        color: "#374151",
    },
    reviewCount: {
        fontSize: 10,
        color: "#9ca3af",
    },
    emptyContainer: {
        alignItems: "center",
        justifyContent: "center",
        paddingTop: 80,
    },
    emptyText: {
        marginTop: 12,
        fontSize: 14,
        color: "#9ca3af",
    },
});
