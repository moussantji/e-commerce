import React from "react";
import { View, Text, TouchableOpacity, StyleSheet } from "react-native";
import { useAuth } from "../context/AuthContext";

export default function ProfileScreen() {
    const { user, logout } = useAuth();

    return (
        <View style={styles.container}>
            <View style={styles.avatar}>
                <Text style={styles.avatarText}>
                    {(user?.name || "?").charAt(0).toUpperCase()}
                </Text>
            </View>
            <Text style={styles.name}>{user?.name}</Text>
            <Text style={styles.email}>{user?.email}</Text>

            <View style={styles.card}>
                <Row label="Rôle" value={user?.role || "—"} />
                <Row label="Ville" value={user?.ville || "—"} />
                <Row label="Pays" value={user?.pays || "—"} />
            </View>

            <TouchableOpacity style={styles.logout} onPress={logout}>
                <Text style={styles.logoutText}>Se déconnecter</Text>
            </TouchableOpacity>
        </View>
    );
}

function Row({ label, value }) {
    return (
        <View style={styles.row}>
            <Text style={styles.rowLabel}>{label}</Text>
            <Text style={styles.rowValue}>{value}</Text>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        alignItems: "center",
        padding: 24,
        backgroundColor: "#f3f4f6",
    },
    avatar: {
        width: 88,
        height: 88,
        borderRadius: 44,
        backgroundColor: "#6366f1",
        justifyContent: "center",
        alignItems: "center",
        marginTop: 24,
    },
    avatarText: { color: "#fff", fontSize: 36, fontWeight: "800" },
    name: { fontSize: 22, fontWeight: "800", marginTop: 12, color: "#111827" },
    email: { color: "#6b7280", marginTop: 2 },
    card: {
        backgroundColor: "#fff",
        borderRadius: 14,
        width: "100%",
        marginTop: 24,
        padding: 6,
    },
    row: {
        flexDirection: "row",
        justifyContent: "space-between",
        padding: 14,
        borderBottomWidth: 1,
        borderBottomColor: "#f3f4f6",
    },
    rowLabel: { color: "#6b7280" },
    rowValue: { fontWeight: "600", color: "#111827" },
    logout: {
        marginTop: "auto",
        backgroundColor: "#fee2e2",
        borderRadius: 14,
        paddingVertical: 16,
        width: "100%",
        alignItems: "center",
    },
    logoutText: { color: "#dc2626", fontWeight: "800", fontSize: 16 },
});
