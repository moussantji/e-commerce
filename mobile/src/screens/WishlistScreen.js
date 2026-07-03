import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    Image,
    FlatList,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import api from "../api/client";
import { useCart } from "../context/CartContext";
import { useWishlist } from "../context/WishlistContext";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";
import AnimatedPressable from "../components/AnimatedPressable";
import SmartImage from "../components/SmartImage";

export default function WishlistScreen({ navigation }) {
    const { add } = useCart();
    const { refresh: refreshWishlist } = useWishlist();
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);

    const load = useCallback(async () => {
        setLoading(true);
        try {
            const { data } = await api.get("/wishlist");
            setProducts(data.data ?? []);
        } catch (e) {
            setProducts([]);
        } finally {
            setLoading(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const removeFav = async (productId) => {
        setProducts((prev) => prev.filter((p) => p.id !== productId));
        try {
            await api.delete(`/wishlist/${productId}`);
            refreshWishlist(); // garde le contexte (cœurs) synchronisé
        } catch (e) {
            load();
        }
    };

    const renderItem = ({ item, index }) => (
        <AnimatedPressable
            style={styles.card}
            scaleTo={0.97}
            index={index}
            onPress={() => navigation.navigate("ProductDetail", { id: item.id, name: item.name })}
        >
            <SmartImage source={item.image} style={styles.image} />
            <TouchableOpacity style={styles.heart} onPress={() => removeFav(item.id)}>
                <Ionicons name="heart" size={18} color={COLORS.badge} />
            </TouchableOpacity>
            <View style={styles.body}>
                <Text style={styles.name} numberOfLines={2}>
                    {item.name}
                </Text>
                <View style={styles.priceRow}>
                    <Text style={styles.price}>{formatPrice(item.sale_price ?? item.price)}</Text>
                    <TouchableOpacity
                        style={styles.addBtn}
                        onPress={() => add(item.id, 1).then(() => navigation.navigate("Tabs", { screen: "Panier" })).catch(() => {})}
                    >
                        <Ionicons name="cart" size={16} color="#fff" />
                    </TouchableOpacity>
                </View>
            </View>
        </AnimatedPressable>
    );

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    return (
        <FlatList
            style={{ flex: 1, backgroundColor: "#f3f4f6" }}
            data={products}
            keyExtractor={(i) => String(i.id)}
            renderItem={renderItem}
            numColumns={2}
            columnWrapperStyle={{ gap: 10, paddingHorizontal: 10 }}
            contentContainerStyle={{ gap: 10, paddingVertical: 12 }}
            ListEmptyComponent={
                <View style={styles.empty}>
                    <Ionicons name="heart-outline" size={54} color="#d1d5db" />
                    <Text style={styles.emptyText}>Votre liste de souhaits est vide</Text>
                </View>
            }
        />
    );
}

const styles = StyleSheet.create({
    center: { flex: 1, justifyContent: "center", alignItems: "center", backgroundColor: "#f3f4f6" },
    card: { flex: 1, backgroundColor: "#fff", borderRadius: 12, overflow: "hidden" },
    image: { width: "100%", aspectRatio: 1, backgroundColor: "#e5e7eb" },
    heart: {
        position: "absolute",
        top: 8,
        right: 8,
        width: 30,
        height: 30,
        borderRadius: 15,
        backgroundColor: "rgba(255,255,255,0.92)",
        alignItems: "center",
        justifyContent: "center",
    },
    body: { padding: 8 },
    name: { fontSize: 12.5, color: "#1f2937", lineHeight: 16, minHeight: 32 },
    priceRow: { flexDirection: "row", alignItems: "center", marginTop: 6 },
    price: { fontSize: 15, fontWeight: "900", color: COLORS.accent },
    addBtn: {
        marginLeft: "auto",
        width: 30,
        height: 30,
        borderRadius: 15,
        backgroundColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
    },
    empty: { alignItems: "center", paddingTop: 100, gap: 12 },
    emptyText: { color: COLORS.textLight, fontSize: 15 },
});
