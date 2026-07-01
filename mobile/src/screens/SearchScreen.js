import React, { useCallback, useEffect, useRef, useState } from "react";
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
    Image,
    Keyboard,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import * as SecureStore from "expo-secure-store";
import api from "../api/client";
import { COLORS, RADIUS } from "../theme";

const RECENT_KEY = "recent_searches";
const MAX_RECENT = 12;

// Icône selon le type de suggestion renvoyé par l'API
const TYPE_ICON = {
    produit: "cube-outline",
    categorie: "grid-outline",
    marque: "pricetags-outline",
    tag: "bookmark-outline",
    tous: "search-outline",
};

const TYPE_LABEL = {
    produit: "Produit",
    categorie: "Catégorie",
    marque: "Marque",
    tag: "Tag",
    tous: "Tous les résultats",
};

export default function SearchScreen({ navigation, route }) {
    const insets = useSafeAreaInsets();
    const [query, setQuery] = useState("");
    const [recent, setRecent] = useState([]);
    const [popular, setPopular] = useState([]);
    const [suggestions, setSuggestions] = useState([]);
    const debounceRef = useRef(null);

    // Charge les recherches récentes (stockées localement)
    const loadRecent = useCallback(async () => {
        try {
            const raw = await SecureStore.getItemAsync(RECENT_KEY);
            setRecent(raw ? JSON.parse(raw) : []);
        } catch (e) {
            setRecent([]);
        }
    }, []);

    // Mots-clés populaires "Rechercher et Trouver" (API, sans query)
    const loadPopular = useCallback(async () => {
        try {
            const { data } = await api.get("/search/suggestions");
            setPopular(data.suggestions ?? []);
        } catch (e) {
            setPopular([]);
        }
    }, []);

    useEffect(() => {
        loadRecent();
        loadPopular();
    }, [loadRecent, loadPopular]);

    // Suggestions en temps réel (mêmes données que la recherche web)
    useEffect(() => {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        const q = query.trim();
        if (q.length < 1) {
            setSuggestions([]);
            return;
        }
        debounceRef.current = setTimeout(async () => {
            try {
                const { data } = await api.get("/search/suggestions", {
                    params: { q },
                });
                setSuggestions(data.suggestions ?? []);
            } catch (e) {
                setSuggestions([]);
            }
        }, 300);
        return () => clearTimeout(debounceRef.current);
    }, [query]);

    const persistRecent = async (list) => {
        setRecent(list);
        try {
            await SecureStore.setItemAsync(RECENT_KEY, JSON.stringify(list));
        } catch (e) {
            /* ignore */
        }
    };

    const runSearch = async (term) => {
        const q = (term ?? query).trim();
        if (!q) return;
        Keyboard.dismiss();
        const next = [q, ...recent.filter((r) => r.toLowerCase() !== q.toLowerCase())].slice(
            0,
            MAX_RECENT,
        );
        await persistRecent(next);
        navigation.navigate("ProductList", { title: q, search: q });
    };

    // Tap sur une suggestion : produit → détail, catégorie → liste catégorie, sinon recherche
    const openSuggestion = async (s) => {
        if (s.type === "produit" && s.id) {
            navigation.navigate("ProductDetail", { id: s.id, name: s.title });
            return;
        }
        if (s.type === "categorie" && s.id) {
            await persistRecent(
                [s.query, ...recent.filter((r) => r.toLowerCase() !== s.query.toLowerCase())].slice(0, MAX_RECENT),
            );
            navigation.navigate("ProductList", {
                title: s.title,
                categoryId: s.id,
            });
            return;
        }
        runSearch(s.query || s.title);
    };

    const clearRecent = async () => {
        await persistRecent([]);
    };

    const removeRecent = async (term) => {
        await persistRecent(recent.filter((r) => r !== term));
    };

    const showSuggestions = query.trim().length > 0;

    return (
        <View style={[styles.container, { paddingTop: insets.top }]}>
            {/* Barre de recherche */}
            <View style={styles.searchRow}>
                <TouchableOpacity
                    onPress={() => navigation.goBack()}
                    style={styles.backBtn}
                    hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
                >
                    <Ionicons name="chevron-back" size={26} color={COLORS.text} />
                </TouchableOpacity>

                <View style={styles.inputWrap}>
                    <TextInput
                        style={styles.input}
                        placeholder="Rechercher un produit..."
                        placeholderTextColor="#9ca3af"
                        value={query}
                        onChangeText={setQuery}
                        autoFocus={route.params?.focusSearch !== false}
                        returnKeyType="search"
                        onSubmitEditing={() => runSearch()}
                    />
                    {query.length > 0 ? (
                        <TouchableOpacity onPress={() => setQuery("")}>
                            <Ionicons name="close-circle" size={18} color="#9ca3af" />
                        </TouchableOpacity>
                    ) : (
                        <Ionicons name="camera-outline" size={20} color="#9ca3af" />
                    )}
                </View>

                <TouchableOpacity style={styles.searchBtn} onPress={() => runSearch()}>
                    <Ionicons name="search" size={20} color="#fff" />
                </TouchableOpacity>
            </View>

            <ScrollView
                keyboardShouldPersistTaps="handled"
                showsVerticalScrollIndicator={false}
                contentContainerStyle={{ paddingBottom: 24 }}
            >
                {showSuggestions ? (
                    /* Suggestions en temps réel (mêmes données que la recherche web) */
                    <View style={styles.suggestList}>
                        {suggestions.map((s, i) => (
                            <TouchableOpacity
                                key={`${s.type}-${s.id ?? s.title}-${i}`}
                                style={styles.suggestRow}
                                onPress={() => openSuggestion(s)}
                                activeOpacity={0.7}
                            >
                                {s.type === "produit" && s.image ? (
                                    <Image source={{ uri: s.image }} style={styles.suggestImg} />
                                ) : (
                                    <View style={styles.suggestIcon}>
                                        <Ionicons
                                            name={TYPE_ICON[s.type] || "search-outline"}
                                            size={18}
                                            color={COLORS.primaryDark}
                                        />
                                    </View>
                                )}
                                <View style={{ flex: 1 }}>
                                    <Text style={styles.suggestTitle} numberOfLines={1}>
                                        {s.title}
                                    </Text>
                                    <Text style={styles.suggestType}>
                                        {TYPE_LABEL[s.type] || ""}
                                        {s.count != null ? ` · ${s.count} article${s.count > 1 ? "s" : ""}` : ""}
                                    </Text>
                                </View>
                                <Ionicons name="arrow-up-outline" size={16} color="#c4c4c4" style={{ transform: [{ rotate: "45deg" }] }} />
                            </TouchableOpacity>
                        ))}
                    </View>
                ) : (
                    <>
                        {/* Dernière recherche */}
                        {recent.length > 0 && (
                            <View style={styles.section}>
                                <View style={styles.sectionHead}>
                                    <Text style={styles.sectionTitle}>Dernière recherche</Text>
                                    <TouchableOpacity onPress={clearRecent} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
                                        <Ionicons name="trash-outline" size={20} color={COLORS.textLight} />
                                    </TouchableOpacity>
                                </View>
                                <View style={styles.chipsWrap}>
                                    {recent.map((term) => (
                                        <TouchableOpacity
                                            key={term}
                                            style={styles.chip}
                                            onPress={() => runSearch(term)}
                                            onLongPress={() => removeRecent(term)}
                                            activeOpacity={0.7}
                                        >
                                            <Text style={styles.chipText} numberOfLines={1}>
                                                {term}
                                            </Text>
                                        </TouchableOpacity>
                                    ))}
                                </View>
                            </View>
                        )}

                        {/* Rechercher et Trouver */}
                        {popular.length > 0 && (
                            <View style={styles.section}>
                                <View style={styles.sectionHead}>
                                    <Text style={styles.sectionTitle}>Rechercher et Trouver</Text>
                                </View>
                                <View style={styles.chipsWrap}>
                                    {popular.map((s, i) => (
                                        <TouchableOpacity
                                            key={`pop-${s.type}-${s.id ?? s.title}-${i}`}
                                            style={styles.chip}
                                            onPress={() => openSuggestion(s)}
                                            activeOpacity={0.7}
                                        >
                                            <Text style={styles.chipText} numberOfLines={1}>
                                                {s.title}
                                            </Text>
                                        </TouchableOpacity>
                                    ))}
                                </View>
                            </View>
                        )}
                    </>
                )}
            </ScrollView>
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: "#fff" },
    searchRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 10,
        paddingHorizontal: 12,
        paddingVertical: 10,
    },
    backBtn: { padding: 2 },
    inputWrap: {
        flex: 1,
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#fff",
        borderRadius: RADIUS.pill,
        borderWidth: 1.5,
        borderColor: COLORS.primaryDark,
        paddingHorizontal: 16,
        height: 42,
    },
    input: { flex: 1, fontSize: 15, color: COLORS.text, paddingVertical: 0 },
    searchBtn: {
        width: 46,
        height: 42,
        borderRadius: RADIUS.pill,
        backgroundColor: "#111827",
        alignItems: "center",
        justifyContent: "center",
    },
    section: { paddingHorizontal: 16, marginTop: 20 },
    sectionHead: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginBottom: 14,
    },
    sectionTitle: { fontSize: 16, fontWeight: "800", color: COLORS.text },
    chipsWrap: {
        flexDirection: "row",
        flexWrap: "wrap",
        gap: 10,
    },
    chip: {
        backgroundColor: "#f3f4f6",
        borderRadius: RADIUS.sm,
        paddingHorizontal: 14,
        paddingVertical: 9,
        maxWidth: "100%",
    },
    chipText: { color: COLORS.text, fontSize: 13.5 },
    // Suggestions en temps réel
    suggestList: { paddingTop: 6 },
    suggestRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 12,
        paddingHorizontal: 16,
        paddingVertical: 11,
        borderBottomWidth: 1,
        borderBottomColor: "#f3f4f6",
    },
    suggestImg: {
        width: 40,
        height: 40,
        borderRadius: 8,
        backgroundColor: "#e5e7eb",
    },
    suggestIcon: {
        width: 40,
        height: 40,
        borderRadius: 20,
        backgroundColor: COLORS.soft,
        alignItems: "center",
        justifyContent: "center",
    },
    suggestTitle: { fontSize: 14.5, color: COLORS.text, fontWeight: "500" },
    suggestType: { fontSize: 11.5, color: COLORS.textLight, marginTop: 2 },
});
