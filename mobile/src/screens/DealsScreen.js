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
import { useWishlist } from "../context/WishlistContext";
import { useAuth } from "../context/AuthContext";
import { Alert } from "react-native";
import AnimatedPressable from "../components/AnimatedPressable";
import SmartImage from "../components/SmartImage";

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
    const { isFav, toggle } = useWishlist();
    const { token } = useAuth();
    const [activeTab, setActiveTab] = useState("on_sale");
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [sticky, setSticky] = useState(false);

    // Responsive: extra padding to keep content below the system status bar
    const STATUS_BAR_PADDING = Platform.OS === "ios" ? insets.top + 12 : insets.top + 16;

    // Barre collante animée qui apparaît en descendant (comme l'accueil)
    const stickyAnim = useRef(new Animated.Value(0)).current;
    useEffect(() => {
        Animated.timing(stickyAnim, {
            toValue: sticky ? 1 : 0,
            duration: 220,
            useNativeDriver: true,
        }).start();
    }, [sticky, stickyAnim]);

    const onScroll = (e) => {
        const y = e.nativeEvent.contentOffset.y;
        const should = y > 140;
        setSticky((prev) => (prev === should ? prev : should));
    };

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

    const onToggleFav = async (productId) => {
        if (!token) {
            Alert.alert("Connexion requise", "Connectez-vous pour gérer vos favoris.");
            return;
        }
        try {
            await toggle(productId);
        } catch (e) {
            /* ignore */
        }
    };

    // Rangée d'onglets de filtre (réutilisée dans le header et la barre collante)
    const renderTabs = (compact = false) => (
        <View style={[styles.tabBar, compact && styles.tabBarCompact]}>
            {TABS.map((tab) => {
                const isActive = activeTab === tab.key;
                return (
                    <TouchableOpacity
                        key={tab.key}
                        style={[
                            styles.tab,
                            isActive && (compact ? styles.tabActiveCompact : styles.tabActive),
                        ]}
                        onPress={() => setActiveTab(tab.key)}
                    >
                        <Ionicons
                            name={isActive ? tab.icon : `${tab.icon}-outline`}
                            size={16}
                            color={
                                compact
                                    ? "#fff"
                                    : isActive
                                    ? COLORS.primaryDark
                                    : "#6b7280"
                            }
                        />
                        <Text
                            style={[
                                styles.tabLabel,
                                compact && styles.tabLabelCompact,
                                !compact && isActive && styles.tabLabelActive,
                            ]}
                        >
                            {tab.label}
                        </Text>
                    </TouchableOpacity>
                );
            })}
        </View>
    );

    const renderProduct = ({ item, index }) => {
        const hasDiscount = item.sale_price && item.sale_price < item.price;
        const discount = hasDiscount
            ? Math.round(((item.price - item.sale_price) / item.price) * 100)
            : 0;

        return (
            <AnimatedPressable
                style={[styles.card, { width: (SCREEN_WIDTH - 32) / 2 }]}
                scaleTo={0.97}
                index={index}
                onPress={() =>
                    navigation.navigate("ProductDetail", {
                        id: item.id,
                        name: item.name,
                    })
                }
            >
                <View style={styles.imageContainer}>
                    <SmartImage
                        source={item.image}
                        style={styles.productImage}
                    />
                    {hasDiscount && (
                        <View style={styles.discountBadge}>
                            <Text style={styles.discountText}>-{discount}%</Text>
                        </View>
                    )}
                    <TouchableOpacity
                        style={styles.heart}
                        onPress={() => onToggleFav(item.id)}
                        hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
                    >
                        <Ionicons
                            name={isFav(item.id) ? "heart" : "heart-outline"}
                            size={16}
                            color={isFav(item.id) ? COLORS.badge : COLORS.primaryDark}
                        />
                    </TouchableOpacity>
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
            </AnimatedPressable>
        );
    };

    // En-tête défilant : hero photo + voile violet, puis onglets de filtre
    const ListHeader = (
        <View>
            <View style={[styles.headerWrapper, { paddingTop: STATUS_BAR_PADDING }]}>
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

                <View style={styles.headerRow}>
                    <View>
                        <Text style={styles.headerTitle}>Bons Plans</Text>
                        <Text style={styles.headerSub}>
                            Les meilleures offres du moment
                        </Text>
                    </View>
                    <TouchableOpacity
                        onPress={() => navigation.navigate("Notifications")}
                        style={styles.notifBtn}
                        activeOpacity={0.7}
                    >
                        <Ionicons name="notifications-outline" size={20} color="#fff" />
                    </TouchableOpacity>
                </View>

                {/* Barre de recherche */}
                <TouchableOpacity
                    style={styles.searchBar}
                    activeOpacity={0.85}
                    onPress={() => navigation.navigate("Search", { focusSearch: true })}
                >
                    <Ionicons name="search" size={18} color="#9ca3af" />
                    <Text style={styles.searchPlaceholder}>Rechercher une offre...</Text>
                    <Ionicons name="camera-outline" size={19} color="#9ca3af" />
                </TouchableOpacity>
            </View>

            {/* Onglets de filtre (fond blanc) */}
            {renderTabs(false)}
        </View>
    );

    return (
        <View style={styles.container}>
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
                    ListHeaderComponent={ListHeader}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={onRefresh}
                            colors={[COLORS.primary]}
                        />
                    }
                    onEndReached={onEndReached}
                    onEndReachedThreshold={0.3}
                    onScroll={onScroll}
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

            {/* Barre collante violette : apparaît en descendant (titre + onglets) */}
            <Animated.View
                pointerEvents={sticky ? "auto" : "none"}
                style={[
                    styles.stickyBar,
                    {
                        paddingTop: STATUS_BAR_PADDING,
                        opacity: stickyAnim,
                        transform: [
                            {
                                translateY: stickyAnim.interpolate({
                                    inputRange: [0, 1],
                                    outputRange: [-20, 0],
                                }),
                            },
                        ],
                    },
                ]}
            >
                <LinearGradient
                    colors={COLORS.gradient}
                    start={COLORS.gradientStart}
                    end={COLORS.gradientEnd}
                    style={StyleSheet.absoluteFill}
                />
                <View style={styles.stickyTitleRow}>
                    <Text style={styles.stickyTitle}>Bons Plans</Text>
                    <View style={{ flexDirection: "row", gap: 8 }}>
                        <TouchableOpacity
                            onPress={() => navigation.navigate("Search", { focusSearch: true })}
                            style={styles.stickyNotifBtn}
                            activeOpacity={0.7}
                        >
                            <Ionicons name="search" size={19} color="#fff" />
                        </TouchableOpacity>
                        <TouchableOpacity
                            onPress={() => navigation.navigate("Notifications")}
                            style={styles.stickyNotifBtn}
                            activeOpacity={0.7}
                        >
                            <Ionicons name="notifications-outline" size={20} color="#fff" />
                        </TouchableOpacity>
                    </View>
                </View>
                {renderTabs(true)}
            </Animated.View>
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
        paddingHorizontal: 16,
        paddingBottom: 16,
        overflow: "hidden",
    },
    headerRow: {
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
        color: "rgba(255,255,255,0.9)",
        marginTop: 2,
    },
    searchBar: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#fff",
        borderRadius: RADIUS.pill,
        paddingHorizontal: 14,
        height: 40,
        marginTop: 14,
    },
    searchPlaceholder: { flex: 1, color: "#9ca3af", fontSize: 14 },
    notifBtn: {
        width: 40,
        height: 40,
        borderRadius: 20,
        backgroundColor: "rgba(255,255,255,0.18)",
        alignItems: "center",
        justifyContent: "center",
    },
    // Sticky bar
    stickyBar: {
        position: "absolute",
        top: 0,
        left: 0,
        right: 0,
        zIndex: 30,
        paddingHorizontal: 12,
        paddingBottom: 6,
    },
    stickyTitleRow: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingHorizontal: 4,
        marginBottom: 4,
    },
    stickyTitle: { fontSize: 18, fontWeight: "900", color: "#fff" },
    stickyNotifBtn: {
        width: 36,
        height: 36,
        borderRadius: 18,
        backgroundColor: "rgba(255,255,255,0.18)",
        alignItems: "center",
        justifyContent: "center",
    },
    // Tab bar
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
    tabBarCompact: {
        backgroundColor: "transparent",
        borderBottomWidth: 0,
        elevation: 0,
        shadowOpacity: 0,
        paddingVertical: 4,
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
    tabActiveCompact: {
        backgroundColor: "rgba(255,255,255,0.22)",
    },
    tabLabel: {
        fontSize: 12,
        fontWeight: "600",
        color: "#6b7280",
    },
    tabLabelActive: {
        color: COLORS.primaryDark,
    },
    tabLabelCompact: {
        color: "#fff",
    },
    loadingContainer: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
    },
    listContent: {
        paddingHorizontal: 8,
        paddingBottom: 20,
    },
    row: {
        justifyContent: "space-between",
        paddingHorizontal: 4,
        marginTop: 8,
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
    heart: {
        position: "absolute",
        top: 6,
        right: 6,
        width: 28,
        height: 28,
        borderRadius: 14,
        backgroundColor: "rgba(255,255,255,0.92)",
        alignItems: "center",
        justifyContent: "center",
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
