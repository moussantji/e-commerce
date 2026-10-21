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
    Platform,
} from "react-native";
import { useFocusEffect } from "@react-navigation/native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import * as ImagePicker from "expo-image-picker";
import api, { apiError } from "../api/client";
import ErrorState from "../components/ErrorState";
import FocusTextInput from "../components/FocusTextInput";
import { formatPrice } from "../utils";
import { COLORS, RADIUS } from "../theme";

const STATUS_LABELS = {
    en_attente: "En attente de paiement",
    paiement_declare: "Paiement en vérification",
    payee: "Payée — en préparation",
    traitement: "En préparation",
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
    const [paymentMethods, setPaymentMethods] = useState([]);
    const [payment, setPayment] = useState(null);
    const [payPhone, setPayPhone] = useState("");
    const [payRef, setPayRef] = useState("");
    const [proofPhotos, setProofPhotos] = useState([]);
    const [submitting, setSubmitting] = useState(false);
    const [walletBalance, setWalletBalance] = useState(0);

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
            const current = data.data ?? data;
            setOrder(current);
            setPayPhone((prev) => prev || current?.adresse_livraison?.telephone || "");

            const [methodsRes, walletRes] = await Promise.all([
                api.get("/payment-methods").catch(() => ({ data: { data: [] } })),
                api.get("/wallet").catch(() => ({ data: {} })),
            ]);

            const list = methodsRes.data?.data ?? [];
            setPaymentMethods(list);
            setWalletBalance(Number(walletRes.data?.balance || 0));
            setPayment((prev) => {
                if (prev && list.some((m) => m.id === prev.id)) return prev;
                const selected = list.find(
                    (m) => m.id === current?.payment_method?.id,
                );
                if (selected) return selected;
                if (list.length === 0 && current?.payment_method) {
                    return current.payment_method;
                }
                return list.find((m) => !m.cod) || list[0] || null;
            });
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
                <ErrorState
                    style={styles.center}
                    title="Impossible d'afficher la commande"
                    message={error || "Cette commande est introuvable ou a été supprimée."}
                    onRetry={() => {
                        setLoading(true);
                        load();
                    }}
                />
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
    const isDeclared = order.statut === "paiement_declare";
    const cancelled = order.statut === "annule";
    const isCodOrder = !!order.payment_method?.cod;
    const mobileMoneyMethods = paymentMethods.filter((m) => !m.cod);
    const payMethodName = isCodOrder
        ? order.payment_method?.name
        : payment?.name || order.payment_method?.name;
    const canUseWallet =
        Number(walletBalance) > 0 &&
        Number(walletBalance) >= Number(order.total || 0);

    const openMap = () => {
        const q = encodeURIComponent(fullAddr || city || "");
        if (q) Linking.openURL(`https://www.google.com/maps/search/?api=1&query=${q}`);
    };

    const pickProofPhotos = async () => {
        const remaining = 3 - proofPhotos.length;
        if (remaining <= 0) {
            Alert.alert("Captures", "Vous pouvez joindre jusqu'à 3 captures.");
            return;
        }
        const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
        if (!perm.granted) {
            Alert.alert("Permission requise", "Autorisez l'accès à vos photos.");
            return;
        }
        const result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsMultipleSelection: true,
            selectionLimit: remaining,
            quality: 0.7,
        });
        if (result.canceled) return;
        const assets = result.assets ?? [];
        setProofPhotos((prev) =>
            [
                ...prev,
                ...assets.map((asset, index) => ({
                    uri: asset.uri,
                    name:
                        asset.fileName ||
                        `preuve-${Date.now()}-${index}.jpg`,
                    type: asset.mimeType || "image/jpeg",
                })),
            ].slice(0, 3),
        );
    };

    const removeProofPhoto = (index) => {
        setProofPhotos((prev) => prev.filter((_, i) => i !== index));
    };

    const confirmCod = async () => {
        const methodId = order.payment_method?.id ?? payment?.id;
        if (!methodId || submitting) return;
        setSubmitting(true);
        try {
            const { data } = await api.post(`/orders/${id}/pay`, {
                payment_method_id: methodId,
            });
            setOrder(data.data ?? data);
            Alert.alert(
                "Commande confirmée",
                "Vous paierez à la livraison. Merci pour votre confiance.",
            );
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setSubmitting(false);
        }
    };

    const payWithWallet = async () => {
        if (submitting) return;
        setSubmitting(true);
        try {
            const { data } = await api.post(`/orders/${id}/pay`, {
                wallet: true,
            });
            setOrder(data.data ?? data);
            setWalletBalance((prev) =>
                Math.max(0, Number(prev) - Number(order.total || 0)),
            );
            Alert.alert(
                "Paiement effectué",
                "Votre commande a été payée avec votre portefeuille.",
            );
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setSubmitting(false);
        }
    };

    const submitPaymentProof = async () => {
        if (submitting) return;
        if (!payment) {
            Alert.alert("Paiement", "Choisissez un moyen de paiement.");
            return;
        }
        if (proofPhotos.length === 0) {
            Alert.alert(
                "Capture obligatoire",
                "Ajoutez au moins une capture de votre paiement.",
            );
            return;
        }

        setSubmitting(true);
        try {
            const fd = new FormData();
            fd.append("payment_method_id", String(payment.id));
            fd.append("provider", payment.name || payment.provider || "Mobile Money");
            if (payPhone.trim()) fd.append("phone", payPhone.trim());
            if (payRef.trim()) fd.append("transaction_id", payRef.trim());
            proofPhotos.forEach((photo) => {
                fd.append("photos[]", {
                    uri:
                        Platform.OS === "ios"
                            ? photo.uri.replace("file://", "")
                            : photo.uri,
                    name: photo.name,
                    type: photo.type,
                });
            });

            const { data } = await api.post(
                `/orders/${id}/payment-proof`,
                fd,
            );
            setOrder(data.data ?? data);
            setProofPhotos([]);
            setPayRef("");
            Alert.alert(
                "Preuve envoyée",
                "Le vendeur va vérifier votre paiement. Vous serez notifié dès sa validation.",
            );
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <View style={styles.container}>
            {Header}
            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={{ paddingBottom: isPending || isDeclared ? 100 : 24 }}
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

                {/* Paiement déclaré : en attente de confirmation */}
                {isDeclared && (
                    <View style={styles.infoBanner}>
                        <Ionicons name="time-outline" size={18} color={COLORS.primaryDark} />
                        <Text style={styles.infoBannerText}>
                            Paiement déclaré. En attente de confirmation par le vendeur.
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

                {/* Paiement inline, comme sur la page commande du site */}
                {isPending && (
                    <View style={styles.card}>
                        <View style={styles.payHead}>
                            <Text style={styles.cardTitle}>Payer ma commande</Text>
                            {payMethodName ? (
                                <View style={styles.methodPill}>
                                    <Text style={styles.methodPillText}>
                                        {payMethodName}
                                    </Text>
                                </View>
                            ) : null}
                        </View>

                        {isCodOrder ? (
                            <>
                                <View style={styles.codBox}>
                                    <Ionicons
                                        name="cash-outline"
                                        size={24}
                                        color={COLORS.primaryDark}
                                    />
                                    <View style={{ flex: 1 }}>
                                        <Text style={styles.codTitle}>
                                            Paiement à la livraison
                                        </Text>
                                        <Text style={styles.codText}>
                                            Vous réglerez le livreur à la
                                            réception de votre colis.
                                        </Text>
                                    </View>
                                </View>
                                <TouchableOpacity
                                    style={[
                                        styles.proofBtn,
                                        submitting && styles.proofBtnDisabled,
                                    ]}
                                    onPress={confirmCod}
                                    disabled={submitting}
                                    activeOpacity={0.9}
                                >
                                    {submitting ? (
                                        <ActivityIndicator color="#fff" />
                                    ) : (
                                        <Text style={styles.proofBtnText}>
                                            Confirmer ma commande
                                        </Text>
                                    )}
                                </TouchableOpacity>
                            </>
                        ) : (
                            <>
                                {canUseWallet ? (
                                    <TouchableOpacity
                                        style={styles.walletPayBtn}
                                        onPress={payWithWallet}
                                        disabled={submitting}
                                        activeOpacity={0.85}
                                    >
                                        <Ionicons
                                            name="wallet-outline"
                                            size={22}
                                            color={COLORS.primaryDark}
                                        />
                                        <View style={{ flex: 1 }}>
                                            <Text style={styles.walletPayTitle}>
                                                Payer avec mon portefeuille
                                            </Text>
                                            <Text style={styles.walletPaySub}>
                                                Solde :{" "}
                                                {formatPrice(walletBalance)}
                                            </Text>
                                        </View>
                                        <Ionicons
                                            name="chevron-forward"
                                            size={18}
                                            color={COLORS.textLight}
                                        />
                                    </TouchableOpacity>
                                ) : null}

                                {mobileMoneyMethods.length > 0 ? (
                                    <>
                                        <Text style={styles.payLabel}>
                                            Choisissez votre opérateur
                                        </Text>
                                        {mobileMoneyMethods.map((m) => {
                                            const on = payment?.id === m.id;
                                            return (
                                                <TouchableOpacity
                                                    key={m.id}
                                                    style={[
                                                        styles.methodChoice,
                                                        on &&
                                                            styles.methodChoiceOn,
                                                    ]}
                                                    activeOpacity={0.85}
                                                    onPress={() => setPayment(m)}
                                                >
                                                    <Ionicons
                                                        name={
                                                            on
                                                                ? "radio-button-on"
                                                                : "radio-button-off"
                                                        }
                                                        size={20}
                                                        color={
                                                            on
                                                                ? COLORS.primaryDark
                                                                : "#cbd5e1"
                                                        }
                                                    />
                                                    <View style={{ flex: 1 }}>
                                                        <Text
                                                            style={
                                                                styles.methodName
                                                            }
                                                        >
                                                            {m.name}
                                                        </Text>
                                                        {m.provider ? (
                                                            <Text
                                                                style={
                                                                    styles.methodProvider
                                                                }
                                                            >
                                                                {m.provider}
                                                            </Text>
                                                        ) : null}
                                                    </View>
                                                </TouchableOpacity>
                                            );
                                        })}
                                    </>
                                ) : (
                                    <Text style={styles.payHint}>
                                        Aucun opérateur mobile money
                                        disponible pour le moment.
                                    </Text>
                                )}

                                {payment && !payment.cod ? (
                                    <View style={styles.instructionsBox}>
                                        {payment.instructions ? (
                                            <Text style={styles.instructionsText}>
                                                {payment.instructions}
                                            </Text>
                                        ) : (
                                            <Text style={styles.instructionsText}>
                                                Effectuez le paiement vers le
                                                numéro indiqué, puis joignez la
                                                capture.
                                            </Text>
                                        )}
                                        {payment.account_number ? (
                                            <Text style={styles.accountNumber}>
                                                Numéro : {payment.account_number}
                                            </Text>
                                        ) : null}
                                    </View>
                                ) : null}

                                <Text style={styles.payLabel}>
                                    Numéro ayant payé (optionnel)
                                </Text>
                                <FocusTextInput
                                    style={styles.payInput}
                                    value={payPhone}
                                    onChangeText={setPayPhone}
                                    placeholder="Ex : 70 00 00 00"
                                    placeholderTextColor="#9ca3af"
                                    keyboardType="phone-pad"
                                />

                                <Text style={styles.payLabel}>
                                    Référence de transaction (optionnel)
                                </Text>
                                <FocusTextInput
                                    style={styles.payInput}
                                    value={payRef}
                                    onChangeText={setPayRef}
                                    placeholder="ID de la transaction"
                                    placeholderTextColor="#9ca3af"
                                    autoCapitalize="characters"
                                />

                                <Text style={styles.payLabel}>
                                    Capture du paiement *
                                </Text>
                                <TouchableOpacity
                                    style={styles.uploadBtn}
                                    onPress={pickProofPhotos}
                                    activeOpacity={0.85}
                                    disabled={submitting}
                                >
                                    <Ionicons
                                        name="camera-outline"
                                        size={20}
                                        color={COLORS.primaryDark}
                                    />
                                    <Text style={styles.uploadText}>
                                        Ajouter une capture (
                                        {proofPhotos.length}/3)
                                    </Text>
                                </TouchableOpacity>

                                {proofPhotos.length > 0 ? (
                                    <View style={styles.proofRow}>
                                        {proofPhotos.map((photo, index) => (
                                            <View
                                                key={`${photo.uri}-${index}`}
                                                style={styles.proofThumbWrap}
                                            >
                                                <Image
                                                    source={{ uri: photo.uri }}
                                                    style={styles.proofThumb}
                                                />
                                                <TouchableOpacity
                                                    style={styles.proofRemove}
                                                    onPress={() =>
                                                        removeProofPhoto(index)
                                                    }
                                                    hitSlop={6}
                                                >
                                                    <Ionicons
                                                        name="close"
                                                        size={13}
                                                        color="#fff"
                                                    />
                                                </TouchableOpacity>
                                            </View>
                                        ))}
                                    </View>
                                ) : null}

                                <TouchableOpacity
                                    style={[
                                        styles.proofBtn,
                                        (!payment ||
                                            proofPhotos.length === 0) &&
                                            styles.proofBtnDisabled,
                                    ]}
                                    onPress={submitPaymentProof}
                                    disabled={
                                        submitting ||
                                        !payment ||
                                        proofPhotos.length === 0
                                    }
                                    activeOpacity={0.9}
                                >
                                    {submitting ? (
                                        <ActivityIndicator color="#fff" />
                                    ) : (
                                        <Text style={styles.proofBtnText}>
                                            J'ai payé · envoyer la preuve
                                        </Text>
                                    )}
                                </TouchableOpacity>
                                <Text style={styles.secureNote}>
                                    🔒 Votre capture est uniquement visible par
                                    l'équipe de vérification.
                                </Text>
                            </>
                        )}
                    </View>
                )}

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
                        style={[styles.cancelBtn, { flex: 1 }]}
                        activeOpacity={0.85}
                        onPress={() => setCancelOpen(true)}
                    >
                        <Text style={styles.cancelText}>Annuler la commande</Text>
                    </TouchableOpacity>
                </View>
            )}

            {/* Pied d'action (paiement déclaré : possibilité d'annuler) */}
            {isDeclared && (
                <View style={[styles.footer, { paddingBottom: insets.bottom + 12 }]}>
                    <TouchableOpacity
                        style={[styles.cancelBtn, { flex: 1 }]}
                        activeOpacity={0.85}
                        onPress={() => setCancelOpen(true)}
                    >
                        <Text style={styles.cancelText}>Annuler la commande</Text>
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

    infoBanner: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: COLORS.soft,
        paddingHorizontal: 16,
        paddingVertical: 12,
    },
    infoBannerText: { flex: 1, color: COLORS.primaryDark, fontSize: 12.5, lineHeight: 17, fontWeight: "600" },

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

    // Paiement dans le détail de commande (même étape que le site)
    payHead: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        gap: 8,
    },
    methodPill: {
        backgroundColor: COLORS.soft,
        borderRadius: RADIUS.pill,
        paddingHorizontal: 10,
        paddingVertical: 4,
        marginBottom: 8,
        maxWidth: "55%",
    },
    methodPillText: {
        color: COLORS.primaryDark,
        fontSize: 11,
        fontWeight: "800",
    },
    codBox: {
        flexDirection: "row",
        alignItems: "center",
        gap: 12,
        backgroundColor: "#f8fafc",
        borderRadius: 12,
        padding: 12,
        marginBottom: 12,
    },
    codTitle: { fontSize: 14, fontWeight: "800", color: COLORS.text },
    codText: {
        fontSize: 12.5,
        color: COLORS.textLight,
        lineHeight: 17,
        marginTop: 2,
    },
    walletPayBtn: {
        flexDirection: "row",
        alignItems: "center",
        gap: 10,
        borderWidth: 1,
        borderColor: "rgba(102,126,234,0.35)",
        backgroundColor: "rgba(102,126,234,0.06)",
        borderRadius: 12,
        padding: 12,
        marginBottom: 6,
    },
    walletPayTitle: { fontSize: 13.5, fontWeight: "800", color: COLORS.text },
    walletPaySub: { fontSize: 11.5, color: COLORS.textLight, marginTop: 2 },
    payLabel: {
        fontSize: 12.5,
        fontWeight: "700",
        color: COLORS.text,
        marginTop: 12,
        marginBottom: 7,
    },
    methodChoice: {
        flexDirection: "row",
        alignItems: "center",
        gap: 9,
        borderWidth: 1,
        borderColor: "#f1f5f9",
        borderRadius: 11,
        paddingVertical: 10,
        paddingHorizontal: 10,
        marginBottom: 7,
    },
    methodChoiceOn: {
        borderColor: COLORS.primary,
        backgroundColor: "rgba(102,126,234,0.06)",
    },
    methodName: { fontSize: 13.5, fontWeight: "700", color: COLORS.text },
    methodProvider: { fontSize: 11.5, color: COLORS.textLight, marginTop: 2 },
    payHint: { fontSize: 12.5, color: COLORS.textLight, lineHeight: 18 },
    instructionsBox: {
        backgroundColor: "#f8fafc",
        borderRadius: 11,
        padding: 11,
        marginTop: 4,
    },
    instructionsText: {
        fontSize: 12.5,
        color: COLORS.text,
        lineHeight: 18,
    },
    accountNumber: {
        fontSize: 13,
        fontWeight: "800",
        color: COLORS.primaryDark,
        marginTop: 7,
    },
    payInput: {
        borderWidth: 1,
        borderColor: COLORS.border,
        borderRadius: 11,
        paddingHorizontal: 12,
        paddingVertical: 10,
        fontSize: 14,
        color: COLORS.text,
        backgroundColor: "#fff",
    },
    uploadBtn: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 8,
        borderWidth: 1.5,
        borderStyle: "dashed",
        borderColor: "rgba(102,126,234,0.5)",
        borderRadius: 12,
        paddingVertical: 13,
        backgroundColor: "rgba(102,126,234,0.04)",
    },
    uploadText: {
        color: COLORS.primaryDark,
        fontWeight: "700",
        fontSize: 13,
    },
    proofRow: { flexDirection: "row", gap: 9, marginTop: 10 },
    proofThumbWrap: { position: "relative" },
    proofThumb: {
        width: 64,
        height: 64,
        borderRadius: 9,
        backgroundColor: "#e5e7eb",
    },
    proofRemove: {
        position: "absolute",
        top: -6,
        right: -6,
        width: 20,
        height: 20,
        borderRadius: 10,
        backgroundColor: "#dc2626",
        alignItems: "center",
        justifyContent: "center",
    },
    proofBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 13,
        paddingVertical: 14,
        alignItems: "center",
        justifyContent: "center",
        marginTop: 14,
    },
    proofBtnDisabled: { opacity: 0.45 },
    proofBtnText: { color: "#fff", fontWeight: "800", fontSize: 14.5 },
    secureNote: {
        fontSize: 11,
        color: COLORS.textLight,
        textAlign: "center",
        marginTop: 10,
        lineHeight: 16,
    },

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
