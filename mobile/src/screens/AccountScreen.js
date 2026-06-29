import React from "react";
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useAuth } from "../context/AuthContext";

const ORANGE = "#FF6A00";

export default function AccountScreen({ navigation }) {
    const { user, logout } = useAuth();

    const menu = [
        {
            icon: "receipt-outline",
            label: "Mes commandes",
            onPress: () => navigation.navigate("Commande"),
        },
        {
            icon: "cart-outline",
            label: "Mon panier",
            onPress: () => navigation.navigate("Panier"),
        },
        {
            icon: "grid-outline",
            label: "Catégories",
            onPress: () => navigation.navigate("Catégories"),
        },
        { icon: "location-outline", label: "Mes adresses", onPress: () => {} },
        { icon: "settings-outline", label: "Paramètres", onPress: () => {} },
    ];

    return (
        <ScrollView style={{ flex: 1, backgroundColor: "#f3f4f6" }}>
            <View style={styles.header}>
                <View style={styles.avatar}>
                    <Text style={styles.avatarText}>
                        {(user?.name || "?").charAt(0).toUpperCase()}
                    </Text>
                </View>
                <View style={{ flex: 1 }}>
                    <Text style={styles.name}>{user?.name}</Text>
                    <Text style={styles.email}>{user?.email}</Text>
                    {user?.ville || user?.pays ? (
                        <Text style={styles.location}>
                            <Ionicons
                                name="location-outline"
                                size={12}
                                color="#fff"
                            />{" "}
                            {[user?.ville, user?.pays]
                                .filter(Boolean)
                                .join(", ")}
                        </Text>
                    ) : null}
                </View>
            </View>

            <View style={styles.menu}>
                {menu.map((m, i) => (
                    <TouchableOpacity
                        key={m.label}
                        style={[
                            styles.row,
                            i < menu.length - 1 && styles.rowBorder,
                        ]}
                        onPress={m.onPress}
                    >
                        <Ionicons name={m.icon} size={22} color={ORANGE} />
                        <Text style={styles.rowLabel}>{m.label}</Text>
                        <Ionicons
                            name="chevron-forward"
                            size={18}
                            color="#d1d5db"
                        />
                    </TouchableOpacity>
                ))}
            </View>

            <TouchableOpacity style={styles.logout} onPress={logout}>
                <Ionicons name="log-out-outline" size={20} color="#dc2626" />
                <Text style={styles.logoutText}>Se déconnecter</Text>
            </TouchableOpacity>
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    header: {
        flexDirection: "row",
        alignItems: "center",
        gap: 14,
        backgroundColor: ORANGE,
        padding: 22,
        paddingTop: 28,
    },
    avatar: {
        width: 70,
        height: 70,
        borderRadius: 35,
        backgroundColor: "rgba(255,255,255,0.25)",
        justifyContent: "center",
        alignItems: "center",
    },
    avatarText: { color: "#fff", fontSize: 30, fontWeight: "800" },
    name: { fontSize: 20, fontWeight: "800", color: "#fff" },
    email: { color: "rgba(255,255,255,0.9)", marginTop: 2 },
    location: { color: "#fff", marginTop: 4, fontSize: 12 },
    menu: {
        backgroundColor: "#fff",
        margin: 12,
        borderRadius: 14,
        overflow: "hidden",
    },
    row: { flexDirection: "row", alignItems: "center", gap: 14, padding: 16 },
    rowBorder: { borderBottomWidth: 1, borderBottomColor: "#f3f4f6" },
    rowLabel: { flex: 1, fontSize: 15, color: "#111827", fontWeight: "500" },
    logout: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 8,
        backgroundColor: "#fee2e2",
        margin: 12,
        marginTop: 0,
        borderRadius: 14,
        paddingVertical: 16,
    },
    logoutText: { color: "#dc2626", fontWeight: "800", fontSize: 16 },
});
