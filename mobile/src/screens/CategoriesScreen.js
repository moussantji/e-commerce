import React, { useCallback, useEffect, useState } from "react";
import {
    View,
    Text,
    FlatList,
    Image,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    RefreshControl,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import api, { apiError } from "../api/client";
import { COLORS } from "../theme";

const ORANGE = COLORS.primaryDark;

export default function CategoriesScreen({ navigation }) {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [error, setError] = useState(null);

    const load = useCallback(async () => {
        setError(null);
        try {
            const { data } = await api.get("/categories");
            setCategories(data.data ?? []);
        } catch (e) {
            setError(apiError(e));
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    const renderItem = ({ item }) => (
        <TouchableOpacity
            style={styles.card}
            onPress={() =>
                navigation.navigate("ProductList", {
                    categoryId: item.id,
                    title: item.name,
                })
            }
        >
            <View style={styles.iconWrap}>
                {item.image ? (
                    <Image source={{ uri: item.image }} style={styles.image} />
                ) : (
                    <Ionicons name="cube-outline" size={30} color={ORANGE} />
                )}
            </View>
            <Text style={styles.name} numberOfLines={2}>
                {item.name}
            </Text>
            <Text style={styles.count}>
                {item.products_count} produit
                {item.products_count > 1 ? "s" : ""}
            </Text>
        </TouchableOpacity>
    );

    if (loading)
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={ORANGE} />
            </View>
        );

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
        <FlatList
            style={{ backgroundColor: "#f3f4f6" }}
            data={categories}
            keyExtractor={(i) => String(i.id)}
            renderItem={renderItem}
            numColumns={3}
            columnWrapperStyle={{ gap: 12, paddingHorizontal: 12 }}
            contentContainerStyle={{ gap: 12, paddingVertical: 12 }}
            refreshControl={
                <RefreshControl
                    refreshing={refreshing}
                    onRefresh={() => {
                        setRefreshing(true);
                        load();
                    }}
                    colors={[ORANGE]}
                />
            }
            ListEmptyComponent={
                <Text style={styles.empty}>Aucune catégorie.</Text>
            }
        />
    );
}

const styles = StyleSheet.create({
    center: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        padding: 24,
        backgroundColor: "#f3f4f6",
    },
    card: {
        flex: 1,
        backgroundColor: "#fff",
        borderRadius: 14,
        padding: 12,
        alignItems: "center",
        elevation: 1,
    },
    iconWrap: {
        width: 60,
        height: 60,
        borderRadius: 30,
        backgroundColor: COLORS.soft,
        justifyContent: "center",
        alignItems: "center",
        overflow: "hidden",
    },
    image: { width: 60, height: 60 },
    name: {
        fontSize: 12,
        fontWeight: "600",
        color: "#111827",
        textAlign: "center",
        marginTop: 8,
        minHeight: 30,
    },
    count: { fontSize: 10, color: "#9ca3af" },
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
