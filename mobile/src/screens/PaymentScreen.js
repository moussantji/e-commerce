import React, { useCallback, useEffect, useState } from "react";
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
    ActivityIndicator,
    Alert,
    Image,
    KeyboardAvoidingView,
    Platform,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

export default function PaymentScreen({ route, navigation }) {
    const insets = useSafeAreaInsets();
    const { orderId, numero, total } = route.params || {};

    const [methods, setMethods] = useState([]);
    const [balance, setBalance] = useState(0);
    const [loading, setLoading] = useState(true);
    const [selected, setSelected] = useState(null);
    const [phone, setPhone] = useState("");
    const [reference, setReference] = useState("");
    const [paying, setPaying] = useState(false);

    const load = useCallback(async () => {
        // Solde du portefeuille (permet de proposer le paiement direct si rechargé)
        let walletBalance = 0;
        try {
            const { data: w } = await api.get("/wallet");
            walletBalance = Number(w.balance ?? 0);
            setBalance(walletBalance);
        } catch (e) {
            /* invité ou erreur : pas de portefeuille */
        }

        try {
            const { data } = await api.get("/payment-methods");
            const serverMethods = data.data ?? [];

            // Le portefeuille n'apparaît QUE si le solde est rechargé (> 0),
            // et suffisant pour couvrir le montant de la commande.
            const list = [...serverMethods];
            if (walletBalance > 0) {
                list.unshift({
                    id: "wallet",
                    name: "Mon portefeuille",
                    provider: `Solde : ${formatPrice(walletBalance)}`,
                    wallet: true,
                });
            }

            setMethods(list);
            if (list.length) setSelected(list[0]);
        } catch (e) {
            setMethods([]);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    const markPaid = async () => {
        if (!selected) {
            Alert.alert("Méthode requise", "Choisissez une méthode de paiement.");
            return;
        }
        setPaying(true);
        try {
            // === Paiement direct avec le portefeuille ===
            if (selected.wallet) {
                if (Number(balance) < Number(total ?? 0)) {
                    Alert.alert(
                        "Solde insuffisant",
                        "Votre solde ne couvre pas le montant. Rechargez votre portefeuille.",
                    );
                    setPaying(false);
                    return;
                }
                await api.post(`/orders/${orderId}/pay`, { wallet: true });
                Alert.alert(
                    "Commande payée ✅",
                    "Votre commande a été réglée avec votre portefeuille.",
                    [
                        {
                            text: "Voir ma commande",
                            onPress: () => navigation.navigate("OrderDetail", { id: orderId }),
                        },
                    ],
                );
                setPaying(false);
                return;
            }

            if (!selected.cod && !phone.trim()) {
                Alert.alert("Numéro requis", "Renseignez le numéro utilisé pour le paiement.");
                setPaying(false);
                return;
            }
            await api.post(`/orders/${orderId}/pay`, {
                payment_method_id: selected.id,
                provider: selected.name,
                phone: phone.trim() || undefined,
                transaction_id: reference.trim() || undefined,
            });
            const codMsg = selected.cod
                ? "Commande confirmée. Vous paierez à la livraison."
                : "Votre paiement est en attente de confirmation par le vendeur. Vous recevrez une notification.";
            Alert.alert(
                selected.cod ? "Commande confirmée ✅" : "Paiement déclaré ✅",
                codMsg,
                [
                    {
                        text: "Voir ma commande",
                        onPress: () =>
                            navigation.navigate("OrderDetail", { id: orderId }),
                    },
                ],
            );
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setPaying(false);
        }
    };

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
                <Text style={styles.headerTitle}>Paiement</Text>
                <View style={{ width: 26 }} />
            </LinearGradient>

            <KeyboardAvoidingView
                style={{ flex: 1 }}
                behavior={Platform.OS === "ios" ? "padding" : undefined}
            >
                <ScrollView contentContainerStyle={{ padding: 16, paddingBottom: 40 }} keyboardShouldPersistTaps="handled">
                    {/* Récap montant */}
                    <View style={styles.amountCard}>
                        <Text style={styles.amountLabel}>Montant à payer</Text>
                        <Text style={styles.amount}>{formatPrice(total ?? 0)}</Text>
                        {numero ? <Text style={styles.numero}>Commande {numero}</Text> : null}
                    </View>

                    {loading ? (
                        <ActivityIndicator style={{ marginTop: 30 }} color={COLORS.primary} />
                    ) : methods.length === 0 ? (
                        <Text style={styles.empty}>Aucune méthode de paiement disponible.</Text>
                    ) : (
                        <>
                            <Text style={styles.sectionTitle}>Choisissez une méthode</Text>
                            {methods.map((m) => {
                                const on = selected?.id === m.id;
                                return (
                                    <TouchableOpacity
                                        key={m.id}
                                        style={[styles.method, on && styles.methodOn]}
                                        onPress={() => setSelected(m)}
                                        activeOpacity={0.8}
                                    >
                                        {m.logo ? (
                                            <Image source={{ uri: m.logo }} style={styles.methodLogo} />
                                        ) : (
                                            <View style={styles.methodIcon}>
                                                <Ionicons
                                                    name={m.wallet ? "wallet-outline" : "phone-portrait-outline"}
                                                    size={20}
                                                    color={COLORS.primaryDark}
                                                />
                                            </View>
                                        )}
                                        <View style={{ flex: 1 }}>
                                            <Text style={styles.methodName}>{m.name}</Text>
                                            {m.provider ? <Text style={styles.methodProvider}>{m.provider}</Text> : null}
                                        </View>
                                        <Ionicons
                                            name={on ? "radio-button-on" : "radio-button-off"}
                                            size={22}
                                            color={on ? COLORS.primaryDark : "#cbd5e1"}
                                        />
                                    </TouchableOpacity>
                                );
                            })}

                            {/* Instructions de la méthode choisie */}
                            {selected ? (
                                <View style={styles.instructionsCard}>
                                    {selected.wallet ? (
                                        <>
                                            <View style={styles.accountRow}>
                                                <Text style={styles.accountLabel}>Solde disponible</Text>
                                                <Text style={styles.accountNumber}>{formatPrice(balance)}</Text>
                                            </View>
                                            <Text style={styles.instructionsTitle}>Paiement par portefeuille</Text>
                                            <Text style={styles.instructionsText}>
                                                {Number(balance) >= Number(total ?? 0)
                                                    ? "Le montant sera débité immédiatement de votre portefeuille et votre commande sera confirmée."
                                                    : "Votre solde est insuffisant pour régler cette commande. Rechargez votre portefeuille."}
                                            </Text>
                                        </>
                                    ) : (
                                        <>
                                            {!selected.cod && selected.account_number ? (
                                                <View style={styles.accountRow}>
                                                    <Text style={styles.accountLabel}>Numéro à créditer</Text>
                                                    <Text style={styles.accountNumber}>{selected.account_number}</Text>
                                                </View>
                                            ) : null}
                                            <Text style={styles.instructionsTitle}>
                                                {selected.cod ? "Paiement à la livraison" : "Instructions"}
                                            </Text>
                                            <Text style={styles.instructionsText}>
                                                {selected.instructions ||
                                                    (selected.cod
                                                        ? "Vous réglez en espèces à la réception."
                                                        : "Envoyez le montant exact puis marquez comme payé.")}
                                            </Text>
                                        </>
                                    )}
                                </View>
                            ) : null}

                            {/* Champs de confirmation (mobile money uniquement) */}
                            {selected && !selected.cod && !selected.wallet ? (
                                <>
                                    <Text style={styles.sectionTitle}>Confirmez votre paiement</Text>
                                    <View style={styles.field}>
                                        <Ionicons name="call-outline" size={18} color={COLORS.textLight} />
                                        <TextInput
                                            style={styles.input}
                                            placeholder="Numéro utilisé (obligatoire)"
                                            placeholderTextColor="#9ca3af"
                                            keyboardType="phone-pad"
                                            value={phone}
                                            onChangeText={setPhone}
                                        />
                                    </View>
                                    <View style={styles.field}>
                                        <Ionicons name="receipt-outline" size={18} color={COLORS.textLight} />
                                        <TextInput
                                            style={styles.input}
                                            placeholder="Référence de transaction (optionnel)"
                                            placeholderTextColor="#9ca3af"
                                            value={reference}
                                            onChangeText={setReference}
                                        />
                                    </View>
                                </>
                            ) : null}
                        </>
                    )}
                </ScrollView>
            </KeyboardAvoidingView>

            {!loading && methods.length > 0 && (
                <View style={[styles.footer, { paddingBottom: insets.bottom + 12 }]}>
                    <TouchableOpacity
                        style={[styles.payBtn, paying && { opacity: 0.6 }]}
                        onPress={markPaid}
                        disabled={paying}
                    >
                        {paying ? (
                            <ActivityIndicator color="#fff" />
                        ) : (
                            <Text style={styles.payText}>
                                {selected?.wallet
                                    ? "Payer avec mon portefeuille"
                                    : selected?.cod
                                    ? "Confirmer la commande"
                                    : "J'ai payé"}
                            </Text>
                        )}
                    </TouchableOpacity>
                </View>
            )}
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
    amountCard: {
        backgroundColor: "#fff",
        borderRadius: RADIUS.lg,
        padding: 18,
        alignItems: "center",
        elevation: 2,
        shadowColor: "#000",
        shadowOpacity: 0.06,
        shadowRadius: 6,
        shadowOffset: { width: 0, height: 2 },
    },
    amountLabel: { color: COLORS.textLight, fontSize: 13 },
    amount: { fontSize: 30, fontWeight: "900", color: COLORS.accent, marginTop: 4 },
    numero: { color: COLORS.textLight, fontSize: 12, marginTop: 4 },
    sectionTitle: { fontSize: 15, fontWeight: "800", color: COLORS.text, marginTop: 22, marginBottom: 10 },
    method: {
        flexDirection: "row",
        alignItems: "center",
        gap: 12,
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 14,
        marginBottom: 10,
        borderWidth: 1.5,
        borderColor: "transparent",
    },
    methodOn: { borderColor: COLORS.primaryDark, backgroundColor: COLORS.soft },
    methodLogo: { width: 40, height: 40, borderRadius: 8, backgroundColor: "#f3f4f6" },
    methodIcon: {
        width: 40,
        height: 40,
        borderRadius: 20,
        backgroundColor: COLORS.soft,
        alignItems: "center",
        justifyContent: "center",
    },
    methodName: { fontSize: 15, fontWeight: "700", color: COLORS.text },
    methodProvider: { fontSize: 12, color: COLORS.textLight, marginTop: 2 },
    instructionsCard: {
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 14,
        marginTop: 4,
        borderLeftWidth: 3,
        borderLeftColor: COLORS.primaryDark,
    },
    accountRow: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingBottom: 10,
        marginBottom: 10,
        borderBottomWidth: 1,
        borderBottomColor: "#f3f4f6",
    },
    accountLabel: { color: COLORS.textLight, fontSize: 13 },
    accountNumber: { fontSize: 16, fontWeight: "900", color: COLORS.primaryDark },
    instructionsTitle: { fontSize: 13, fontWeight: "800", color: COLORS.text, marginBottom: 6 },
    instructionsText: { fontSize: 13.5, color: "#374151", lineHeight: 21 },
    field: {
        flexDirection: "row",
        alignItems: "center",
        gap: 10,
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        paddingHorizontal: 14,
        height: 50,
        marginBottom: 10,
        borderWidth: 1,
        borderColor: "#e5e7eb",
    },
    input: { flex: 1, fontSize: 15, color: COLORS.text },
    empty: { textAlign: "center", color: COLORS.textLight, marginTop: 30 },
    footer: {
        paddingHorizontal: 16,
        paddingTop: 10,
        backgroundColor: "#fff",
        borderTopWidth: 1,
        borderTopColor: "#f0f0f0",
    },
    payBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 16,
        alignItems: "center",
    },
    payText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
