import React, { useCallback, useEffect, useRef, useState } from "react";
import {
    View,
    Text,
    Image,
    TouchableOpacity,
    StyleSheet,
    FlatList,
    ActivityIndicator,
    RefreshControl,
    Animated,
    ImageBackground,
    Platform,
    Dimensions,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const HERO_IMAGE =
    "https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1080&q=80";

const { width: SCREEN_WIDTH } = Dimensions.get("window");

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

    const scrollY = useRef(new Animated.Value(0)).current;

    // Responsive: extra padding above the bell icon to avoid system status bar icons
    const STATUS_BAR_PADDING = Platform.OS === "ios" ? insets.top + 12 : insets.top + 16;

    // Header shrinks only a little (not folding completely)
    const HEADER_EXPANDED = 160;
    const HEADER_COLLAPSED = 110;
    const SCROLL_DISTANCE = HEADER_EXPANDED - HEADER_COLLAPSED;

    const headerHeight = scrollY.interpolate({
        inputRange: [0, SCROLL_DISTANCE],
        outputRange: [HEADER_EXPANDED + STATUS_BAR_PADDING, HEADER_COLLAPSED + STATUS_BAR_PADDING],
        extrapolate: "clamp",
    });

    // Subtitle fades out slightly
    const subtitleOpacity = scrollY.interpolate({
        inputRange: [0, SCROLL_DISTANCE * 0.7],
        outputRange: [1, 0],
        extrapolate: "clamp",
    });

    // Title font shrinks just a tiny bit
    const titleFontSize = scrollY.interpolate({
        inputRange: [0, SCROLL_DISTANCE],
        outputRange: [22, 18],
        extrapolate: "clamp",
    });

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
                style={[styles.card, { width: (SCREEN_WIDTH - 32) / 2 }]}
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
            {/* Header with photo + purple overlay — shrinks slightly on scroll */}
            <Animated.View style={[styles.headerWrapper, { height: headerHeight }]}>
                <ImageBackground
                    source={{ uri: HERO_IMAGE }}
                    style={StyleSheet.absoluteFill}
                    resizeMode="cover"
                >
                    <LinearGradient
                        colors={["rgba(102,126,234,0.6)", "rgba(118,75,162,0.88)"]}
                        start={{ x: 0, y: 0 }}
                        end={{ x: 1, y: 1 }}
                        style={StyleSheet.absoluteFill}
                    />
                </ImageBackground>

                <View style={[styles.headerContent, { paddingTop: STATUS_BAR_PADDING }]}>
                    {/* Top row: title + notification bell (well below status bar) */}
                    <View style={styles.headerTopRow}>
                        <Animated.Text style={[styles.headerTitle, { fontSize: titleFontSize }]}>
                            Bons Plans
                        </Animated.Text>
                        <TouchableOpacity
                            onPress={() => navigation.navigate("Notifications")}
                            style={styles.notifBtn}
                            activeOpacity={0.7}
                        >
                            <Ionicons name="notifications-outline" size={20} color="#fff" />
                        </TouchableOpacity>
                    </View>

                    {/* Subtitle — fades out on scroll */}
                    <Animated.Text style={[styles.headerSub, { opacity: subtitleOpacity }]}>
                        Les meilleures offres du moment
                    </Animated.Text>
                </View>
            </Animated.View>

            {/* Sticky tabs */}
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
                <Animated.FlatList
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
                    onScroll={Animated.event(
                        [{ nativeEvent: { contentOffset: { y: scrollY } } }],
                        { useNativeDriver: false }
                    )}
                    scrollEventThrottle={16}
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
    headerWrapper: {
        width: "100%",
        overflow: "hidden",
    },
    headerContent: {
        flex: 1,
        justifyContent: "flex-end",
        paddingHorizontal: 16,
        paddingBottom: 14,
    },
    headerTopRow: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginBottom: 4,
    },
    headerTitle: {
        fontWeight: "900",
        color: "#fff",
    },
    headerSub: {
        fontSize: 13,
        color: "rgba(255,255,255,0.9)",
        marginTop: 2,
    },
    notifBtn: {
        width: 40,
        height: 40,
        borderRadius: 20,
        backgroundColor: "rgba(255,255,255,0.18)",
        alignItems: "center",
        justifyContent: "center",
        // Extra margin to stay well below system status bar icons
        marginTop: 0,
    },
    tabBar: {
        flexDirection: "row",
        backgroundColor: "#fff",
        paddingHorizontal: 8,
        paddingVertical: 8,
        borderBottomWidth: 1,
        borderBottomColor: "#f3f4f6",
        elevation: 2,
        shadowColor: "#000",
        shadowOpacity: 0.04,
        shadowRadius: 4,
        shadowOffset: { width: 0, height: 2 },
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
