import React, { useCallback, useEffect, useState } from "react";
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
    Keyboard,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import * as SecureStore from "expo-secure-store";
import api from "../api/client";
import { COLORS, RADIUS } from "../theme";

const RECENT_KEY = "recent_searches";
const MAX_RECENT = 12;

export default function SearchScreen({ navigation, route }) {
    const insets = useSafeAreaInsets();
    const [query, setQuery] = useState("");
    const [recent, setRecent] = useState([]);
    const [suggestions, setSuggestions] = useState([]);

    // Charge les recherches récentes (stockées localement)
    const loadRecent = useCallback(async () => {
        try {
            const raw = await SecureStore.getItemAsync(RECENT_KEY);
            setRecent(raw ? JSON.parse(raw) : []);
        } catch (e) {
            setRecent([]);
        }
    }, []);

    // Suggestions "Rechercher et Trouver" : catégories + mots-clés populaires
    const loadSuggestions = useCallback(async () => {
        try {
            const { data } = await api.get("/categories");
            const cats = (data.data ?? data ?? [])
                .map((c) => c.name)
                .filter(Boolean);
            setSuggestions(cats.slice(0, 12));
        } catch (e) {
            setSuggestions([]);
        }
    }, []);

    useEffect(() => {
        loadRecent();
        loadSuggestions();
    }, [loadRecent, loadSuggestions]);

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
        // Ajoute en tête, sans doublon, limité à MAX_RECENT
        const next = [q, ...recent.filter((r) => r.toLowerCase() !== q.toLowerCase())].slice(
            0,
            MAX_RECENT,
        );
        await persistRecent(next);
        navigation.navigate("ProductList", { title: q, search: q });
    };

    const clearRecent = async () => {
        await persistRecent([]);
    };

    const removeRecent = async (term) => {
        await persistRecent(recent.filter((r) => r !== term));
    };

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
                {suggestions.length > 0 && (
                    <View style={styles.section}>
                        <View style={styles.sectionHead}>
                            <Text style={styles.sectionTitle}>Rechercher et Trouver</Text>
                        </View>
                        <View style={styles.chipsWrap}>
                            {suggestions.map((term) => (
                                <TouchableOpacity
                                    key={term}
                                    style={styles.chip}
                                    onPress={() => runSearch(term)}
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
});
