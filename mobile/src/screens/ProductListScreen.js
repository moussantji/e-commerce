import React, { useCallback, useEffect, useRef, useState } from "react";
import {
    View,
    Text,
    FlatList,
    Image,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    RefreshControl,
    ScrollView,
    Modal,
    Pressable,
    Dimensions,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { useCart } from "../context/CartContext";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const { width: SCREEN_WIDTH } = Dimensions.get("window");
const CARD_WIDTH = (SCREEN_WIDTH - 24) / 2;

const SORTS = [
    { key: "latest", label: "Recommander" },
    { key: "popular", label: "Les plus populaires" },
    { key: "price_asc", label: "Prix croissant" },
    { key: "price_desc", label: "Prix décroissant" },
];

export default function ProductListScreen({ route, navigation }) {
    const params = route.params || {};
    const insets = useSafeAreaInsets();
    const { add } = useCart();

    const [search] = useState(params.search || "");
    const [products, setProducts] = useState([]);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [loading, setLoading] = useState(false);
    const [refreshing, setRefreshing] = useState(false);
    const [error, setError] = useState(null);

    const [sort, setSort] = useState("latest");
    const [sortOpen, setSortOpen] = useState(false);

    // Facettes de filtre (catégories, marques, caractéristiques, prix)
    const [facets, setFacets] = useState(null);
    // Sélections : { brands: [], carac: { Color: [...] }, category_id, min_price, max_price }
    const [selBrands, setSelBrands] = useState([]);
    const [selCarac, setSelCarac] = useState({});
    const [filterOpen, setFilterOpen] = useState(false);
    const [activeChip, setActiveChip] = useState(null); // type ouvert dans le bottom-sheet

    useEffect(() => {
        navigation.setOptions?.({ headerShown: false });
    }, [navigation]);

    const buildParams = useCallback(
        (pageToLoad) => {
            const p = {
                page: pageToLoad,
                per_page: 12,
                sort,
                search: search || undefined,
                category_id: params.categoryId || undefined,
                featured: params.featured || undefined,
            };
            if (selBrands.length) p.brands = selBrands;
            Object.entries(selCarac).forEach(([type, values]) => {
                if (values?.length) p[`carac[${type}]`] = values;
            });
            return p;
        },
        [sort, search, params.categoryId, params.featured, selBrands, selCarac],
    );

    const load = useCallback(
        async (pageToLoad = 1) => {
            setLoading(true);
            setError(null);
            try {
                const { data } = await api.get("/products", {
                    params: buildParams(pageToLoad),
                });
                setLastPage(data.meta?.last_page ?? 1);
                setPage(data.meta?.current_page ?? pageToLoad);
                setProducts((prev) => {
                    if (pageToLoad === 1) return data.data ?? [];
                    const seen = new Set(prev.map((x) => x.id));
                    return [...prev, ...(data.data ?? []).filter((x) => !seen.has(x.id))];
                });
            } catch (e) {
                setError(apiError(e));
            } finally {
                setLoading(false);
                setRefreshing(false);
            }
        },
        [buildParams],
    );

    const loadFacets = useCallback(async () => {
        try {
            const { data } = await api.get("/products/filters", {
                params: {
                    search: search || undefined,
                    category_id: params.categoryId || undefined,
                },
            });
            setFacets(data);
        } catch (e) {
            setFacets(null);
        }
    }, [search, params.categoryId]);

    useEffect(() => {
        loadFacets();
    }, [loadFacets]);

    // Recharge quand tri/filtres changent
    useEffect(() => {
        load(1);
    }, [load]);

    const toggleCarac = (type, value) => {
        setSelCarac((prev) => {
            const cur = prev[type] || [];
            const next = cur.includes(value)
                ? cur.filter((v) => v !== value)
                : [...cur, value];
            const clone = { ...prev, [type]: next };
            if (!next.length) delete clone[type];
            return clone;
        });
    };

    const toggleBrand = (name) => {
        setSelBrands((prev) =>
            prev.includes(name) ? prev.filter((b) => b !== name) : [...prev, name],
        );
    };

    const clearAll = () => {
        setSelBrands([]);
        setSelCarac({});
    };

    const activeFilterCount =
        selBrands.length + Object.values(selCarac).reduce((n, v) => n + v.length, 0);

    const renderItem = ({ item }) => {
        const hasDiscount = item.sale_price && item.sale_price < item.price;
        const discount = hasDiscount
            ? Math.round(((item.price - item.sale_price) / item.price) * 100)
            : 0;
        const lowStock = item.stock != null && item.stock > 0 && item.stock <= 5;

        return (
            <TouchableOpacity
                style={styles.card}
                activeOpacity={0.9}
                onPress={() =>
                    navigation.navigate("ProductDetail", { id: item.id, name: item.name })
                }
            >
                <View style={styles.imgWrap}>
                    <Image source={{ uri: item.image }} style={styles.image} />
                    {hasDiscount ? (
                        <View style={styles.badge}>
                            <Text style={styles.badgeText}>-{discount}%</Text>
                        </View>
                    ) : null}
                </View>
                <View style={styles.cardBody}>
                    <Text style={styles.name} numberOfLines={2}>
                        {item.name}
                    </Text>
                    {lowStock ? (
                        <Text style={styles.lowStock}>⏳ Plus que {item.stock}</Text>
                    ) : null}
                    <View style={styles.priceRow}>
                        <Text style={styles.price}>
                            {formatPrice(item.sale_price ?? item.price)}
                        </Text>
                        {hasDiscount ? (
                            <Text style={styles.oldPrice}>{formatPrice(item.price)}</Text>
                        ) : null}
                        <TouchableOpacity
                            style={styles.addBtn}
                            onPress={() => add(item.id, 1).catch(() => {})}
                            activeOpacity={0.8}
                        >
                            <Ionicons name="cart" size={16} color="#fff" />
                        </TouchableOpacity>
                    </View>
                    {item.rating_count > 0 ? (
                        <Text style={styles.rating}>
                            ⭐ {item.rating_avg ?? 0} ({item.rating_count})
                        </Text>
                    ) : null}
                </View>
            </TouchableOpacity>
        );
    };

    const sortLabel = SORTS.find((s) => s.key === sort)?.label || "Recommander";

    return (
        <View style={[styles.container, { paddingTop: insets.top }]}>
            {/* En-tête : retour + recherche + wishlist */}
            <View style={styles.header}>
                <TouchableOpacity onPress={() => navigation.goBack()} hitSlop={10}>
                    <Ionicons name="chevron-back" size={26} color={COLORS.text} />
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.searchBox}
                    activeOpacity={0.8}
                    onPress={() => navigation.navigate("Search", { focusSearch: true })}
                >
                    <Text style={styles.searchText} numberOfLines={1}>
                        {search || "Rechercher..."}
                    </Text>
                    <Ionicons name="camera-outline" size={19} color="#9ca3af" />
                </TouchableOpacity>
                <TouchableOpacity onPress={() => navigation.navigate("Search", { focusSearch: true })} hitSlop={8}>
                    <Ionicons name="search" size={22} color={COLORS.text} />
                </TouchableOpacity>
            </View>

            {/* Barre de tri + filtre */}
            <View style={styles.sortBar}>
                <TouchableOpacity
                    style={styles.sortItem}
                    onPress={() => setSortOpen(true)}
                >
                    <Text style={styles.sortText} numberOfLines={1}>
                        {sortLabel}
                    </Text>
                    <Ionicons name="chevron-down" size={14} color={COLORS.text} />
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.sortItem}
                    onPress={() => setSort("popular")}
                >
                    <Text
                        style={[styles.sortText, sort === "popular" && styles.sortActive]}
                    >
                        Populaires
                    </Text>
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.sortItem}
                    onPress={() =>
                        setSort((s) => (s === "price_asc" ? "price_desc" : "price_asc"))
                    }
                >
                    <Text
                        style={[
                            styles.sortText,
                            (sort === "price_asc" || sort === "price_desc") &&
                                styles.sortActive,
                        ]}
                    >
                        Prix
                    </Text>
                    <Ionicons
                        name={sort === "price_asc" ? "arrow-up" : "arrow-down"}
                        size={13}
                        color={
                            sort === "price_asc" || sort === "price_desc"
                                ? COLORS.primaryDark
                                : COLORS.text
                        }
                    />
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.sortItem}
                    onPress={() => {
                        setActiveChip(null);
                        setFilterOpen(true);
                    }}
                >
                    <Text style={styles.sortText}>Filtre</Text>
                    <Ionicons name="options-outline" size={15} color={COLORS.text} />
                    {activeFilterCount > 0 && (
                        <View style={styles.filterCount}>
                            <Text style={styles.filterCountText}>{activeFilterCount}</Text>
                        </View>
                    )}
                </TouchableOpacity>
            </View>

            {/* Puces de filtres dynamiques (Catégories, Color, Material...) */}
            {facets?.caracteristiques?.length > 0 && (
                <View style={styles.chipsBar}>
                    <ScrollView
                        horizontal
                        showsHorizontalScrollIndicator={false}
                        contentContainerStyle={{ gap: 8, paddingHorizontal: 12 }}
                    >
                        {facets.caracteristiques.map((c) => {
                            const count = selCarac[c.type]?.length || 0;
                            return (
                                <TouchableOpacity
                                    key={c.type}
                                    style={[styles.chip, count > 0 && styles.chipActive]}
                                    onPress={() => {
                                        setActiveChip(c.type);
                                        setFilterOpen(true);
                                    }}
                                >
                                    <Text
                                        style={[
                                            styles.chipText,
                                            count > 0 && styles.chipTextActive,
                                        ]}
                                    >
                                        {c.label}
                                        {count > 0 ? ` (${count})` : ""}
                                    </Text>
                                    <Ionicons
                                        name="chevron-down"
                                        size={13}
                                        color={count > 0 ? "#fff" : COLORS.textLight}
                                    />
                                </TouchableOpacity>
                            );
                        })}
                    </ScrollView>
                </View>
            )}

            {/* Grille */}
            {error ? (
                <View style={styles.center}>
                    <Text style={styles.errorText}>{error}</Text>
                    <TouchableOpacity style={styles.retry} onPress={() => load(1)}>
                        <Text style={styles.retryText}>Réessayer</Text>
                    </TouchableOpacity>
                </View>
            ) : (
                <FlatList
                    data={products}
                    keyExtractor={(i) => String(i.id)}
                    renderItem={renderItem}
                    numColumns={2}
                    columnWrapperStyle={{ gap: 8, paddingHorizontal: 8 }}
                    contentContainerStyle={{ gap: 8, paddingVertical: 8, paddingBottom: 24 }}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={() => {
                                setRefreshing(true);
                                load(1);
                            }}
                            colors={[COLORS.primary]}
                        />
                    }
                    onEndReached={() => {
                        if (!loading && page < lastPage) load(page + 1);
                    }}
                    onEndReachedThreshold={0.4}
                    ListEmptyComponent={
                        !loading ? (
                            <Text style={styles.empty}>Aucun produit trouvé.</Text>
                        ) : null
                    }
                    ListFooterComponent={
                        loading ? (
                            <ActivityIndicator style={{ margin: 16 }} color={COLORS.primary} />
                        ) : null
                    }
                />
            )}

            {/* Bottom-sheet de tri */}
            <Modal visible={sortOpen} transparent animationType="fade" onRequestClose={() => setSortOpen(false)}>
                <Pressable style={styles.backdrop} onPress={() => setSortOpen(false)}>
                    <Pressable style={[styles.sheet, { paddingBottom: insets.bottom + 12 }]}>
                        <Text style={styles.sheetTitle}>Trier par</Text>
                        {SORTS.map((s) => (
                            <TouchableOpacity
                                key={s.key}
                                style={styles.sheetRow}
                                onPress={() => {
                                    setSort(s.key);
                                    setSortOpen(false);
                                }}
                            >
                                <Text
                                    style={[
                                        styles.sheetRowText,
                                        sort === s.key && styles.sheetRowTextActive,
                                    ]}
                                >
                                    {s.label}
                                </Text>
                                {sort === s.key && (
                                    <Ionicons name="checkmark" size={20} color={COLORS.primaryDark} />
                                )}
                            </TouchableOpacity>
                        ))}
                    </Pressable>
                </Pressable>
            </Modal>

            {/* Bottom-sheet de filtres */}
            <Modal visible={filterOpen} transparent animationType="slide" onRequestClose={() => setFilterOpen(false)}>
                <Pressable style={styles.backdrop} onPress={() => setFilterOpen(false)}>
                    <Pressable style={[styles.filterSheet, { paddingBottom: insets.bottom + 12 }]}>
                        <View style={styles.filterHead}>
                            <Text style={styles.sheetTitle}>Filtres</Text>
                            <TouchableOpacity onPress={clearAll}>
                                <Text style={styles.clearText}>Tout effacer</Text>
                            </TouchableOpacity>
                        </View>

                        <ScrollView showsVerticalScrollIndicator={false}>
                            {/* Marques */}
                            {facets?.brands?.length > 0 && (
                                <View style={styles.filterSection}>
                                    <Text style={styles.filterLabel}>Marques</Text>
                                    <View style={styles.valuesWrap}>
                                        {facets.brands.map((b) => {
                                            const on = selBrands.includes(b.name);
                                            return (
                                                <TouchableOpacity
                                                    key={b.id}
                                                    style={[styles.value, on && styles.valueOn]}
                                                    onPress={() => toggleBrand(b.name)}
                                                >
                                                    <Text style={[styles.valueText, on && styles.valueTextOn]}>
                                                        {b.name}
                                                    </Text>
                                                </TouchableOpacity>
                                            );
                                        })}
                                    </View>
                                </View>
                            )}

                            {/* Caractéristiques par type */}
                            {facets?.caracteristiques?.map((c) => (
                                <View
                                    key={c.type}
                                    style={[
                                        styles.filterSection,
                                        activeChip === c.type && styles.filterSectionActive,
                                    ]}
                                >
                                    <Text style={styles.filterLabel}>{c.label}</Text>
                                    <View style={styles.valuesWrap}>
                                        {c.values.map((v) => {
                                            const on = (selCarac[c.type] || []).includes(v);
                                            return (
                                                <TouchableOpacity
                                                    key={v}
                                                    style={[styles.value, on && styles.valueOn]}
                                                    onPress={() => toggleCarac(c.type, v)}
                                                >
                                                    <Text style={[styles.valueText, on && styles.valueTextOn]}>
                                                        {v}
                                                    </Text>
                                                </TouchableOpacity>
                                            );
                                        })}
                                    </View>
                                </View>
                            ))}

                            {(!facets ||
                                (!facets.brands?.length && !facets.caracteristiques?.length)) && (
                                <Text style={styles.empty}>Aucun filtre disponible.</Text>
                            )}
                        </ScrollView>

                        <TouchableOpacity
                            style={styles.applyBtn}
                            onPress={() => setFilterOpen(false)}
                        >
                            <Text style={styles.applyText}>
                                Voir les résultats
                                {activeFilterCount > 0 ? ` (${activeFilterCount})` : ""}
                            </Text>
                        </TouchableOpacity>
                    </Pressable>
                </Pressable>
            </Modal>
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: "#f3f4f6" },
    header: {
        flexDirection: "row",
        alignItems: "center",
        gap: 10,
        paddingHorizontal: 12,
        paddingVertical: 8,
        backgroundColor: "#fff",
    },
    searchBox: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#f3f4f6",
        borderRadius: RADIUS.pill,
        paddingHorizontal: 14,
        height: 38,
    },
    searchText: { flex: 1, color: COLORS.text, fontSize: 14 },
    sortBar: {
        flexDirection: "row",
        alignItems: "center",
        backgroundColor: "#fff",
        paddingVertical: 10,
        paddingHorizontal: 8,
        borderTopWidth: 1,
        borderTopColor: "#f3f4f6",
    },
    sortItem: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 2,
    },
    sortText: { fontSize: 12.5, color: COLORS.text, fontWeight: "600" },
    sortActive: { color: COLORS.primaryDark, fontWeight: "800" },
    filterCount: {
        marginLeft: 2,
        minWidth: 16,
        height: 16,
        borderRadius: 8,
        backgroundColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
        paddingHorizontal: 4,
    },
    filterCountText: { color: "#fff", fontSize: 9, fontWeight: "800" },
    chipsBar: {
        backgroundColor: "#fff",
        paddingBottom: 10,
        borderBottomWidth: 1,
        borderBottomColor: "#eee",
    },
    chip: {
        flexDirection: "row",
        alignItems: "center",
        gap: 3,
        backgroundColor: "#f3f4f6",
        borderRadius: RADIUS.pill,
        paddingHorizontal: 12,
        paddingVertical: 6,
    },
    chipActive: { backgroundColor: COLORS.primaryDark },
    chipText: { fontSize: 12.5, color: COLORS.text, fontWeight: "600" },
    chipTextActive: { color: "#fff" },
    // Cartes
    card: {
        width: CARD_WIDTH,
        backgroundColor: "#fff",
        borderRadius: 10,
        overflow: "hidden",
    },
    imgWrap: { position: "relative" },
    image: { width: "100%", aspectRatio: 0.85, backgroundColor: "#e5e7eb" },
    badge: {
        position: "absolute",
        top: 6,
        left: 6,
        backgroundColor: "#ef4444",
        borderRadius: 4,
        paddingHorizontal: 6,
        paddingVertical: 2,
    },
    badgeText: { color: "#fff", fontSize: 10, fontWeight: "800" },
    cardBody: { padding: 8 },
    name: { fontSize: 12.5, color: "#1f2937", lineHeight: 16, minHeight: 32 },
    lowStock: { fontSize: 11, color: "#f59e0b", fontWeight: "600", marginTop: 3 },
    priceRow: { flexDirection: "row", alignItems: "center", gap: 5, marginTop: 4 },
    price: { fontSize: 15, fontWeight: "900", color: COLORS.accent },
    oldPrice: {
        fontSize: 11,
        color: "#9ca3af",
        textDecorationLine: "line-through",
    },
    addBtn: {
        marginLeft: "auto",
        width: 30,
        height: 30,
        borderRadius: 15,
        backgroundColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
    },
    rating: { fontSize: 11, color: "#6b7280", marginTop: 4 },
    empty: { textAlign: "center", color: "#6b7280", marginTop: 40 },
    center: { flex: 1, justifyContent: "center", alignItems: "center", padding: 24 },
    errorText: { color: "#b91c1c", textAlign: "center", marginBottom: 16 },
    retry: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 10,
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    retryText: { color: "#fff", fontWeight: "700" },
    // Bottom-sheets
    backdrop: {
        flex: 1,
        backgroundColor: "rgba(0,0,0,0.4)",
        justifyContent: "flex-end",
    },
    sheet: {
        backgroundColor: "#fff",
        borderTopLeftRadius: 20,
        borderTopRightRadius: 20,
        paddingTop: 16,
        paddingHorizontal: 16,
    },
    sheetTitle: { fontSize: 16, fontWeight: "800", color: COLORS.text, marginBottom: 8 },
    sheetRow: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingVertical: 14,
        borderBottomWidth: 1,
        borderBottomColor: "#f3f4f6",
    },
    sheetRowText: { fontSize: 15, color: COLORS.text },
    sheetRowTextActive: { color: COLORS.primaryDark, fontWeight: "800" },
    filterSheet: {
        backgroundColor: "#fff",
        borderTopLeftRadius: 20,
        borderTopRightRadius: 20,
        paddingTop: 16,
        paddingHorizontal: 16,
        maxHeight: "80%",
    },
    filterHead: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginBottom: 12,
    },
    clearText: { color: COLORS.primaryDark, fontWeight: "700", fontSize: 13 },
    filterSection: { marginBottom: 20 },
    filterSectionActive: {
        backgroundColor: COLORS.soft,
        borderRadius: RADIUS.md,
        padding: 10,
        marginHorizontal: -4,
    },
    filterLabel: { fontSize: 14, fontWeight: "800", color: COLORS.text, marginBottom: 10 },
    valuesWrap: { flexDirection: "row", flexWrap: "wrap", gap: 8 },
    value: {
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: RADIUS.sm,
        paddingHorizontal: 14,
        paddingVertical: 8,
        backgroundColor: "#fff",
    },
    valueOn: { borderColor: COLORS.primaryDark, backgroundColor: COLORS.soft },
    valueText: { fontSize: 13, color: COLORS.text },
    valueTextOn: { color: COLORS.primaryDark, fontWeight: "700" },
    applyBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 15,
        alignItems: "center",
        marginTop: 8,
    },
    applyText: { color: "#fff", fontWeight: "800", fontSize: 15 },
});
