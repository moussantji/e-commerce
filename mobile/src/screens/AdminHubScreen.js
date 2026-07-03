import React from "react";
import {
    View,
    Text,
    ScrollView,
    TouchableOpacity,
    StyleSheet,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { COLORS, RADIUS } from "../theme";
import { ADMIN_RESOURCES } from "../config/adminResources";

/**
 * Point d'entrée de l'espace de gestion administrateur : donne accès à la
 * gestion des produits, catégories, caractéristiques, marques, tags, méthodes
 * de livraison, méthodes de paiement, coupons et utilisateurs, ainsi qu'aux
 * commandes et paiements déjà existants.
 */
export default function AdminHubScreen({ navigation }) {
    const insets = useSafeAreaInsets();

    const shortcuts = [
        {
            key: "orders",
            title: "Commandes",
            icon: "receipt-outline",
            onPress: () => navigation.navigate("AdminOrders"),
        },
        {
            key: "payments",
            title: "Paiements",
            icon: "shield-checkmark-outline",
            onPress: () => navigation.navigate("AdminPayments"),
        },
    ];

    return (
        <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
            <LinearGradient
                colors={COLORS.gradient}
                start={COLORS.gradientStart}
                end={COLORS.gradientEnd}
                style={[styles.header, { paddingTop: insets.top + 8 }]}
            >
                <TouchableOpacity onPress={() => navigation.goBack()} hitSlop={10}>
                    <Ionicons name="chevron-back" size={26} color="#fff" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Espace administrateur</Text>
                <View style={{ width: 26 }} />
            </LinearGradient>

            <ScrollView contentContainerStyle={{ padding: 12, paddingBottom: 30 }}>
                <Text style={styles.section}>Suivi</Text>
                <View style={styles.grid}>
                    {shortcuts.map((s) => (
                        <TouchableOpacity
                            key={s.key}
                            style={styles.tile}
                            onPress={s.onPress}
                            activeOpacity={0.85}
                        >
                            <View style={styles.tileIcon}>
                                <Ionicons name={s.icon} size={24} color={COLORS.primaryDark} />
                            </View>
                            <Text style={styles.tileTitle}>{s.title}</Text>
                        </TouchableOpacity>
                    ))}
                </View>

                <Text style={styles.section}>Catalogue &amp; paramètres</Text>
                <View style={styles.grid}>
                    {ADMIN_RESOURCES.map((r) => (
                        <TouchableOpacity
                            key={r.key}
                            style={styles.tile}
                            onPress={() =>
                                navigation.navigate("AdminManage", {
                                    resource: r.key,
                                    title: r.title,
                                })
                            }
                            activeOpacity={0.85}
                        >
                            <View style={styles.tileIcon}>
                                <Ionicons name={r.icon} size={24} color={COLORS.primaryDark} />
                            </View>
                            <Text style={styles.tileTitle}>{r.title}</Text>
                        </TouchableOpacity>
                    ))}
                </View>
            </ScrollView>
        </View>
    );
}

const styles = StyleSheet.create({
    header: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingHorizontal: 12,
        paddingBottom: 14,
    },
    headerTitle: { fontSize: 17, fontWeight: "800", color: "#fff" },
    section: {
        fontSize: 13,
        fontWeight: "800",
        color: COLORS.textLight,
        marginTop: 14,
        marginBottom: 10,
        marginLeft: 4,
        textTransform: "uppercase",
        letterSpacing: 0.4,
    },
    grid: { flexDirection: "row", flexWrap: "wrap", gap: 10 },
    tile: {
        width: "31.5%",
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        paddingVertical: 18,
        paddingHorizontal: 8,
        alignItems: "center",
        gap: 10,
        elevation: 1,
        shadowColor: "#000",
        shadowOpacity: 0.05,
        shadowRadius: 4,
        shadowOffset: { width: 0, height: 2 },
    },
    tileIcon: {
        width: 46,
        height: 46,
        borderRadius: 23,
        backgroundColor: COLORS.soft,
        alignItems: "center",
        justifyContent: "center",
    },
    tileTitle: {
        fontSize: 12,
        fontWeight: "700",
        color: COLORS.text,
        textAlign: "center",
    },
});
