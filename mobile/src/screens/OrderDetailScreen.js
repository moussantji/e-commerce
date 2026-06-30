import React, { useCallback, useState } from "react";
import {
    View,
    Text,
    ScrollView,
    Image,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
    Linking,
    Modal,
    Pressable,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const STATUS_LABELS = {
    en_attente: "En attente de paiement",
    traitement: "En traitement",
    expedie: "Expédiée",
    livre: "Livrée",
    annule: "Commande annulée",
};

const CANCEL_REASONS = [
    "Je n'en veux pas / Mauvais achat / Trop d'articles",
    "C'est trop lent pour livrer les produits",
    "Erreur de spécification / taille / couleur",
    "Mauvaise adresse de livraison sélectionnée",
];

function addrField(a, keys) {
    if (!a) return null;
    for (const k of keys) {
        if (a[k]) return a[k];
    }
    return null;
}

export default function OrderDetailScreen({ route, navigation }) {
    const insets = useSafeAreaInsets();
    const { id } = route.params || {};
    const [order, setOrder] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [cancelOpen, setCancelOpen] = useState(false);

    const cancelOrder = async (reason) => {
        setCancelOpen(false);
        try {
            const { data } = await api.post(`/orders/${id}/cancel`, { reason });
            setOrder(data.data ?? data);
            Alert.alert("Commande annulée", "Votre commande a bien été annulée.");
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        }
    };

    const load = useCallback(async () => {
        setError(null);
        try {
            const { data } = await api.get(`/orders/${id}`);
            setOrder(data.data ?? data);
        } catch (e) {
            setError(apiError(e));
        } finally {
            setLoading(false);
        }
    }, [id]);

    useFocusEffect(
        useCallback(() => {
            load();
        }, [load]),
    );

    const Header = (
        <View style={[styles.header, { paddingTop: insets.top + 8 }]}>
            <TouchableOpacity
                style={styles.hIcon}
                onPress={() => navigation.goBack()}
            >
                <Ionicons name="arrow-back" size={24} color={COLORS.text} />
            </TouchableOpacity>
            <Text style={styles.hTitle}>Détails de la commande</Text>
            <View style={styles.hIcon} />
        </View>
    );

    if (loading) {
        return (
            <View style={styles.container}>
                {Header}
                <View style={styles.center}>
                    <ActivityIndicator size="large" color={COLORS.primary} />
                </View>
            </View>
        );
    }

    if (error || !order) {
        return (
            <View style={styles.container}>
                {Header}
                <View style={styles.center}>
                    <Text style={styles.errorText}>
                        {error || "Commande introuvable"}
                    </Text>
                    <TouchableOpacity style={styles.retry} onPress={load}>
                        <Text style={styles.retryText}>Réessayer</Text>
                    </TouchableOpacity>
                </View>
            </View>
        );
    }

    const a = order.adresse_livraison || {};
    const name = addrField(a, ["nom", "name", "full_name", "destinataire"]);
    const phone = addrField(a, ["telephone", "phone", "tel", "mobile"]);
    const line = addrField(a, ["adresse", "address", "rue", "street"]);
    const city = addrField(a, ["ville", "city"]);
    const region = addrField(a, ["region", "etat", "state"]);
    const country = addrField(a, ["pays", "country"]);
    const fullAddr = [line, city, region, country].filter(Boolean).join(", ");
    const isPending = order.statut === "en_attente";
    const cancelled = order.statut === "annule";

    const openMap = () => {
        const q = encodeURIComponent(fullAddr || city || "");
        if (q) Linking.openURL(`https://www.google.com/maps/search/?api=1&query=${q}`);
    };

    return (
        <View style={styles.container}>
            {Header}
            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={{ paddingBottom: isPending ? 100 : 24 }}
            >
                {/* Avertissement délai de paiement */}
                {isPending && (
                    <View style={styles.warning}>
                        <Ionicons name="alert-circle" size={18} color="#dc2626" />
                        <Text style={styles.warningText}>
                            La commande sera annulée si le paiement n'est pas
                            effectué à temps.
                        </Text>
                    </View>
                )}

                {/* Bandeau statut */}
                <LinearGradient
                    colors={COLORS.gradient}
                    start={COLORS.gradientStart}
                    end={COLORS.gradientEnd}
                    style={styles.statusBanner}
                >
                    <Text style={styles.bannerNum}>
                        Commande n°: {order.numero}
                    </Text>
                    <Text style={styles.bannerStatus}>
                        {STATUS_LABELS[order.statut] || order.statut_label}
                    </Text>
                </LinearGradient>

                {/* Adresse d'envoi */}
                <View style={styles.card}>
                    <Text style={styles.cardTitle}>Adresse d'envoi</Text>
                    {name || phone ? (
                        <View style={styles.addrRow}>
                            <Text style={styles.addrName}>{name || "—"}</Text>
                            {phone ? (
                                <Text style={styles.addrPhone}>{phone}</Text>
                            ) : null}
                        </View>
                    ) : null}
                    <Text style={styles.addrLine}>
                        {fullAddr || "Adresse non renseignée"}
                    </Text>
                </View>

                {/* Type de livraison */}
                <View style={styles.card}>
                    <Text style={styles.cardTitle}>Type de livraison</Text>
                    <Text style={styles.deliveryType}>
                        Livraison à domicile
                    </Text>
                    {fullAddr ? (
                        <Text style={styles.deliveryAddr}>{fullAddr}</Text>
                    ) : null}
                    <View style={styles.deliveryFoot}>
                        <Text style={styles.deliveryFee}>
                            Frais : {formatPrice(order.frais_livraison)}
                        </Text>
                        {fullAddr ? (
                            <TouchableOpacity
                                style={styles.mapBtn}
                                onPress={openMap}
                            >
                                <Ionicons
                                    name="location"
                                    size={15}
                                    color={COLORS.primaryDark}
                                />
                                <Text style={styles.mapText}>Voir la carte</Text>
                            </TouchableOpacity>
                        ) : null}
                    </View>
                </View>

                {/* Articles */}
                <View style={styles.card}>
                    <Text style={styles.cardTitle}>Articles commandés</Text>
                    {(order.items ?? []).map((it, idx) => (
                        <View key={`${order.id}-${idx}`} style={styles.itemRow}>
                            <Image
                                source={{ uri: it.image }}
                                style={styles.itemImg}
                            />
                            <View style={styles.itemInfo}>
                                <Text style={styles.itemName} numberOfLines={2}>
                                    {it.name}
                                </Text>
                                <Text style={styles.logistics}>
                                    Méthodes logistiques : Standard
                                </Text>
                                <Text style={styles.qty}>x{it.quantity}</Text>
                            </View>
                            <Text style={styles.itemPrice}>
                                {formatPrice(it.line_total)}
                            </Text>
                        </View>
                    ))}

                    {/* Récapitulatif */}
                    <View style={styles.summary}>
                        <View style={styles.sumRow}>
                            <Text style={styles.sumLabel}>Sous-total</Text>
                            <Text style={styles.sumVal}>
                                {formatPrice(order.sous_total)}
                            </Text>
                        </View>
                        <View style={styles.sumRow}>
                            <Text style={styles.sumLabel}>Livraison</Text>
                            <Text style={styles.sumVal}>
                                {formatPrice(order.frais_livraison)}
                            </Text>
                        </View>
                        <View style={[styles.sumRow, { marginTop: 4 }]}>
                            <Text style={styles.totalLabel}>Total</Text>
                            <Text style={styles.totalVal}>
                                {formatPrice(order.total)}
                            </Text>
                        </View>
                    </View>
                </View>

                {cancelled && (
                    <Text style={styles.cancelReason}>
                        Commande annulée. Délai de paiement dépassé.
                    </Text>
                )}
            </ScrollView>

            {/* Pied d'action (en attente de paiement) */}
            {isPending && (
                <View style={[styles.footer, { paddingBottom: insets.bottom + 12 }]}>
                    <TouchableOpacity
                        style={styles.cancelBtn}
                        activeOpacity={0.85}
                        onPress={() => setCancelOpen(true)}
                    >
                        <Text style={styles.cancelText}>Annuler</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                        activeOpacity={0.85}
                        style={{ flex: 1 }}
                        onPress={() =>
                            Alert.alert(
                                "Paiement",
                                "Le paiement en ligne sera bientôt disponible.",
                            )
                        }
                    >
                        <LinearGradient
                            colors={COLORS.gradient}
                            start={COLORS.gradientStart}
                            end={COLORS.gradientEnd}
                            style={styles.payBtn}
                        >
                            <Text style={styles.payText}>PAYEZ MAINTENANT</Text>
                        </LinearGradient>
                    </TouchableOpacity>
                </View>
            )}
            {/* Bottom-sheet : raisons d'annulation */}
            <Modal
                visible={cancelOpen}
                transparent
                animationType="slide"
                onRequestClose={() => setCancelOpen(false)}
            >
                <Pressable
                    style={styles.backdrop}
                    onPress={() => setCancelOpen(false)}
                />
                <View style={[styles.sheet, { paddingBottom: insets.bottom + 8 }]}>
                    <Text style={styles.sheetTitle}>
                        Sélectionner la raison d'annulation
                    </Text>
                    {CANCEL_REASONS.map((r) => (
                        <TouchableOpacity
                            key={r}
                            style={styles.reasonRow}
                            activeOpacity={0.7}
                            onPress={() => cancelOrder(r)}
                        >
                            <Text style={styles.reasonText}>{r}</Text>
                        </TouchableOpacity>
                    ))}
                    <TouchableOpacity
                        style={styles.rejectBtn}
                        onPress={() => setCancelOpen(false)}
                        activeOpacity={0.8}
                    >
                        <Text style={styles.rejectText}>REJETER</Text>
                    </TouchableOpacity>
                </View>
            </Modal>
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.bg },
    center: { flex: 1, alignItems: "center", justifyContent: "center", padding: 24 },
    header: {
        flexDirection: "row",
        alignItems: "center",
        paddingHorizontal: 12,
        paddingBottom: 10,
        backgroundColor: "#fff",
        borderBottomWidth: 1,
        borderBottomColor: "#f0f0f0",
    },
    hIcon: { width: 40, height: 32, alignItems: "center", justifyContent: "center" },
    hTitle: { flex: 1, textAlign: "center", fontSize: 18, fontWeight: "800", color: COLORS.text },

    warning: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#fff",
        paddingHorizontal: 16,
        paddingVertical: 12,
    },
    warningText: { flex: 1, color: "#374151", fontSize: 12.5, lineHeight: 17 },

    statusBanner: { paddingHorizontal: 18, paddingVertical: 18 },
    bannerNum: { color: "rgba(255,255,255,0.9)", fontSize: 13 },
    bannerStatus: { color: "#fff", fontSize: 20, fontWeight: "900", marginTop: 4 },

    card: {
        backgroundColor: "#fff",
        borderRadius: 14,
        padding: 16,
        marginHorizontal: 12,
        marginTop: 12,
        elevation: 1,
    },
    cardTitle: { fontSize: 15, fontWeight: "800", color: COLORS.text, marginBottom: 10 },
    addrRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
    addrName: { fontWeight: "700", color: COLORS.text, fontSize: 14 },
    addrPhone: { color: COLORS.text, fontSize: 14 },
    addrLine: { color: COLORS.textLight, fontSize: 13, marginTop: 6, lineHeight: 19 },

    deliveryType: { color: COLORS.text, fontWeight: "600", fontSize: 14 },
    deliveryAddr: { color: COLORS.textLight, fontSize: 13, marginTop: 6 },
    deliveryFoot: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginTop: 12,
    },
    deliveryFee: { color: COLORS.text, fontWeight: "600", fontSize: 13 },
    mapBtn: { flexDirection: "row", alignItems: "center", gap: 4 },
    mapText: { color: COLORS.primaryDark, fontWeight: "700", fontSize: 13 },

    itemRow: { flexDirection: "row", gap: 10, marginBottom: 12 },
    itemImg: { width: 70, height: 70, borderRadius: 8, backgroundColor: "#e5e7eb" },
    itemInfo: { flex: 1 },
    itemName: { fontSize: 13.5, color: COLORS.text, fontWeight: "500", lineHeight: 18 },
    logistics: { fontSize: 11.5, color: "#9ca3af", marginTop: 4 },
    qty: { fontSize: 12.5, color: "#6b7280", marginTop: 4 },
    itemPrice: { fontWeight: "800", color: COLORS.text, fontSize: 14 },

    summary: { borderTopWidth: 1, borderTopColor: "#f3f4f6", paddingTop: 12, marginTop: 4 },
    sumRow: { flexDirection: "row", justifyContent: "space-between", marginBottom: 6 },
    sumLabel: { color: "#6b7280", fontSize: 13 },
    sumVal: { color: COLORS.text, fontSize: 13 },
    totalLabel: { fontWeight: "800", color: COLORS.text, fontSize: 15 },
    totalVal: { fontWeight: "900", color: COLORS.accent, fontSize: 18 },

    cancelReason: {
        color: "#dc2626",
        fontSize: 12.5,
        marginHorizontal: 16,
        marginTop: 12,
    },

    footer: {
        position: "absolute",
        bottom: 0,
        left: 0,
        right: 0,
        flexDirection: "row",
        alignItems: "center",
        gap: 12,
        backgroundColor: "#fff",
        paddingHorizontal: 16,
        paddingTop: 12,
        borderTopWidth: 1,
        borderTopColor: "#eee",
    },
    cancelBtn: {
        borderWidth: 1.5,
        borderColor: COLORS.border,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 26,
        paddingVertical: 12,
    },
    cancelText: { color: COLORS.text, fontWeight: "700", fontSize: 14 },
    payBtn: {
        borderRadius: RADIUS.pill,
        paddingVertical: 14,
        alignItems: "center",
    },
    payText: { color: "#fff", fontWeight: "800", fontSize: 15 },
    errorText: { color: "#b91c1c", textAlign: "center", marginBottom: 16 },
    retry: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 10,
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    retryText: { color: "#fff", fontWeight: "700" },

    backdrop: { flex: 1, backgroundColor: "rgba(0,0,0,0.45)" },
    sheet: {
        backgroundColor: "#fff",
        borderTopLeftRadius: RADIUS.lg,
        borderTopRightRadius: RADIUS.lg,
        paddingTop: 18,
    },
    sheetTitle: {
        textAlign: "center",
        fontSize: 15,
        fontWeight: "700",
        color: COLORS.text,
        marginBottom: 8,
    },
    reasonRow: {
        paddingVertical: 16,
        paddingHorizontal: 20,
        borderTopWidth: 1,
        borderTopColor: "#f3f4f6",
        alignItems: "center",
    },
    reasonText: { color: COLORS.accent, fontSize: 14, fontWeight: "600", textAlign: "center" },
    rejectBtn: {
        marginTop: 6,
        paddingVertical: 16,
        alignItems: "center",
        borderTopWidth: 6,
        borderTopColor: "#f3f4f6",
    },
    rejectText: { color: COLORS.primaryDark, fontWeight: "800", fontSize: 15 },
});
