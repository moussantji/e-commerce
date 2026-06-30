import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    ScrollView,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import api from "../api/client";
import { COLORS, RADIUS } from "../theme";

export default function WalletScreen() {
    const [wallet, setWallet] = useState({ balance: 0, currency: "FCFA", transactions: [] });
    const [loading, setLoading] = useState(true);

    const load = useCallback(async () => {
        try {
            const { data } = await api.get("/wallet");
            setWallet({
                balance: data.balance ?? 0,
                currency: data.currency ?? "FCFA",
                transactions: data.transactions ?? [],
            });
        } catch (e) {
            /* garde les valeurs par défaut */
        } finally {
            setLoading(false);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    const soon = () => Alert.alert("Bientôt", "Cette fonction sera disponible prochainement.");

    return (
        <ScrollView style={{ flex: 1, backgroundColor: COLORS.bg }}>
            <LinearGradient
                colors={COLORS.gradient}
                start={COLORS.gradientStart}
                end={COLORS.gradientEnd}
                style={styles.card}
            >
                <Text style={styles.label}>Solde du portefeuille</Text>
                <Text style={styles.balance}>
                    {Number(wallet.balance).toLocaleString("fr-FR", {
                        minimumFractionDigits: 2,
                    })}{" "}
                    <Text style={styles.currency}>{wallet.currency}</Text>
                </Text>
                <View style={styles.actions}>
                    <TouchableOpacity style={styles.action} onPress={soon}>
                        <Ionicons name="add-circle-outline" size={20} color="#fff" />
                        <Text style={styles.actionText}>Recharger</Text>
                    </TouchableOpacity>
                    <TouchableOpacity style={styles.action} onPress={soon}>
                        <Ionicons name="swap-horizontal-outline" size={20} color="#fff" />
                        <Text style={styles.actionText}>Transférer</Text>
                    </TouchableOpacity>
                </View>
            </LinearGradient>

            <Text style={styles.sectionTitle}>Transactions</Text>
            {wallet.transactions.length === 0 ? (
                <View style={styles.empty}>
                    <Ionicons name="receipt-outline" size={48} color="#d1d5db" />
                    <Text style={styles.emptyText}>Aucune transaction</Text>
                </View>
            ) : (
                wallet.transactions.map((t, i) => (
                    <View key={i} style={styles.txRow}>
                        <Text style={styles.txLabel}>{t.label}</Text>
                        <Text style={styles.txAmount}>{t.amount}</Text>
                    </View>
                ))
            )}
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    center: { flex: 1, justifyContent: "center", alignItems: "center", backgroundColor: COLORS.bg },
    card: { margin: 12, borderRadius: RADIUS.lg, padding: 22 },
    label: { color: "rgba(255,255,255,0.9)", fontSize: 13 },
    balance: { color: "#fff", fontSize: 34, fontWeight: "900", marginTop: 6 },
    currency: { fontSize: 16, fontWeight: "700" },
    actions: { flexDirection: "row", gap: 24, marginTop: 18 },
    action: { flexDirection: "row", alignItems: "center", gap: 6 },
    actionText: { color: "#fff", fontWeight: "700" },
    sectionTitle: {
        fontSize: 15,
        fontWeight: "800",
        color: COLORS.text,
        marginHorizontal: 16,
        marginTop: 8,
        marginBottom: 8,
    },
    empty: { alignItems: "center", paddingTop: 40, gap: 10 },
    emptyText: { color: COLORS.textLight, fontSize: 14 },
    txRow: {
        flexDirection: "row",
        justifyContent: "space-between",
        backgroundColor: "#fff",
        marginHorizontal: 12,
        marginBottom: 8,
        padding: 14,
        borderRadius: RADIUS.md,
    },
    txLabel: { color: COLORS.text },
    txAmount: { color: COLORS.accent, fontWeight: "800" },
});
