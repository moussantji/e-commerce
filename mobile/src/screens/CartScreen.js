import React, { useEffect } from "react";
import {
    View,
    Text,
    FlatList,
    Image,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { useCart } from "../context/CartContext";
import { apiError } from "../api/client";
import { formatPrice } from "../utils";

export default function CartScreen() {
    const { cart, loading, refresh, update, remove } = useCart();

    useFocusEffect(
        React.useCallback(() => {
            refresh();
        }, [refresh]),
    );

    const changeQty = async (item, delta) => {
        const q = item.quantity + delta;
        if (q < 1) return;
        try {
            await update(item.product_id, q);
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        }
    };

    const renderItem = ({ item }) => (
        <View style={styles.row}>
            <Image source={{ uri: item.image }} style={styles.image} />
            <View style={{ flex: 1 }}>
                <Text style={styles.name} numberOfLines={2}>
                    {item.name}
                </Text>
                <Text style={styles.price}>{formatPrice(item.unit_price)}</Text>
                <View style={styles.qtyRow}>
                    <TouchableOpacity
                        style={styles.qtyBtn}
                        onPress={() => changeQty(item, -1)}
                    >
                        <Text style={styles.qtySign}>−</Text>
                    </TouchableOpacity>
                    <Text style={styles.qty}>{item.quantity}</Text>
                    <TouchableOpacity
                        style={styles.qtyBtn}
                        onPress={() => changeQty(item, 1)}
                    >
                        <Text style={styles.qtySign}>+</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                        style={styles.remove}
                        onPress={() => remove(item.product_id)}
                    >
                        <Text style={styles.removeText}>Retirer</Text>
                    </TouchableOpacity>
                </View>
            </View>
            <Text style={styles.lineTotal}>{formatPrice(item.line_total)}</Text>
        </View>
    );

    if (loading && cart.items.length === 0) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color="#FF6A00" />
            </View>
        );
    }

    if (cart.items.length === 0) {
        return (
            <View style={styles.center}>
                <Text style={styles.empty}>🛒 Votre panier est vide</Text>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <FlatList
                data={cart.items}
                keyExtractor={(i) => String(i.product_id)}
                renderItem={renderItem}
                contentContainerStyle={{ padding: 12, gap: 12 }}
            />
            <View style={styles.footer}>
                <View style={styles.totalRow}>
                    <Text style={styles.totalLabel}>
                        Total ({cart.count} articles)
                    </Text>
                    <Text style={styles.totalValue}>
                        {formatPrice(cart.total)}
                    </Text>
                </View>
                <TouchableOpacity
                    style={styles.checkout}
                    onPress={() =>
                        Alert.alert(
                            "Commande",
                            "Le paiement sera ajouté prochainement.",
                        )
                    }
                >
                    <Text style={styles.checkoutText}>Passer la commande</Text>
                </TouchableOpacity>
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: "#f3f4f6" },
    center: { flex: 1, justifyContent: "center", alignItems: "center" },
    empty: { color: "#6b7280", fontSize: 16 },
    row: {
        flexDirection: "row",
        backgroundColor: "#fff",
        borderRadius: 14,
        padding: 10,
        gap: 10,
        elevation: 1,
    },
    image: {
        width: 70,
        height: 70,
        borderRadius: 10,
        backgroundColor: "#e5e7eb",
    },
    name: { fontWeight: "600", color: "#111827" },
    price: { color: "#6b7280", marginTop: 2, fontSize: 12 },
    qtyRow: {
        flexDirection: "row",
        alignItems: "center",
        marginTop: 8,
        gap: 8,
    },
    qtyBtn: {
        width: 30,
        height: 30,
        borderRadius: 8,
        backgroundColor: "#eef2ff",
        justifyContent: "center",
        alignItems: "center",
    },
    qtySign: { fontSize: 18, color: "#FF6A00", fontWeight: "800" },
    qty: { minWidth: 24, textAlign: "center", fontWeight: "700" },
    remove: { marginLeft: 8 },
    removeText: { color: "#dc2626", fontSize: 12, fontWeight: "600" },
    lineTotal: { fontWeight: "800", color: "#111827", alignSelf: "center" },
    footer: {
        padding: 16,
        backgroundColor: "#fff",
        borderTopWidth: 1,
        borderTopColor: "#eee",
    },
    totalRow: {
        flexDirection: "row",
        justifyContent: "space-between",
        marginBottom: 12,
    },
    totalLabel: { color: "#6b7280", fontSize: 15 },
    totalValue: { fontWeight: "900", fontSize: 20, color: "#111827" },
    checkout: {
        backgroundColor: "#22c55e",
        borderRadius: 14,
        paddingVertical: 16,
        alignItems: "center",
    },
    checkoutText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
