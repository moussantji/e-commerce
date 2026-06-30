import React, { useEffect, useState } from "react";
import {
    View,
    Text,
    Image,
    ScrollView,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
} from "react-native";
import api, { apiError } from "../api/client";
import { useCart } from "../context/CartContext";
import { useAuth } from "../context/AuthContext";
import { formatPrice } from "../utils";
import { LinearGradient } from "expo-linear-gradient";
import { COLORS } from "../theme";

export default function ProductDetailScreen({ route, navigation }) {
    const { id } = route.params;
    const { add } = useCart();
    const { token } = useAuth();
    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [adding, setAdding] = useState(false);

    useEffect(() => {
        (async () => {
            try {
                const { data } = await api.get(`/products/${id}`);
                setProduct(data.data);
            } catch (e) {
                Alert.alert("Erreur", apiError(e));
            } finally {
                setLoading(false);
            }
        })();
    }, [id]);

    const addToCart = async () => {
        if (!token) {
            Alert.alert(
                "Connexion requise",
                "Connectez-vous pour ajouter au panier.",
            );
            return;
        }
        setAdding(true);
        try {
            await add(product.id, 1);
            Alert.alert("✅ Ajouté", `${product.name} ajouté au panier.`);
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setAdding(false);
        }
    };

    if (loading)
        return (
            <View style={styles.center}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    if (!product)
        return (
            <View style={styles.center}>
                <Text>Produit introuvable.</Text>
            </View>
        );

    return (
        <View style={{ flex: 1, backgroundColor: "#fff" }}>
            <ScrollView>
                <Image
                    source={{ uri: product.image }}
                    style={styles.image}
                    resizeMode="cover"
                />
                <View style={styles.body}>
                    <Text style={styles.name}>{product.name}</Text>
                    <Text style={styles.rating}>
                        ⭐ {product.rating_avg ?? 0} / 5 ·{" "}
                        {product.rating_count ?? 0} avis
                    </Text>

                    <View style={styles.priceRow}>
                        <Text style={styles.price}>
                            {formatPrice(product.sale_price ?? product.price)}
                        </Text>
                        {product.sale_price ? (
                            <Text style={styles.oldPrice}>
                                {formatPrice(product.price)}
                            </Text>
                        ) : null}
                    </View>

                    <Text
                        style={[
                            styles.stock,
                            { color: product.in_stock ? "#16a34a" : "#dc2626" },
                        ]}
                    >
                        {product.in_stock
                            ? `En stock (${product.stock})`
                            : "Rupture de stock"}
                    </Text>

                    {product.description ? (
                        <>
                            <Text style={styles.sectionTitle}>Description</Text>
                            <Text style={styles.description}>
                                {String(product.description).replace(
                                    /<[^>]*>/g,
                                    "",
                                )}
                            </Text>
                        </>
                    ) : null}
                </View>
            </ScrollView>

            <View style={styles.footer}>
                <TouchableOpacity
                    style={styles.buttonWrap}
                    onPress={addToCart}
                    disabled={!product.in_stock || adding}
                    activeOpacity={0.85}
                >
                    <LinearGradient
                        colors={
                            !product.in_stock || adding
                                ? ["#cbd5e1", "#9ca3af"]
                                : COLORS.gradient
                        }
                        start={COLORS.gradientStart}
                        end={COLORS.gradientEnd}
                        style={styles.button}
                    >
                        <Text style={styles.buttonText}>
                            {!product.in_stock
                                ? "Indisponible"
                                : adding
                                  ? "Ajout..."
                                  : "🛒 Ajouter au panier"}
                        </Text>
                    </LinearGradient>
                </TouchableOpacity>
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    center: { flex: 1, justifyContent: "center", alignItems: "center" },
    image: { width: "100%", height: 320, backgroundColor: "#e5e7eb" },
    body: { padding: 18 },
    name: { fontSize: 22, fontWeight: "800", color: "#111827" },
    rating: { color: "#6b7280", marginTop: 6 },
    priceRow: {
        flexDirection: "row",
        alignItems: "center",
        gap: 10,
        marginTop: 14,
    },
    price: { fontSize: 26, fontWeight: "900", color: COLORS.accent },
    oldPrice: {
        fontSize: 16,
        color: "#9ca3af",
        textDecorationLine: "line-through",
    },
    stock: { marginTop: 8, fontWeight: "700" },
    sectionTitle: {
        marginTop: 20,
        fontSize: 16,
        fontWeight: "700",
        color: "#111827",
    },
    description: { marginTop: 8, color: "#374151", lineHeight: 21 },
    footer: { padding: 16, borderTopWidth: 1, borderTopColor: "#f0f0f0" },
    buttonWrap: { borderRadius: 14, overflow: "hidden" },
    button: {
        borderRadius: 14,
        paddingVertical: 16,
        alignItems: "center",
    },
    disabled: { opacity: 0.7 },
    buttonText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
