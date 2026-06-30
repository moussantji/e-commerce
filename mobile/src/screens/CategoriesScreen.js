import React, { useCallback, useEffect, useState } from "react";
import {
    View,
    Text,
    ScrollView,
    Image,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { COLORS, RADIUS } from "../theme";

const ALL = { id: "all", name: "Pour vous" };

export default function CategoriesScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const [categories, setCategories] = useState([]);
    const [active, setActive] = useState("all");
    const [items, setItems] = useState([]);
    const [mode, setMode] = useState("products"); // 'products' | 'cats'
    const [loading, setLoading] = useState(true);
    const [gridLoading, setGridLoading] = useState(false);
    const [error, setError] = useState(null);

    const fetchItems = useCallback(async (catId) => {
        const params = { per_page: 18 };
        if (catId && catId !== "all") params.category_id = catId;
        else params.featured = 1;
        const { data } = await api.get("/products", { params });
        return data.data ?? [];
    }, []);

    const load = useCallback(async () => {
        setError(null);
        try {
            const [cats, grid] = await Promise.all([
                api.get("/categories"),
                fetchItems("all"),
            ]);
            setCategories(cats.data.data ?? []);
            setItems(grid);
        } catch (e) {
            setError(apiError(e));
        } finally {
            setLoading(false);
        }
    }, [fetchItems]);

    useEffect(() => {
        load();
    }, [load]);

    const select = useCallback(
        async (catId) => {
            if (catId === active) return;
            setActive(catId);

            // Si la catégorie a des sous-catégories, on les affiche
            if (catId !== "all") {
                const cat = categories.find((c) => c.id === catId);
                if (cat?.children?.length) {
                    setMode("cats");
                    setItems(cat.children);
                    return;
                }
            }

            // Sinon : repli sur les produits de la catégorie
            setMode("products");
            setGridLoading(true);
            try {
                setItems(await fetchItems(catId));
            } catch (e) {
                setItems([]);
            } finally {
                setGridLoading(false);
            }
        },
        [active, categories, fetchItems],
    );

    const tabs = [ALL, ...categories];
    const sidebar = [ALL, ...categories];
    const activeName =
        active === "all"
            ? "Pour vous"
            : categories.find((c) => c.id === active)?.name || "Sélection";

    const goDetail = (item) =>
        navigation.navigate("ProductDetail", { id: item.id, name: item.name });

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    if (error) {
        return (
            <View style={styles.center}>
                <Text style={styles.errorText}>{error}</Text>
                <TouchableOpacity style={styles.retry} onPress={load}>
                    <Text style={styles.retryText}>Réessayer</Text>
                </TouchableOpacity>
            </View>
        );
    }

    return (
        <View style={styles.root}>
            {/* En-tête : recherche + onglets catégories */}
            <LinearGradient
                colors={COLORS.gradient}
                start={COLORS.gradientStart}
                end={COLORS.gradientEnd}
                style={[styles.header, { paddingTop: insets.top + 8 }]}
            >
                <View style={styles.searchRow}>
                    <Ionicons name="mail-outline" size={22} color="#fff" />
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
                        <Text style={styles.searchText}>Rechercher</Text>
                        <View style={styles.searchBtn}>
                            <Ionicons name="search" size={16} color="#fff" />
                        </View>
                    </TouchableOpacity>
                    <Ionicons name="heart-outline" size={22} color="#fff" />
                </View>

                <ScrollView
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    contentContainerStyle={styles.tabsRow}
                >
                    {tabs.map((t) => {
                        const on = active === t.id;
                        return (
                            <TouchableOpacity
                                key={String(t.id)}
                                style={styles.tab}
                                onPress={() => select(t.id)}
                            >
                                <Text
                                    style={[
                                        styles.tabText,
                                        on && styles.tabTextOn,
                                    ]}
                                >
                                    {t.name}
                                </Text>
                                {on && <View style={styles.tabUnderline} />}
                            </TouchableOpacity>
                        );
                    })}
                </ScrollView>
            </LinearGradient>

            {/* Corps : sidebar gauche + grille droite */}
            <View style={styles.body}>
                <ScrollView
                    style={styles.sidebar}
                    showsVerticalScrollIndicator={false}
                >
                    {sidebar.map((c) => {
                        const on = active === c.id;
                        return (
                            <TouchableOpacity
                                key={String(c.id)}
                                style={[styles.navItem, on && styles.navItemOn]}
                                onPress={() => select(c.id)}
                                activeOpacity={0.8}
                            >
                                {on && <View style={styles.navBar} />}
                                <Text
                                    style={[
                                        styles.navText,
                                        on && styles.navTextOn,
                                    ]}
                                    numberOfLines={2}
                                >
                                    {c.name}
                                </Text>
                            </TouchableOpacity>
                        );
                    })}
                </ScrollView>

                <ScrollView
                    style={styles.content}
                    showsVerticalScrollIndicator={false}
                    contentContainerStyle={{ paddingBottom: 24 }}
                >
                    <Text style={styles.sectionTitle}>{activeName}</Text>

                    {gridLoading ? (
                        <ActivityIndicator
                            color={COLORS.primary}
                            style={{ marginTop: 30 }}
                        />
                    ) : items.length === 0 ? (
                        <Text style={styles.empty}>Aucun produit.</Text>
                    ) : (
                        <View style={styles.grid}>
                            {items.map((p) => (
                                <TouchableOpacity
                                    key={p.id}
                                    style={styles.tile}
                                    activeOpacity={0.8}
                                    onPress={() =>
                                        mode === "cats"
                                            ? navigation.navigate(
                                                  "ProductList",
                                                  {
                                                      categoryId: p.id,
                                                      title: p.name,
                                                  },
                                              )
                                            : goDetail(p)
                                    }
                                >
                                    <View style={styles.tileCircle}>
                                        <Image
                                            source={{ uri: p.image }}
                                            style={styles.tileImg}
                                        />
                                    </View>
                                    <Text
                                        style={styles.tileLabel}
                                        numberOfLines={2}
                                    >
                                        {p.name}
                                    </Text>
                                </TouchableOpacity>
                            ))}
                        </View>
                    )}
                </ScrollView>
            </View>
        </View>
    );
}

const SIDEBAR_W = 104;

const styles = StyleSheet.create({
    root: { flex: 1, backgroundColor: COLORS.bg },
    center: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        padding: 24,
        backgroundColor: COLORS.bg,
    },
    header: { paddingHorizontal: 12, paddingBottom: 10 },
    searchRow: { flexDirection: "row", alignItems: "center", gap: 10 },
    search: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#fff",
        borderRadius: 22,
        paddingLeft: 14,
        paddingRight: 4,
        paddingVertical: 4,
        height: 40,
    },
    searchText: { flex: 1, color: "#9ca3af", fontSize: 14 },
    searchBtn: {
        width: 32,
        height: 32,
        borderRadius: 16,
        backgroundColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
    },
    tabsRow: { gap: 18, paddingTop: 12, paddingRight: 12, alignItems: "center" },
    tab: { alignItems: "center" },
    tabText: {
        color: "rgba(255,255,255,0.75)",
        fontSize: 15,
        fontWeight: "600",
        paddingBottom: 5,
    },
    tabTextOn: { color: "#fff", fontWeight: "800" },
    tabUnderline: {
        height: 3,
        width: 20,
        borderRadius: 3,
        backgroundColor: "#fff",
    },
    body: { flex: 1, flexDirection: "row" },
    sidebar: { width: SIDEBAR_W, backgroundColor: "#efeff5" },
    navItem: {
        paddingVertical: 16,
        paddingHorizontal: 10,
        justifyContent: "center",
    },
    navItemOn: { backgroundColor: COLORS.bg },
    navBar: {
        position: "absolute",
        left: 0,
        top: "28%",
        bottom: "28%",
        width: 3,
        borderRadius: 3,
        backgroundColor: COLORS.primaryDark,
    },
    navText: { fontSize: 12.5, color: "#4b5563", fontWeight: "500" },
    navTextOn: { color: COLORS.primaryDark, fontWeight: "800" },
    content: { flex: 1, paddingHorizontal: 12 },
    sectionTitle: {
        fontSize: 16,
        fontWeight: "800",
        color: COLORS.text,
        marginTop: 14,
        marginBottom: 10,
    },
    grid: { flexDirection: "row", flexWrap: "wrap", justifyContent: "space-between" },
    tile: { width: "31%", alignItems: "center", marginBottom: 16 },
    tileCircle: {
        width: 78,
        height: 78,
        borderRadius: 39,
        backgroundColor: "#fff",
        overflow: "hidden",
        borderWidth: 1,
        borderColor: COLORS.border,
    },
    tileImg: { width: "100%", height: "100%" },
    tileLabel: {
        fontSize: 11,
        color: COLORS.text,
        textAlign: "center",
        marginTop: 6,
        fontWeight: "500",
    },
    empty: { textAlign: "center", color: "#6b7280", marginTop: 40 },
    errorText: { color: "#b91c1c", textAlign: "center", marginBottom: 16 },
    retry: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 10,
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    retryText: { color: "#fff", fontWeight: "700" },
});
