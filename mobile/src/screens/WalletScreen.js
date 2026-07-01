import React, { useCallback, useEffect, useState } from "react";
import {
    View,
    Text,
    ScrollView,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
    Modal,
    Pressable,
    KeyboardAvoidingView,
    Platform,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

export default function WalletScreen({ navigation }) {
    const insets = useSafeAreaInsets();
    const [wallet, setWallet] = useState({ balance: 0, currency: "FCFA", transactions: [] });
    const [loading, setLoading] = useState(true);
    const [methods, setMethods] = useState([]);

    // Modals
    const [topupOpen, setTopupOpen] = useState(false);
    const [transferOpen, setTransferOpen] = useState(false);
    const [busy, setBusy] = useState(false);

    // Top-up form
    const [amount, setAmount] = useState("");
    const [method, setMethod] = useState(null);
    const [phone, setPhone] = useState("");
    const [reference, setReference] = useState("");

    // Transfer form
    const [tAmount, setTAmount] = useState("");
    const [recipient, setRecipient] = useState("");
    const [note, setNote] = useState("");

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

    const loadMethods = useCallback(async () => {
        try {
            const { data } = await api.get("/payment-methods");
            setMethods(data.data ?? []);
            if ((data.data ?? []).length) setMethod(data.data[0]);
        } catch (e) {
            setMethods([]);
        }
    }, []);

    useFocusEffect(
        useCallback(() => {
            load();
            loadMethods();
        }, [load, loadMethods]),
    );

    const submitTopup = async () => {
        const amt = parseFloat(amount);
        if (!amt || amt < 100) {
            Alert.alert("Montant invalide", "Le montant minimum est de 100 FCFA.");
            return;
        }
        if (!method) {
            Alert.alert("Méthode requise", "Choisissez une méthode de paiement.");
            return;
        }
        setBusy(true);
        try {
            await api.post("/wallet/topup", {
                amount: amt,
                method: method.name,
                phone: phone.trim() || undefined,
                transaction_id: reference.trim() || undefined,
            });
            setTopupOpen(false);
            setAmount(""); setPhone(""); setReference("");
            load();
            Alert.alert(
                "Rechargement déclaré ✅",
                "En attente de confirmation par le vendeur. Vous serez notifié une fois validé.",
            );
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setBusy(false);
        }
    };

    const submitTransfer = async () => {
        const amt = parseFloat(tAmount);
        if (!amt || amt < 100) {
            Alert.alert("Montant invalide", "Le montant minimum est de 100 FCFA.");
            return;
        }
        if (!recipient.trim()) {
            Alert.alert("Destinataire requis", "Entrez l'email ou le téléphone du destinataire.");
            return;
        }
        setBusy(true);
        try {
            await api.post("/wallet/transfer", {
                amount: amt,
                recipient: recipient.trim(),
                note: note.trim() || undefined,
            });
            setTransferOpen(false);
            setTAmount(""); setRecipient(""); setNote("");
            load();
            Alert.alert("Transfert effectué ✅", "Le montant a été envoyé.");
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setBusy(false);
        }
    };

    if (loading) {
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    const statusBadge = (status) => {
        const map = {
            pending: { label: "En attente", color: "#f59e0b", bg: "#fef3c7" },
            confirmed: { label: "Confirmé", color: "#16a34a", bg: "#dcfce7" },
            rejected: { label: "Rejeté", color: "#dc2626", bg: "#fee2e2" },
        };
        const s = map[status] || map.confirmed;
        return (
            <View style={[styles.badge, { backgroundColor: s.bg }]}>
                <Text style={[styles.badgeText, { color: s.color }]}>{s.label}</Text>
            </View>
        );
    };

    return (
        <View style={{ flex: 1, backgroundColor: COLORS.bg }}>
            <ScrollView showsVerticalScrollIndicator={false}>
                <LinearGradient
                    colors={COLORS.gradient}
                    start={COLORS.gradientStart}
                    end={COLORS.gradientEnd}
                    style={styles.card}
                >
                    <Text style={styles.label}>Solde du portefeuille</Text>
                    <Text style={styles.balance}>
                        {Number(wallet.balance).toLocaleString("fr-FR", { minimumFractionDigits: 0 })}{" "}
                        <Text style={styles.currency}>{wallet.currency}</Text>
                    </Text>
                    <View style={styles.actions}>
                        <TouchableOpacity style={styles.action} onPress={() => setTopupOpen(true)}>
                            <Ionicons name="add-circle-outline" size={20} color="#fff" />
                            <Text style={styles.actionText}>Recharger</Text>
                        </TouchableOpacity>
                        <TouchableOpacity style={styles.action} onPress={() => setTransferOpen(true)}>
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
                    wallet.transactions.map((t) => (
                        <View key={t.id} style={styles.txRow}>
                            <View style={styles.txIcon}>
                                <Ionicons
                                    name={t.incoming ? "arrow-down-outline" : "arrow-up-outline"}
                                    size={18}
                                    color={t.incoming ? "#16a34a" : "#dc2626"}
                                />
                            </View>
                            <View style={{ flex: 1 }}>
                                <Text style={styles.txLabel}>{t.label}</Text>
                                <Text style={styles.txDate}>
                                    {t.method ? `${t.method} · ` : ""}{t.date}
                                </Text>
                            </View>
                            <View style={{ alignItems: "flex-end" }}>
                                <Text style={[styles.txAmount, { color: t.incoming ? "#16a34a" : "#dc2626" }]}>
                                    {t.incoming ? "+" : "-"}{formatPrice(t.amount)}
                                </Text>
                                {statusBadge(t.status)}
                            </View>
                        </View>
                    ))
                )}
                <View style={{ height: 20 }} />
            </ScrollView>

            {/* Modal recharger */}
            <Modal visible={topupOpen} transparent animationType="slide" onRequestClose={() => setTopupOpen(false)}>
                <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
                    <Pressable style={styles.backdrop} onPress={() => setTopupOpen(false)}>
                        <Pressable style={[styles.sheet, { paddingBottom: insets.bottom + 16 }]}>
                            <Text style={styles.sheetTitle}>Recharger le portefeuille</Text>

                            <Text style={styles.fieldLabel}>Montant (FCFA)</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Ex : 5000"
                                placeholderTextColor="#9ca3af"
                                keyboardType="numeric"
                                value={amount}
                                onChangeText={setAmount}
                            />

                            <Text style={styles.fieldLabel}>Méthode</Text>
                            <View style={styles.methodRow}>
                                {methods.map((m) => {
                                    const on = method?.id === m.id;
                                    return (
                                        <TouchableOpacity
                                            key={m.id}
                                            style={[styles.methodChip, on && styles.methodChipOn]}
                                            onPress={() => setMethod(m)}
                                        >
                                            <Text style={[styles.methodChipText, on && { color: "#fff" }]}>
                                                {m.name}
                                            </Text>
                                        </TouchableOpacity>
                                    );
                                })}
                            </View>

                            {method?.account_number ? (
                                <View style={styles.instr}>
                                    <Text style={styles.instrAccount}>Numéro : {method.account_number}</Text>
                                    {method.instructions ? (
                                        <Text style={styles.instrText}>{method.instructions}</Text>
                                    ) : null}
                                </View>
                            ) : null}

                            <Text style={styles.fieldLabel}>Numéro utilisé (optionnel)</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Votre numéro"
                                placeholderTextColor="#9ca3af"
                                keyboardType="phone-pad"
                                value={phone}
                                onChangeText={setPhone}
                            />
                            <Text style={styles.fieldLabel}>Référence de transaction (optionnel)</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Référence reçue par SMS"
                                placeholderTextColor="#9ca3af"
                                value={reference}
                                onChangeText={setReference}
                            />

                            <TouchableOpacity
                                style={[styles.submitBtn, busy && { opacity: 0.6 }]}
                                onPress={submitTopup}
                                disabled={busy}
                            >
                                {busy ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitText}>J'ai payé</Text>}
                            </TouchableOpacity>
                        </Pressable>
                    </Pressable>
                </KeyboardAvoidingView>
            </Modal>

            {/* Modal transférer */}
            <Modal visible={transferOpen} transparent animationType="slide" onRequestClose={() => setTransferOpen(false)}>
                <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
                    <Pressable style={styles.backdrop} onPress={() => setTransferOpen(false)}>
                        <Pressable style={[styles.sheet, { paddingBottom: insets.bottom + 16 }]}>
                            <Text style={styles.sheetTitle}>Transférer de l'argent</Text>

                            <Text style={styles.fieldLabel}>Montant (FCFA)</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Ex : 2000"
                                placeholderTextColor="#9ca3af"
                                keyboardType="numeric"
                                value={tAmount}
                                onChangeText={setTAmount}
                            />
                            <Text style={styles.fieldLabel}>Destinataire (email ou téléphone)</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="email@exemple.com ou 07..."
                                placeholderTextColor="#9ca3af"
                                autoCapitalize="none"
                                value={recipient}
                                onChangeText={setRecipient}
                            />
                            <Text style={styles.fieldLabel}>Note (optionnel)</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="Message"
                                placeholderTextColor="#9ca3af"
                                value={note}
                                onChangeText={setNote}
                            />
                            <Text style={styles.hint}>Solde disponible : {formatPrice(wallet.balance)}</Text>

                            <TouchableOpacity
                                style={[styles.submitBtn, busy && { opacity: 0.6 }]}
                                onPress={submitTransfer}
                                disabled={busy}
                            >
                                {busy ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitText}>Envoyer</Text>}
                            </TouchableOpacity>
                        </Pressable>
                    </Pressable>
                </KeyboardAvoidingView>
            </Modal>
        </View>
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
        alignItems: "center",
        gap: 12,
        backgroundColor: "#fff",
        marginHorizontal: 12,
        marginBottom: 8,
        padding: 14,
        borderRadius: RADIUS.md,
    },
    txIcon: {
        width: 36,
        height: 36,
        borderRadius: 18,
        backgroundColor: "#f3f4f6",
        alignItems: "center",
        justifyContent: "center",
    },
    txLabel: { color: COLORS.text, fontWeight: "600", fontSize: 14 },
    txDate: { color: COLORS.textLight, fontSize: 11.5, marginTop: 2 },
    txAmount: { fontWeight: "800", fontSize: 14 },
    badge: { borderRadius: 6, paddingHorizontal: 6, paddingVertical: 1, marginTop: 4 },
    badgeText: { fontSize: 10, fontWeight: "700" },
    // Modals
    backdrop: { flex: 1, backgroundColor: "rgba(0,0,0,0.4)", justifyContent: "flex-end" },
    sheet: {
        backgroundColor: "#fff",
        borderTopLeftRadius: 20,
        borderTopRightRadius: 20,
        padding: 18,
        maxHeight: "88%",
    },
    sheetTitle: { fontSize: 17, fontWeight: "800", color: COLORS.text, marginBottom: 12 },
    fieldLabel: { fontSize: 12.5, fontWeight: "700", color: COLORS.textLight, marginTop: 12, marginBottom: 6 },
    input: {
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: RADIUS.md,
        paddingHorizontal: 14,
        paddingVertical: 12,
        fontSize: 15,
        color: COLORS.text,
    },
    methodRow: { flexDirection: "row", flexWrap: "wrap", gap: 8 },
    methodChip: {
        paddingHorizontal: 14,
        paddingVertical: 9,
        borderRadius: RADIUS.pill,
        backgroundColor: "#f3f4f6",
        borderWidth: 1,
        borderColor: "#eee",
    },
    methodChipOn: { backgroundColor: COLORS.primaryDark, borderColor: COLORS.primaryDark },
    methodChipText: { fontSize: 13, fontWeight: "600", color: COLORS.text },
    instr: {
        backgroundColor: COLORS.soft,
        borderRadius: RADIUS.md,
        padding: 12,
        marginTop: 12,
    },
    instrAccount: { fontWeight: "900", color: COLORS.primaryDark, marginBottom: 6 },
    instrText: { fontSize: 12.5, color: "#374151", lineHeight: 19 },
    hint: { color: COLORS.textLight, fontSize: 12, marginTop: 10 },
    submitBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 15,
        alignItems: "center",
        marginTop: 18,
    },
    submitText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
