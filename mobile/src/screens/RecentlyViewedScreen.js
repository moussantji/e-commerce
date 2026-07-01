import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    Image,
    FlatList,
    TouchableOpacity,
    StyleSheet,
    Alert,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { formatPrice } from "../utils";
import { COLORS } from "../theme";
import { getRecentlyViewed, clearRecentlyViewed } from "../recentlyViewed";

export default function RecentlyViewedScreen({ navigation }) {
    const [items, setItems] = useState([]);

    const load = useCallback(async () => {
        setItems(await getRecentlyViewed());
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const clearAll = () => {
        Alert.alert("Vider l'historique", "Supprimer tous les produits vus récemment ?", [
            { text: "Annuler", style: "cancel" },
            {
                text: "Vider",
                style: "destructive",
                onPress: async () => {
                    await clearRecentlyViewed();
                    setItems([]);
                },
            },
        ]);
    };

    const renderItem = ({ item }) => (
        <TouchableOpacity
            style={styles.card}
            activeOpacity={0.9}
            onPress={() => navigation.navigate("ProductDetail", { id: item.id, name: item.name })}
        >
            <Image source={{ uri: item.image }} style={styles.image} />
            <View style={styles.body}>
                <Text style={styles.name} numberOfLines={2}>
                    {item.name}
                </Text>
                <Text style={styles.price}>{formatPrice(item.sale_price ?? item.price)}</Text>
            </View>
        </TouchableOpacity>
    );

    return (
        <View style={{ flex: 1, backgroundColor: "#f3f4f6" }}>
            {items.length > 0 && (
                <TouchableOpacity style={styles.clearRow} onPress={clearAll}>
                    <Ionicons name="trash-outline" size={16} color={COLORS.textLight} />
                    <Text style={styles.clearText}>Vider l'historique</Text>
                </TouchableOpacity>
            )}
            <FlatList
                data={items}
                keyExtractor={(i) => String(i.id)}
                renderItem={renderItem}
                numColumns={2}
                columnWrapperStyle={{ gap: 10, paddingHorizontal: 10 }}
                contentContainerStyle={{ gap: 10, paddingVertical: 10 }}
                ListEmptyComponent={
                    <View style={styles.empty}>
                        <Ionicons name="time-outline" size={54} color="#d1d5db" />
                        <Text style={styles.emptyText}>Aucun produit consulté récemment</Text>
                    </View>
                }
            />
        </View>
    );
}

const styles = StyleSheet.create({
    clearRow: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "flex-end",
        gap: 5,
        paddingHorizontal: 16,
        paddingVertical: 10,
        backgroundColor: "#fff",
    },
    clearText: { color: COLORS.textLight, fontSize: 13 },
    card: { flex: 1, backgroundColor: "#fff", borderRadius: 12, overflow: "hidden" },
    image: { width: "100%", aspectRatio: 1, backgroundColor: "#e5e7eb" },
    body: { padding: 8 },
    name: { fontSize: 12.5, color: "#1f2937", lineHeight: 16, minHeight: 32 },
    price: { fontSize: 15, fontWeight: "900", color: COLORS.accent, marginTop: 4 },
    empty: { alignItems: "center", paddingTop: 100, gap: 12 },
    emptyText: { color: COLORS.textLight, fontSize: 15 },
});
