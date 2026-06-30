import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    FlatList,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    RefreshControl,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";
import { COLORS } from "../theme";

const ORANGE = COLORS.primaryDark;

const STATUS_COLORS = {
    en_attente: "#f59e0b",
    traitement: "#3b82f6",
    expedie: "#8b5cf6",
    livre: "#22c55e",
    annule: "#ef4444",
};

export default function OrdersScreen() {
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [error, setError] = useState(null);

    const load = useCallback(async () => {
        setError(null);
        try {
            const { data } = await api.get("/orders", {
                params: { per_page: 30 },
            });
            setOrders(data.data ?? []);
        } catch (e) {
            setError(apiError(e));
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const renderItem = ({ item }) => (
        <View style={styles.card}>
            <View style={styles.cardHead}>
                <Text style={styles.numero}>Commande {item.numero}</Text>
                <View
                    style={[
                        styles.statusPill,
                        {
                            backgroundColor:
                                (STATUS_COLORS[item.statut] || "#6b7280") +
                                "22",
                        },
                    ]}
                >
                    <Text
                        style={[
                            styles.statusText,
                            { color: STATUS_COLORS[item.statut] || "#6b7280" },
                        ]}
                    >
                        {item.statut_label}
                    </Text>
                </View>
            </View>
            <View style={styles.cardRow}>
                <Ionicons name="calendar-outline" size={15} color="#9ca3af" />
                <Text style={styles.meta}>{item.date}</Text>
                <Ionicons
                    name="cube-outline"
                    size={15}
                    color="#9ca3af"
                    style={{ marginLeft: 12 }}
                />
                <Text style={styles.meta}>
                    {item.items_count ?? 0} article(s)
                </Text>
            </View>
            <View style={styles.cardFoot}>
                <Text style={styles.totalLabel}>Total</Text>
                <Text style={styles.total}>{formatPrice(item.total)}</Text>
            </View>
        </View>
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

    if (orders.length === 0) {
        return (
            <View style={styles.center}>
                <Ionicons name="receipt-outline" size={64} color="#d1d5db" />
                <Text style={styles.empty}>Aucune commande pour le moment</Text>
            </View>
        );
    }

    return (
        <FlatList
            style={{ backgroundColor: "#f3f4f6" }}
            data={orders}
            keyExtractor={(i) => String(i.id)}
            renderItem={renderItem}
            contentContainerStyle={{ padding: 12, gap: 12 }}
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
        backgroundColor: "#fff",
        borderRadius: 14,
        padding: 14,
        elevation: 1,
    },
    cardHead: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
    },
    numero: { fontWeight: "700", color: "#111827", flex: 1, marginRight: 8 },
    statusPill: { borderRadius: 20, paddingHorizontal: 10, paddingVertical: 4 },
    statusText: { fontSize: 12, fontWeight: "700" },
    cardRow: {
        flexDirection: "row",
        alignItems: "center",
        marginTop: 10,
        gap: 4,
    },
    meta: { color: "#6b7280", fontSize: 13 },
    cardFoot: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        marginTop: 12,
        borderTopWidth: 1,
        borderTopColor: "#f3f4f6",
        paddingTop: 10,
    },
    totalLabel: { color: "#6b7280" },
    total: { color: COLORS.accent, fontWeight: "900", fontSize: 18 },
    empty: { color: "#6b7280", marginTop: 12, fontSize: 15 },
    errorText: { color: "#b91c1c", textAlign: "center", marginBottom: 16 },
    retry: {
        backgroundColor: ORANGE,
        borderRadius: 10,
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    retryText: { color: "#fff", fontWeight: "700" },
});
