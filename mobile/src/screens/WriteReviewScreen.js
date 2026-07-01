import React, { useState } from "react";
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
    Image,
    Alert,
    ActivityIndicator,
    Platform,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import * as ImagePicker from "expo-image-picker";
import api, { apiError } from "../api/client";
import { COLORS, RADIUS } from "../theme";

const MAX_PHOTOS = 5;

export default function WriteReviewScreen({ route, navigation }) {
    const insets = useSafeAreaInsets();
    const { productId, productName } = route.params || {};

    const [rating, setRating] = useState(0);
    const [comment, setComment] = useState("");
    const [photos, setPhotos] = useState([]); // { uri, name, type }
    const [submitting, setSubmitting] = useState(false);

    const pickPhotos = async () => {
        if (photos.length >= MAX_PHOTOS) {
            Alert.alert("Limite atteinte", `Vous pouvez ajouter jusqu'à ${MAX_PHOTOS} photos.`);
            return;
        }
        const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
        if (!perm.granted) {
            Alert.alert("Permission requise", "Autorisez l'accès aux photos pour en ajouter.");
            return;
        }
        const result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsMultipleSelection: true,
            selectionLimit: MAX_PHOTOS - photos.length,
            quality: 0.7,
        });
        if (result.canceled) return;

        const picked = (result.assets || []).map((a, i) => {
            const uri = a.uri;
            const ext = (uri.split(".").pop() || "jpg").toLowerCase();
            return {
                uri,
                name: a.fileName || `photo_${Date.now()}_${i}.${ext}`,
                type: a.mimeType || `image/${ext === "jpg" ? "jpeg" : ext}`,
            };
        });
        setPhotos((prev) => [...prev, ...picked].slice(0, MAX_PHOTOS));
    };

    const removePhoto = (uri) => {
        setPhotos((prev) => prev.filter((p) => p.uri !== uri));
    };

    const submit = async () => {
        if (rating < 0.5) {
            Alert.alert("Note requise", "Sélectionnez une note (1 à 5 étoiles).");
            return;
        }
        setSubmitting(true);
        try {
            const form = new FormData();
            form.append("rating", String(rating));
            if (comment.trim()) form.append("comment", comment.trim());
            photos.forEach((p) => {
                form.append("photos[]", {
                    uri: Platform.OS === "ios" ? p.uri.replace("file://", "") : p.uri,
                    name: p.name,
                    type: p.type,
                });
            });

            const { data } = await api.post(`/products/${productId}/reviews`, form, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            // Renvoie l'avis créé à l'écran produit
            navigation.navigate({
                name: "ProductDetail",
                params: { id: productId, newReview: data.data },
                merge: true,
            });
            Alert.alert("Merci !", "Votre avis a bien été publié.");
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <View style={[styles.container, { paddingTop: insets.top }]}>
            {/* En-tête */}
            <View style={styles.header}>
                <TouchableOpacity onPress={() => navigation.goBack()} hitSlop={10}>
                    <Ionicons name="chevron-back" size={26} color={COLORS.text} />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Écrire un avis</Text>
                <View style={{ width: 26 }} />
            </View>

            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={{ padding: 16, paddingBottom: 40 }}
                keyboardShouldPersistTaps="handled"
            >
                {productName ? (
                    <Text style={styles.productName} numberOfLines={2}>
                        {productName}
                    </Text>
                ) : null}

                {/* Note en étoiles */}
                <Text style={styles.label}>Votre note</Text>
                <View style={styles.starPicker}>
                    {[1, 2, 3, 4, 5].map((n) => (
                        <TouchableOpacity key={n} onPress={() => setRating(n)} hitSlop={6}>
                            <Ionicons
                                name={rating >= n ? "star" : "star-outline"}
                                size={40}
                                color="#f59e0b"
                            />
                        </TouchableOpacity>
                    ))}
                </View>
                <Text style={styles.starHint}>
                    {rating > 0 ? `${rating} / 5` : "Touchez pour noter"}
                </Text>

                {/* Commentaire */}
                <Text style={styles.label}>Votre commentaire</Text>
                <TextInput
                    style={styles.input}
                    placeholder="Partagez votre expérience (optionnel)"
                    placeholderTextColor="#9ca3af"
                    value={comment}
                    onChangeText={setComment}
                    multiline
                    maxLength={1000}
                />

                {/* Photos */}
                <Text style={styles.label}>Photos (optionnel)</Text>
                <View style={styles.photosWrap}>
                    {photos.map((p) => (
                        <View key={p.uri} style={styles.photoBox}>
                            <Image source={{ uri: p.uri }} style={styles.photo} />
                            <TouchableOpacity
                                style={styles.removePhoto}
                                onPress={() => removePhoto(p.uri)}
                            >
                                <Ionicons name="close" size={14} color="#fff" />
                            </TouchableOpacity>
                        </View>
                    ))}
                    {photos.length < MAX_PHOTOS && (
                        <TouchableOpacity style={styles.addPhoto} onPress={pickPhotos}>
                            <Ionicons name="camera-outline" size={26} color={COLORS.textLight} />
                            <Text style={styles.addPhotoText}>Ajouter</Text>
                        </TouchableOpacity>
                    )}
                </View>
            </ScrollView>

            {/* Bouton publier */}
            <View style={[styles.footer, { paddingBottom: insets.bottom + 12 }]}>
                <TouchableOpacity
                    style={[styles.submitBtn, submitting && { opacity: 0.6 }]}
                    onPress={submit}
                    disabled={submitting}
                >
                    {submitting ? (
                        <ActivityIndicator color="#fff" />
                    ) : (
                        <Text style={styles.submitText}>Publier mon avis</Text>
                    )}
                </TouchableOpacity>
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: "#fff" },
    header: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingHorizontal: 12,
        paddingVertical: 10,
        borderBottomWidth: 1,
        borderBottomColor: "#f3f4f6",
    },
    headerTitle: { fontSize: 17, fontWeight: "800", color: COLORS.text },
    productName: { fontSize: 14, color: COLORS.textLight, marginBottom: 16 },
    label: { fontSize: 14, fontWeight: "800", color: COLORS.text, marginTop: 18, marginBottom: 10 },
    starPicker: { flexDirection: "row", justifyContent: "center", gap: 10 },
    starHint: { textAlign: "center", color: COLORS.textLight, marginTop: 8, fontWeight: "600" },
    input: {
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: RADIUS.md,
        padding: 14,
        minHeight: 110,
        textAlignVertical: "top",
        fontSize: 14,
        color: COLORS.text,
    },
    photosWrap: { flexDirection: "row", flexWrap: "wrap", gap: 10 },
    photoBox: { position: "relative" },
    photo: { width: 78, height: 78, borderRadius: 10, backgroundColor: "#e5e7eb" },
    removePhoto: {
        position: "absolute",
        top: -6,
        right: -6,
        width: 22,
        height: 22,
        borderRadius: 11,
        backgroundColor: "#111827",
        alignItems: "center",
        justifyContent: "center",
    },
    addPhoto: {
        width: 78,
        height: 78,
        borderRadius: 10,
        borderWidth: 1.5,
        borderColor: "#d1d5db",
        borderStyle: "dashed",
        alignItems: "center",
        justifyContent: "center",
        gap: 2,
    },
    addPhotoText: { fontSize: 11, color: COLORS.textLight },
    footer: {
        paddingHorizontal: 16,
        paddingTop: 10,
        borderTopWidth: 1,
        borderTopColor: "#f0f0f0",
        backgroundColor: "#fff",
    },
    submitBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 16,
        alignItems: "center",
    },
    submitText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
