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
import api from "../api/client";
import { COLORS, RADIUS } from "../theme";

// Notifications de démonstration affichées tant que l'API /notifications
// n'est pas branchée côté Laravel (repli automatique).
const FALLBACK = [
    {
        id: "n1",
        title: "Vente Flash en cours ⚡",
        body: "Jusqu'à -50% sur une sélection. Ça se termine bientôt !",
        time: "Il y a 5 min",
        unread: true,
        icon: "flash-outline",
    },
    {
        id: "n2",
        title: "Votre commande est en route 📦",
        body: "Suivez votre livraison depuis l'onglet Commandes.",
        time: "Il y a 2 h",
        unread: true,
        icon: "cube-outline",
    },
    {
        id: "n3",
        title: "Bienvenue 🎁",
        body: "Profitez de la livraison offerte dès 25 000 FCFA.",
        time: "Hier",
        unread: false,
        icon: "pricetag-outline",
    },
];

function normalize(item) {
    return {
        id: String(item.id),
        title: item.title ?? item.titre ?? "Notification",
        body: item.body ?? item.message ?? item.contenu ?? "",
        time: item.time ?? item.created_at ?? "",
        unread: item.unread ?? !item.read_at,
        icon: item.icon ?? "notifications-outline",
    };
}

export default function NotificationsScreen() {
    const [items, setItems] = useState(FALLBACK);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

    const load = useCallback(async () => {
        try {
            const { data } = await api.get("/notifications");
            const list = data.data ?? data ?? [];
            if (Array.isArray(list)) {
                // L'API a répondu : on affiche les vraies données (même vides).
                setItems(list.map(normalize));
            }
        } catch (e) {
            // endpoint indisponible : on garde le repli de démonstration
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
        <View style={[styles.card, item.unread && styles.cardUnread]}>
            <View style={styles.icon}>
                <Ionicons name={item.icon} size={20} color={COLORS.primaryDark} />
            </View>
            <View style={{ flex: 1 }}>
                <View style={styles.titleRow}>
                    <Text style={styles.title}>{item.title}</Text>
                    {item.unread && <View style={styles.dot} />}
                </View>
                {!!item.body && <Text style={styles.body}>{item.body}</Text>}
                {!!item.time && <Text style={styles.time}>{item.time}</Text>}
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
            data={items}
            keyExtractor={(i) => i.id}
            renderItem={renderItem}
            contentContainerStyle={{ padding: 12, gap: 10 }}
            refreshControl={
                <RefreshControl
                    refreshing={refreshing}
                    onRefresh={() => {
                        setRefreshing(true);
                        load();
                    }}
                    colors={[COLORS.primary]}
                    tintColor={COLORS.primary}
                />
            }
            ListEmptyComponent={
                <View style={styles.empty}>
                    <Ionicons
                        name="notifications-off-outline"
                        size={56}
                        color="#d1d5db"
                    />
                    <Text style={styles.emptyText}>
                        Aucune notification pour le moment
                    </Text>
                </View>
            }
            ListFooterComponent={
                <Text style={styles.footer}>Vous êtes à jour ✨</Text>
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
        gap: 12,
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 14,
        borderWidth: 1,
        borderColor: COLORS.border,
    },
    cardUnread: {
        backgroundColor: COLORS.soft,
        borderColor: COLORS.softBorder,
    },
    icon: {
        width: 40,
        height: 40,
        borderRadius: 20,
        backgroundColor: "#fff",
        alignItems: "center",
        justifyContent: "center",
        borderWidth: 1,
        borderColor: COLORS.softBorder,
    },
    titleRow: { flexDirection: "row", alignItems: "center", gap: 6 },
    title: { fontWeight: "700", color: COLORS.text, fontSize: 14, flexShrink: 1 },
    dot: { width: 8, height: 8, borderRadius: 4, backgroundColor: COLORS.badge },
    body: { color: COLORS.textLight, fontSize: 12.5, marginTop: 3, lineHeight: 18 },
    time: { color: COLORS.muted, fontSize: 11, marginTop: 6 },
    empty: { alignItems: "center", paddingTop: 80, gap: 12 },
    emptyText: { color: COLORS.textLight, fontSize: 15 },
    footer: {
        textAlign: "center",
        color: COLORS.muted,
        marginTop: 16,
        fontSize: 13,
    },
});
