import React from "react";
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
    Alert,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { useAuth } from "../context/AuthContext";
import { COLORS, RADIUS } from "../theme";

export default function SettingsScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const { user, logout } = useAuth();

    const soon = (t) => Alert.alert(t, "Bientôt disponible.");

    const confirmLogout = () => {
        Alert.alert("Déconnexion", "Voulez-vous vous déconnecter ?", [
            { text: "Annuler", style: "cancel" },
            { text: "Se déconnecter", style: "destructive", onPress: logout },
        ]);
    };

    const confirmDelete = () => {
        Alert.alert(
            "Supprimer le compte",
            "Cette action est irréversible. Contactez le support pour supprimer votre compte.",
            [{ text: "OK" }],
        );
    };

    // Sections façon KiKUU
    const sections = [
        {
            title: "Compte",
            items: [
                {
                    icon: "person-outline",
                    label: "Modifier le profil",
                    onPress: () => navigation.navigate("EditProfile"),
                },
                {
                    icon: "location-outline",
                    label: "Gestion des adresses",
                    onPress: () => navigation.navigate("Addresses"),
                },
                {
                    icon: "lock-closed-outline",
                    label: "Sécurité du compte",
                    onPress: () => soon("Sécurité du compte"),
                },
            ],
        },
        {
            title: "Préférences",
            items: [
                {
                    icon: "notifications-outline",
                    label: "Notifications",
                    onPress: () => navigation.navigate("Notifications"),
                },
                {
                    icon: "language-outline",
                    label: "Langue",
                    value: "Français",
                    onPress: () => soon("Langue"),
                },
                {
                    icon: "cash-outline",
                    label: "Devise",
                    value: "FCFA",
                    onPress: () => soon("Devise"),
                },
                {
                    icon: "trash-bin-outline",
                    label: "Vider le cache",
                    value: "0 Mo",
                    onPress: () => Alert.alert("Cache vidé", "Le cache a été nettoyé."),
                },
            ],
        },
        {
            title: "À propos",
            items: [
                {
                    icon: "information-circle-outline",
                    label: "À propos de l'application",
                    value: "v1.0.0",
                    onPress: () => soon("À propos"),
                },
                {
                    icon: "document-text-outline",
                    label: "Conditions d'utilisation",
                    onPress: () => soon("Conditions d'utilisation"),
                },
                {
                    icon: "shield-checkmark-outline",
                    label: "Politique de confidentialité",
                    onPress: () => soon("Politique de confidentialité"),
                },
                {
                    icon: "help-circle-outline",
                    label: "Aide & support",
                    onPress: () => soon("Aide & support"),
                },
            ],
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
                <Text style={styles.headerTitle}>Paramètres</Text>
                <View style={{ width: 26 }} />
            </LinearGradient>

            <ScrollView contentContainerStyle={{ paddingVertical: 12, paddingBottom: 30 }}>
                {sections.map((section) => (
                    <View key={section.title} style={styles.section}>
                        <Text style={styles.sectionTitle}>{section.title}</Text>
                        <View style={styles.card}>
                            {section.items.map((item, i) => (
                                <TouchableOpacity
                                    key={item.label}
                                    style={[styles.row, i < section.items.length - 1 && styles.rowBorder]}
                                    onPress={item.onPress}
                                    activeOpacity={0.7}
                                >
                                    <Ionicons name={item.icon} size={20} color={COLORS.text} />
                                    <Text style={styles.rowLabel}>{item.label}</Text>
                                    {item.value ? <Text style={styles.rowValue}>{item.value}</Text> : null}
                                    <Ionicons name="chevron-forward" size={18} color="#d1d5db" />
                                </TouchableOpacity>
                            ))}
                        </View>
                    </View>
                ))}

                {/* Déconnexion + suppression */}
                <TouchableOpacity style={styles.logout} onPress={confirmLogout}>
                    <Ionicons name="log-out-outline" size={20} color="#dc2626" />
                    <Text style={styles.logoutText}>Se déconnecter</Text>
                </TouchableOpacity>

                <TouchableOpacity onPress={confirmDelete}>
                    <Text style={styles.deleteText}>Supprimer mon compte</Text>
                </TouchableOpacity>

                {user?.email ? <Text style={styles.account}>Connecté : {user.email}</Text> : null}
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
    section: { marginTop: 8, marginBottom: 6 },
    sectionTitle: {
        fontSize: 13,
        fontWeight: "700",
        color: COLORS.textLight,
        marginHorizontal: 16,
        marginBottom: 8,
    },
    card: { backgroundColor: "#fff", marginHorizontal: 12, borderRadius: RADIUS.lg },
    row: {
        flexDirection: "row",
        alignItems: "center",
        gap: 14,
        paddingHorizontal: 16,
        paddingVertical: 14,
    },
    rowBorder: { borderBottomWidth: 1, borderBottomColor: "#f3f4f6" },
    rowLabel: { flex: 1, fontSize: 15, color: COLORS.text, fontWeight: "500" },
    rowValue: { color: COLORS.textLight, fontSize: 13, marginRight: 4 },
    logout: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 8,
        backgroundColor: "#fff",
        marginHorizontal: 12,
        marginTop: 16,
        borderRadius: RADIUS.lg,
        paddingVertical: 16,
    },
    logoutText: { color: "#dc2626", fontWeight: "800", fontSize: 15 },
    deleteText: {
        textAlign: "center",
        color: "#9ca3af",
        marginTop: 16,
        fontSize: 13,
        textDecorationLine: "underline",
    },
    account: { textAlign: "center", color: "#9ca3af", marginTop: 16, fontSize: 12 },
});
