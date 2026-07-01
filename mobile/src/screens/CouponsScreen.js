import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    FlatList,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import api, { apiError } from "../api/client";
import { COLORS, RADIUS } from "../theme";

export default function CouponsScreen() {
    const [coupons, setCoupons] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const load = useCallback(async () => {
        setError(null);
        try {
            const { data } = await api.get("/coupons");
            setCoupons(data.data ?? []);
        } catch (e) {
            setError(apiError(e));
        } finally {
            setLoading(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const renderItem = ({ item }) => (
        <View style={styles.card}>
            <LinearGradient
                colors={COLORS.gradient}
                start={COLORS.gradientStart}
                end={COLORS.gradientEnd}
                style={styles.left}
            >
                <Text style={styles.value}>
                    {item.type === "percentage"
                        ? `-${item.value}%`
                        : `${item.value}`}
                </Text>
                {item.type !== "percentage" && (
                    <Text style={styles.currency}>FCFA</Text>
                )}
            </LinearGradient>
            <View style={styles.right}>
                <Text style={styles.desc}>{item.description}</Text>
                <Text style={styles.code}>Code : {item.code}</Text>
                {item.expires_at ? (
                    <Text style={styles.expire}>
                        Valable jusqu'au {item.expires_at}
                    </Text>
                ) : null}
            </View>
        </View>
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
            style={{ backgroundColor: COLORS.bg }}
            data={coupons}
            keyExtractor={(i) => String(i.id)}
            renderItem={renderItem}
            contentContainerStyle={{ padding: 12, gap: 12 }}
            ListEmptyComponent={
                <View style={styles.empty}>
                    <Ionicons name="ribbon-outline" size={56} color="#d1d5db" />
                    <Text style={styles.emptyText}>
                        {error || "Aucun bon disponible pour le moment"}
                    </Text>
                </View>
            }
        />
    );
}

const styles = StyleSheet.create({
    center: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        backgroundColor: COLORS.bg,
    },
    card: {
        flexDirection: "row",
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        overflow: "hidden",
        elevation: 1,
    },
    left: {
        width: 100,
        alignItems: "center",
        justifyContent: "center",
        paddingVertical: 18,
    },
    value: { color: "#fff", fontSize: 22, fontWeight: "900" },
    currency: { color: "rgba(255,255,255,0.9)", fontSize: 11, fontWeight: "700" },
    right: { flex: 1, padding: 14, justifyContent: "center" },
    desc: { fontSize: 14, fontWeight: "700", color: COLORS.text },
    code: { fontSize: 13, color: COLORS.primaryDark, fontWeight: "700", marginTop: 6 },
    expire: { fontSize: 11.5, color: COLORS.textLight, marginTop: 4 },
    empty: { alignItems: "center", paddingTop: 80, gap: 12 },
    emptyText: { color: COLORS.textLight, fontSize: 14, textAlign: "center", paddingHorizontal: 24 },
});
