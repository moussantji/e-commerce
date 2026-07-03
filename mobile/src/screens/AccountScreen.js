import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    Image,
    ImageBackground,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import { useAuth } from "../context/AuthContext";
import api from "../api/client";
import { COLORS, RADIUS } from "../theme";
import { getRecentlyViewed } from "../recentlyViewed";

// Image de fond de l'en-tête du profil (comme l'accueil)
const ACCOUNT_BG_IMAGE =
    "https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1080&q=80";

const ORDER_STEPS = [
    { key: "en_attente", statuses: ["en_attente"], tab: "en_attente", icon: "wallet-outline", label: "À payer" },
    { key: "verif", statuses: ["paiement_declare"], tab: "verif", icon: "hourglass-outline", label: "En vérification" },
    { key: "prep", statuses: ["payee", "traitement"], tab: "route", icon: "cube-outline", label: "En préparation" },
    { key: "expedie", statuses: ["expedie"], tab: "route", icon: "car-outline", label: "Expédiée" },
    { key: "livre", statuses: ["livre"], tab: "livre", icon: "chatbox-ellipses-outline", label: "À évaluer" },
];

export default function AccountScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const { user, logout } = useAuth();
    const [thumbs, setThumbs] = useState([]);
    const [orders, setOrders] = useState([]);
    const [couponCount, setCouponCount] = useState(0);
    const [balance, setBalance] = useState(0);
    const [recentThumbs, setRecentThumbs] = useState([]);

    const load = useCallback(async () => {
        try {
            const [p, o] = await Promise.all([
                api.get("/products", { params: { per_page: 9 } }),
                api.get("/orders", { params: { per_page: 30 } }),
            ]);
            setThumbs((p.data.data ?? []).map((x) => x.image).filter(Boolean));
            setOrders(o.data.data ?? []);
        } catch (e) {
            /* silencieux */
        }
        try {
            const { data } = await api.get("/coupons");
            setCouponCount(data.count ?? (data.data ?? []).length);
        } catch (e) {
            /* ignore */
        }
        try {
            const { data } = await api.get("/wallet");
            setBalance(data.balance ?? 0);
        } catch (e) {
            /* ignore */
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
            getRecentlyViewed().then((list) =>
                setRecentThumbs(list.map((p) => p.image).filter(Boolean)),
            );
        }, [load]),
    );

    const countByStatus = (statuses) =>
        orders.filter((o) => statuses.includes(o.statut)).length;

    const ThumbRow = ({ from }) => (
        <View style={styles.thumbRow}>
            {thumbs.slice(from, from + 3).map((uri, i) => (
                <Image key={i} source={{ uri }} style={styles.thumb} />
            ))}
        </View>
    );

    const RecentThumbs = () => (
        <View style={styles.thumbRow}>
            {recentThumbs.slice(0, 3).map((uri, i) => (
                <Image key={i} source={{ uri }} style={styles.thumb} />
            ))}
        </View>
    );

    const menu = [
        {
            icon: "heart",
            label: "Liste de souhait",
            right: <ThumbRow from={0} />,
            onPress: () => navigation.navigate("Wishlist"),
        },
        {
            icon: "time-outline",
            label: "Vu récemment",
            right: <RecentThumbs />,
            onPress: () => navigation.navigate("RecentlyViewed"),
        },
        {
            icon: "ribbon-outline",
            label: "Mes bons",
            right: <Text style={styles.rightInfo}>Disponible : {couponCount}</Text>,
            onPress: () => navigation.navigate("Coupons"),
        },
        {
            icon: "wallet-outline",
            label: "Mon portefeuille",
            right: (
                <Text style={styles.rightInfo}>
                    Solde : {Number(balance).toFixed(2)}
                </Text>
            ),
            onPress: () => navigation.navigate("Wallet"),
        },
        {
            icon: "home-outline",
            label: "Gestion des adresses",
            onPress: () => navigation.navigate("Addresses"),
        },
    ];

    if (user?.role === "admin") {
        menu.push({
            icon: "shield-checkmark-outline",
            label: "Espace vendeur (paiements)",
            right: <Text style={styles.rightInfo}>Admin</Text>,
            onPress: () => navigation.navigate("AdminPayments"),
        });
        menu.push({
            icon: "cube-outline",
            label: "Gestion des commandes",
            right: <Text style={styles.rightInfo}>Admin</Text>,
            onPress: () => navigation.navigate("AdminOrders"),
        });
    }

    const initial = (user?.name || "?").charAt(0).toUpperCase();

    return (
        <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
            <ScrollView showsVerticalScrollIndicator={false}>
                {/* En-tête : photo de profil en fond + voile violet */}
                <ImageBackground
                    source={{ uri: user?.avatar || ACCOUNT_BG_IMAGE }}
                    style={[styles.header, { paddingTop: insets.top + 8 }]}
                    resizeMode="cover"
                    blurRadius={user?.avatar ? 2 : 0}
                >
                    <LinearGradient
                        colors={["rgba(102,126,234,0.5)", "rgba(118,75,162,0.8)"]}
                        start={COLORS.gradientStart}
                        end={COLORS.gradientEnd}
                        style={StyleSheet.absoluteFill}
                    />
                    <View style={styles.topRow}>
                        <View style={styles.langBtn}>
                            <Text style={styles.langText}>Français</Text>
                        </View>
                        <View style={{ flex: 1 }} />
                        <TouchableOpacity
                            style={styles.topIcon}
                            onPress={() => navigation.navigate("Settings")}
                        >
                            <Ionicons name="settings-outline" size={22} color="#fff" />
                        </TouchableOpacity>
                        <TouchableOpacity
                            style={styles.topIcon}
                            onPress={() => navigation.navigate("Notifications")}
                        >
                            <Ionicons name="chatbubble-ellipses-outline" size={22} color="#fff" />
                        </TouchableOpacity>
                    </View>

                    {/* Carte profil */}
                    <View style={styles.profileCard}>
                        {user?.avatar ? (
                            <Image source={{ uri: user.avatar }} style={styles.avatar} />
                        ) : (
                            <View style={styles.avatar}>
                                <Text style={styles.avatarText}>{initial}</Text>
                            </View>
                        )}
                        <View style={{ flex: 1 }}>
                            {user ? (
                                <>
                                    <View style={styles.nameRow}>
                                        <Text style={styles.name} numberOfLines={1}>
                                            {user?.name || "Mon compte"}
                                        </Text>
                                        <TouchableOpacity onPress={() => navigation.navigate("EditProfile")}>
                                            <Ionicons name="create-outline" size={18} color={COLORS.textLight} />
                                        </TouchableOpacity>
                                    </View>
                                    <View style={styles.vip}>
                                        <Text style={styles.vipText}>VIP 0</Text>
                                    </View>
                                </>
                            ) : (
                                <>
                                    <Text style={styles.name} numberOfLines={1}>Invité</Text>
                                    <TouchableOpacity
                                        style={styles.loginCta}
                                        onPress={() => navigation.navigate("Login")}
                                    >
                                        <Text style={styles.loginCtaText}>
                                            Se connecter / S'inscrire
                                        </Text>
                                    </TouchableOpacity>
                                </>
                            )}
                        </View>
                    </View>
                </ImageBackground>

                {/* Ma Commande */}
                <View style={styles.block}>
                    <View style={styles.blockHead}>
                        <Text style={styles.blockTitle}>Ma Commande</Text>
                        <TouchableOpacity onPress={() => (user ? navigation.navigate("Orders") : navigation.navigate("Login"))}>
                            <Text style={styles.seeAll}>Voir tout</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={styles.stepsRow}>
                        {ORDER_STEPS.map((s) => {
                            const count = countByStatus(s.statuses);
                            return (
                                <TouchableOpacity
                                    key={s.key}
                                    style={styles.step}
                                    onPress={() =>
                                        user
                                            ? navigation.navigate("Orders", { initialTab: s.tab })
                                            : navigation.navigate("Login")
                                    }
                                >
                                    <View style={styles.stepIcon}>
                                        <Ionicons
                                            name={s.icon}
                                            size={26}
                                            color={COLORS.text}
                                        />
                                        {count > 0 && (
                                            <View style={styles.stepBadge}>
                                                <Text style={styles.stepBadgeText}>
                                                    {count}
                                                </Text>
                                            </View>
                                        )}
                                    </View>
                                    <Text style={styles.stepLabel}>{s.label}</Text>
                                </TouchableOpacity>
                            );
                        })}
                    </View>
                </View>

                {/* Menu */}
                <View style={styles.menu}>
                    {menu.map((m, i) => (
                        <TouchableOpacity
                            key={m.label}
                            style={[
                                styles.row,
                                i < menu.length - 1 && styles.rowBorder,
                            ]}
                            onPress={user ? m.onPress : () => navigation.navigate("Login")}
                            activeOpacity={0.7}
                        >
                            <Ionicons name={m.icon} size={22} color={COLORS.text} />
                            <Text style={styles.rowLabel}>{m.label}</Text>
                            {m.right || null}
                            <Ionicons
                                name="chevron-forward"
                                size={18}
                                color="#d1d5db"
                                style={{ marginLeft: 6 }}
                            />
                        </TouchableOpacity>
                    ))}
                </View>

                {user ? (
                    <TouchableOpacity style={styles.logout} onPress={logout}>
                        <Ionicons name="log-out-outline" size={20} color="#dc2626" />
                        <Text style={styles.logoutText}>Se déconnecter</Text>
                    </TouchableOpacity>
                ) : (
                    <TouchableOpacity
                        style={styles.logout}
                        onPress={() => navigation.navigate("Login")}
                    >
                        <Ionicons name="log-in-outline" size={20} color={COLORS.primaryDark} />
                        <Text style={[styles.logoutText, { color: COLORS.primaryDark }]}>
                            Se connecter
                        </Text>
                    </TouchableOpacity>
                )}

                <View style={{ height: 16 }} />
            </ScrollView>
        </View>
    );
}

const styles = StyleSheet.create({
    header: { paddingHorizontal: 12, paddingBottom: 44, minHeight: 200, justifyContent: "flex-start" },
    topRow: { flexDirection: "row", alignItems: "center", gap: 8 },
    langBtn: {
        flexDirection: "row",
        alignItems: "center",
        paddingHorizontal: 10,
        paddingVertical: 5,
    },
    langText: { color: "#fff", fontWeight: "700", fontSize: 14 },
    topIcon: { padding: 6 },

    profileCard: {
        flexDirection: "row",
        alignItems: "center",
        gap: 14,
        backgroundColor: "#fff",
        borderRadius: RADIUS.lg,
        padding: 16,
        marginTop: 12,
        marginBottom: -32,
        elevation: 3,
        shadowColor: "#000",
        shadowOpacity: 0.08,
        shadowRadius: 8,
        shadowOffset: { width: 0, height: 3 },
    },
    avatar: {
        width: 60,
        height: 60,
        borderRadius: 30,
        backgroundColor: COLORS.primaryDark,
        justifyContent: "center",
        alignItems: "center",
    },
    avatarText: { color: "#fff", fontSize: 26, fontWeight: "800" },
    nameRow: { flexDirection: "row", alignItems: "center", gap: 8 },
    name: { fontSize: 20, fontWeight: "900", color: COLORS.text, flexShrink: 1 },
    vip: {
        alignSelf: "flex-start",
        borderWidth: 1,
        borderColor: COLORS.softBorder,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 10,
        paddingVertical: 2,
        marginTop: 6,
    },
    vipText: { color: COLORS.primaryDark, fontWeight: "700", fontSize: 11 },
    loginCta: {
        alignSelf: "flex-start",
        backgroundColor: COLORS.primaryDark,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 14,
        paddingVertical: 6,
        marginTop: 6,
    },
    loginCtaText: { color: "#fff", fontWeight: "800", fontSize: 12 },

    block: {
        backgroundColor: "#fff",
        marginTop: 44,
        paddingHorizontal: 16,
        paddingVertical: 16,
    },
    blockHead: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: 16,
    },
    blockTitle: { fontSize: 16, fontWeight: "800", color: COLORS.text },
    seeAll: { color: COLORS.primaryDark, fontWeight: "700", fontSize: 13 },
    stepsRow: { flexDirection: "row", justifyContent: "space-between" },
    step: { flex: 1, alignItems: "center", gap: 8 },
    stepIcon: { width: 34, height: 30, alignItems: "center", justifyContent: "center" },
    stepBadge: {
        position: "absolute",
        top: -6,
        right: -8,
        minWidth: 18,
        height: 18,
        paddingHorizontal: 4,
        borderRadius: 9,
        backgroundColor: COLORS.accent,
        alignItems: "center",
        justifyContent: "center",
    },
    stepBadgeText: { color: "#fff", fontSize: 10, fontWeight: "800" },
    stepLabel: {
        fontSize: 11,
        color: COLORS.textLight,
        textAlign: "center",
        lineHeight: 14,
    },

    menu: { backgroundColor: "#fff", marginTop: 10 },
    row: { flexDirection: "row", alignItems: "center", gap: 14, paddingHorizontal: 16, paddingVertical: 15 },
    rowBorder: { borderBottomWidth: 1, borderBottomColor: "#f3f4f6" },
    rowLabel: { flex: 1, fontSize: 15, color: COLORS.text, fontWeight: "500" },
    rightInfo: { color: COLORS.primaryDark, fontSize: 13, fontWeight: "600" },
    thumbRow: { flexDirection: "row", gap: 6 },
    thumb: { width: 34, height: 34, borderRadius: 6, backgroundColor: "#e5e7eb" },

    logout: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 8,
        backgroundColor: "#fff",
        marginTop: 10,
        paddingVertical: 16,
    },
    logoutText: { color: "#dc2626", fontWeight: "800", fontSize: 15 },
});
