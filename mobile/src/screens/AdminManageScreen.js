import React, { useCallback, useEffect, useMemo, useState } from "react";
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ActivityIndicator,
    Alert,
    ScrollView,
    FlatList,
    Modal,
    Switch,
    Pressable,
    KeyboardAvoidingView,
    Platform,
    Image,
} from "react-native";
import * as ImagePicker from "expo-image-picker";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { COLORS, RADIUS } from "../theme";
import { getAdminResource } from "../config/adminResources";

export default function AdminManageScreen({ route, navigation }) {
    const insets = useSafeAreaInsets();
    const resourceKey = route.params?.resource;
    const config = getAdminResource(resourceKey);
    const title = route.params?.title || config?.title || "Gestion";

    const [items, setItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState("");
    const [optionsMap, setOptionsMap] = useState({ categories: [], brands: [] });

    // Formulaire (création / édition)
    const [formOpen, setFormOpen] = useState(false);
    const [editing, setEditing] = useState(null); // objet en cours d'édition, ou null pour création
    const [form, setForm] = useState({});
    const [saving, setSaving] = useState(false);

    const base = `/admin/manage/${resourceKey}`;

    const needsOptions = useMemo(
        () => (config?.fields || []).some((f) => typeof f.options === "string"),
        [config],
    );

    const load = useCallback(async () => {
        try {
            const { data } = await api.get(base, {
                params: search ? { search } : {},
            });
            setItems(data.data ?? []);
        } catch (e) {
            if (e?.response?.status === 403) {
                Alert.alert("Accès refusé", "Réservé aux administrateurs.");
                navigation.goBack();
            } else {
                Alert.alert("Erreur", apiError(e));
            }
        } finally {
            setLoading(false);
        }
    }, [base, search, navigation]);

    const loadOptions = useCallback(async () => {
        if (!needsOptions) return;
        try {
            const { data } = await api.get("/admin/manage/options");
            setOptionsMap({
                categories: data.categories ?? [],
                brands: data.brands ?? [],
            });
        } catch (e) {
            /* ignore */
        }
    }, [needsOptions]);

    useEffect(() => {
        load();
        loadOptions();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // Recherche avec léger debounce
    useEffect(() => {
        const t = setTimeout(() => load(), 350);
        return () => clearTimeout(t);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    if (!config) {
        return (
            <View style={styles.center}>
                <Text style={styles.emptyText}>Ressource inconnue.</Text>
            </View>
        );
    }

    const optionsFor = (field) =>
        Array.isArray(field.options) ? field.options : optionsMap[field.options] || [];

    const openCreate = () => {
        const initial = {};
        config.fields.forEach((f) => {
            if (f.type === "switch") initial[f.key] = f.default ?? false;
            else if (f.type === "image") initial[f.key] = null;
            else if (f.default !== undefined) initial[f.key] = f.default;
            else initial[f.key] = "";
        });
        setForm(initial);
        setEditing(null);
        setFormOpen(true);
    };

    const openEdit = (item) => {
        const initial = {};
        config.fields.forEach((f) => {
            if (f.type === "switch") initial[f.key] = !!item[f.key];
            else if (f.type === "password") initial[f.key] = "";
            else if (f.type === "image")
                // Image existante : on conserve son URL pour l'aperçu, sans la ré-uploader.
                initial[f.key] = item[f.key] ? { url: item[f.key], existing: true } : null;
            else initial[f.key] = item[f.key] != null ? String(item[f.key]) : "";
        });
        setForm(initial);
        setEditing(item);
        setFormOpen(true);
    };

    const setField = (key, value) => setForm((f) => ({ ...f, [key]: value }));

    const pickImage = async (key) => {
        const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
        if (!perm.granted) {
            Alert.alert("Permission requise", "Autorisez l'accès aux photos.");
            return;
        }
        const result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            quality: 0.7,
        });
        if (result.canceled) return;
        const asset = result.assets[0];
        const ext = (asset.uri.split(".").pop() || "jpg").toLowerCase();
        setField(key, {
            uri: asset.uri,
            name: asset.fileName || `image_${Date.now()}.${ext}`,
            type: asset.mimeType || `image/${ext === "jpg" ? "jpeg" : ext}`,
        });
    };

    const buildPayload = () => {
        const payload = {};
        for (const f of config.fields) {
            if (f.type === "image") continue; // géré séparément (multipart)
            const v = form[f.key];
            if (f.type === "switch") {
                payload[f.key] = !!v;
            } else if (f.type === "number") {
                if (v !== "" && v != null) payload[f.key] = Number(v);
            } else {
                const s = typeof v === "string" ? v.trim() : v;
                if (s !== "" && s != null) payload[f.key] = s;
            }
        }
        return payload;
    };

    // Retourne les champs image qui contiennent une NOUVELLE image sélectionnée.
    const pickedImageFields = () =>
        config.fields.filter(
            (f) => f.type === "image" && form[f.key] && form[f.key].uri && !form[f.key].existing,
        );

    const buildFormData = () => {
        const fd = new FormData();
        for (const f of config.fields) {
            if (f.type === "image") continue;
            const v = form[f.key];
            if (f.type === "switch") {
                fd.append(f.key, v ? "1" : "0");
            } else if (f.type === "number") {
                if (v !== "" && v != null) fd.append(f.key, String(v));
            } else {
                const s = typeof v === "string" ? v.trim() : v;
                if (s !== "" && s != null) fd.append(f.key, String(s));
            }
        }
        pickedImageFields().forEach((f) => {
            const img = form[f.key];
            fd.append(f.key, {
                uri: Platform.OS === "ios" ? img.uri.replace("file://", "") : img.uri,
                name: img.name,
                type: img.type,
            });
        });
        return fd;
    };

    const validate = () => {
        for (const f of config.fields) {
            const isRequired = f.required || (f.createRequired && !editing);
            if (!isRequired) continue;
            const v = form[f.key];
            if (f.type === "switch") continue;
            if (f.type === "image") {
                if (!v) {
                    Alert.alert("Image requise", `Le champ « ${f.label} » est obligatoire.`);
                    return false;
                }
                continue;
            }
            if (v === "" || v == null) {
                Alert.alert("Champ requis", `Le champ « ${f.label} » est obligatoire.`);
                return false;
            }
        }
        return true;
    };

    const submit = async () => {
        if (!validate()) return;
        setSaving(true);
        try {
            const hasNewImage = pickedImageFields().length > 0;

            if (hasNewImage) {
                // Envoi multipart (image + champs). PUT est simulé via _method
                // car PHP ne parse pas le multipart sur une vraie requête PUT.
                const fd = buildFormData();
                if (editing) fd.append("_method", "PUT");
                const url = editing ? `${base}/${editing.id}` : base;
                await api.post(url, fd, {
                    headers: { "Content-Type": "multipart/form-data" },
                });
            } else {
                const payload = buildPayload();
                if (editing) {
                    await api.put(`${base}/${editing.id}`, payload);
                } else {
                    await api.post(base, payload);
                }
            }

            setFormOpen(false);
            setEditing(null);
            load();
        } catch (e) {
            Alert.alert("Impossible", apiError(e));
        } finally {
            setSaving(false);
        }
    };

    const confirmDelete = (item) => {
        Alert.alert(
            "Confirmer la suppression",
            `Supprimer « ${config.primary(item)} » ? Cette action est irréversible.`,
            [
                { text: "Annuler", style: "cancel" },
                {
                    text: "Supprimer",
                    style: "destructive",
                    onPress: async () => {
                        try {
                            await api.delete(`${base}/${item.id}`);
                            load();
                        } catch (e) {
                            Alert.alert("Impossible", apiError(e));
                        }
                    },
                },
            ],
        );
    };

    const renderItem = ({ item }) => {
        const subtitle = config.subtitle ? config.subtitle(item) : "";
        const hasImageField = config.fields.some((f) => f.type === "image");
        return (
            <View style={styles.row}>
                {hasImageField ? (
                    item.image ? (
                        <Image source={{ uri: item.image }} style={styles.rowThumb} />
                    ) : (
                        <View style={[styles.rowThumb, { alignItems: "center", justifyContent: "center" }]}>
                            <Ionicons name={config.icon} size={20} color="#c4c4c4" />
                        </View>
                    )
                ) : null}
                <TouchableOpacity
                    style={{ flex: 1 }}
                    onPress={() => openEdit(item)}
                    activeOpacity={0.7}
                >
                    <Text style={styles.rowTitle} numberOfLines={1}>
                        {config.primary(item)}
                    </Text>
                    {subtitle ? (
                        <Text style={styles.rowSub} numberOfLines={1}>
                            {subtitle}
                        </Text>
                    ) : null}
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.iconBtn}
                    onPress={() => openEdit(item)}
                    hitSlop={8}
                >
                    <Ionicons name="create-outline" size={20} color={COLORS.primaryDark} />
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.iconBtn}
                    onPress={() => confirmDelete(item)}
                    hitSlop={8}
                >
                    <Ionicons name="trash-outline" size={20} color="#dc2626" />
                </TouchableOpacity>
            </View>
        );
    };

    const renderField = (f) => {
        const value = form[f.key];
        if (f.type === "switch") {
            return (
                <View key={f.key} style={styles.switchRow}>
                    <Text style={styles.fieldLabel}>{f.label}</Text>
                    <Switch
                        value={!!value}
                        onValueChange={(v) => setField(f.key, v)}
                        trackColor={{ true: COLORS.primaryDark }}
                    />
                </View>
            );
        }

        if (f.type === "image") {
            const preview = value?.uri || value?.url || null;
            return (
                <View key={f.key} style={{ marginTop: 12 }}>
                    <Text style={styles.fieldLabel}>
                        {f.label}
                        {f.required || (f.createRequired && !editing) ? " *" : ""}
                    </Text>
                    <View style={styles.imageRow}>
                        <TouchableOpacity
                            style={styles.imagePickBox}
                            onPress={() => pickImage(f.key)}
                            activeOpacity={0.8}
                        >
                            {preview ? (
                                <Image source={{ uri: preview }} style={styles.imagePreview} />
                            ) : (
                                <Ionicons name="image-outline" size={30} color="#9ca3af" />
                            )}
                        </TouchableOpacity>
                        <View style={{ flex: 1, gap: 8 }}>
                            <TouchableOpacity
                                style={styles.imageBtn}
                                onPress={() => pickImage(f.key)}
                                activeOpacity={0.8}
                            >
                                <Ionicons name="cloud-upload-outline" size={18} color={COLORS.primaryDark} />
                                <Text style={styles.imageBtnText}>
                                    {preview ? "Changer l'image" : "Choisir une image"}
                                </Text>
                            </TouchableOpacity>
                            {preview ? (
                                <TouchableOpacity
                                    style={styles.imageBtn}
                                    onPress={() => setField(f.key, null)}
                                    activeOpacity={0.8}
                                >
                                    <Ionicons name="trash-outline" size={18} color="#dc2626" />
                                    <Text style={[styles.imageBtnText, { color: "#dc2626" }]}>Retirer</Text>
                                </TouchableOpacity>
                            ) : null}
                        </View>
                    </View>
                </View>
            );
        }

        if (f.type === "select") {
            const options = optionsFor(f);
            return (
                <View key={f.key} style={{ marginTop: 12 }}>
                    <Text style={styles.fieldLabel}>{f.label}</Text>
                    <ScrollView
                        horizontal
                        showsHorizontalScrollIndicator={false}
                        contentContainerStyle={{ gap: 8, paddingVertical: 4 }}
                    >
                        {f.required ? null : (
                            <TouchableOpacity
                                style={[styles.chip, (value === "" || value == null) && styles.chipOn]}
                                onPress={() => setField(f.key, "")}
                            >
                                <Text
                                    style={[
                                        styles.chipText,
                                        (value === "" || value == null) && styles.chipTextOn,
                                    ]}
                                >
                                    Aucun
                                </Text>
                            </TouchableOpacity>
                        )}
                        {options.map((opt) => {
                            const on = String(value) === String(opt.value);
                            return (
                                <TouchableOpacity
                                    key={String(opt.value)}
                                    style={[styles.chip, on && styles.chipOn]}
                                    onPress={() => setField(f.key, opt.value)}
                                >
                                    <Text style={[styles.chipText, on && styles.chipTextOn]}>
                                        {opt.label}
                                    </Text>
                                </TouchableOpacity>
                            );
                        })}
                    </ScrollView>
                </View>
            );
        }

        const keyboardType =
            f.type === "number" ? "numeric" : f.type === "email" ? "email-address" : "default";

        return (
            <View key={f.key} style={{ marginTop: 12 }}>
                <Text style={styles.fieldLabel}>
                    {f.label}
                    {f.required || (f.createRequired && !editing) ? " *" : ""}
                </Text>
                <TextInput
                    style={[styles.input, f.type === "textarea" && styles.textarea]}
                    value={value != null ? String(value) : ""}
                    onChangeText={(t) => setField(f.key, t)}
                    placeholder={f.hint || ""}
                    placeholderTextColor="#9ca3af"
                    keyboardType={keyboardType}
                    autoCapitalize={f.autoCapitalize || (f.type === "email" ? "none" : "sentences")}
                    secureTextEntry={f.type === "password"}
                    multiline={f.type === "textarea"}
                />
                {f.hint && f.type === "password" ? (
                    <Text style={styles.hint}>{f.hint}</Text>
                ) : null}
            </View>
        );
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
                <Text style={styles.headerTitle}>{title}</Text>
                <TouchableOpacity onPress={openCreate} hitSlop={10}>
                    <Ionicons name="add" size={28} color="#fff" />
                </TouchableOpacity>
            </LinearGradient>

            <View style={styles.searchWrap}>
                <Ionicons name="search" size={18} color="#9ca3af" />
                <TextInput
                    style={styles.searchInput}
                    placeholder="Rechercher..."
                    placeholderTextColor="#9ca3af"
                    value={search}
                    onChangeText={setSearch}
                />
            </View>

            {loading ? (
                <View style={styles.center}>
                    <ActivityIndicator size="large" color={COLORS.primary} />
                </View>
            ) : (
                <FlatList
                    data={items}
                    keyExtractor={(it) => String(it.id)}
                    renderItem={renderItem}
                    contentContainerStyle={{ padding: 12, paddingBottom: 30 }}
                    ListEmptyComponent={
                        <View style={styles.center}>
                            <Ionicons name={config.icon} size={48} color="#d1d5db" />
                            <Text style={styles.emptyText}>Aucun élément. Appuyez sur + pour ajouter.</Text>
                        </View>
                    }
                />
            )}

            {/* Bouton flottant d'ajout */}
            <TouchableOpacity
                style={[styles.fab, { bottom: insets.bottom + 20 }]}
                onPress={openCreate}
                activeOpacity={0.85}
            >
                <Ionicons name="add" size={28} color="#fff" />
            </TouchableOpacity>

            {/* Formulaire création / édition */}
            <Modal
                visible={formOpen}
                transparent
                animationType="slide"
                onRequestClose={() => setFormOpen(false)}
            >
                <KeyboardAvoidingView
                    behavior={Platform.OS === "ios" ? "padding" : undefined}
                    style={{ flex: 1 }}
                >
                    <Pressable style={styles.backdrop} onPress={() => setFormOpen(false)}>
                        <Pressable style={[styles.sheet, { paddingBottom: insets.bottom + 16 }]}>
                            <View style={styles.sheetHead}>
                                <Text style={styles.sheetTitle}>
                                    {editing ? "Modifier" : "Ajouter"} · {title}
                                </Text>
                                <TouchableOpacity onPress={() => setFormOpen(false)} hitSlop={10}>
                                    <Ionicons name="close" size={24} color={COLORS.text} />
                                </TouchableOpacity>
                            </View>

                            <ScrollView
                                showsVerticalScrollIndicator={false}
                                keyboardShouldPersistTaps="handled"
                            >
                                {config.fields.map(renderField)}
                                <View style={{ height: 8 }} />
                            </ScrollView>

                            <TouchableOpacity
                                style={[styles.submitBtn, saving && { opacity: 0.6 }]}
                                onPress={submit}
                                disabled={saving}
                            >
                                {saving ? (
                                    <ActivityIndicator color="#fff" />
                                ) : (
                                    <Text style={styles.submitText}>
                                        {editing ? "Enregistrer" : "Créer"}
                                    </Text>
                                )}
                            </TouchableOpacity>
                        </Pressable>
                    </Pressable>
                </KeyboardAvoidingView>
            </Modal>
        </View>
    );
}

const styles = StyleSheet.create({
    center: { flex: 1, justifyContent: "center", alignItems: "center", padding: 24, gap: 10 },
    header: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingHorizontal: 12,
        paddingBottom: 14,
    },
    headerTitle: { fontSize: 17, fontWeight: "800", color: "#fff", flex: 1, textAlign: "center" },
    searchWrap: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#fff",
        margin: 12,
        marginBottom: 0,
        paddingHorizontal: 14,
        height: 42,
        borderRadius: RADIUS.pill,
        borderWidth: 1,
        borderColor: "#eee",
    },
    searchInput: { flex: 1, fontSize: 14.5, color: COLORS.text, paddingVertical: 0 },
    row: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#fff",
        borderRadius: RADIUS.md,
        padding: 14,
        marginBottom: 8,
    },
    rowTitle: { fontSize: 15, fontWeight: "700", color: COLORS.text },
    rowSub: { fontSize: 12.5, color: COLORS.textLight, marginTop: 3 },
    iconBtn: {
        width: 36,
        height: 36,
        borderRadius: 18,
        backgroundColor: "#f9fafb",
        alignItems: "center",
        justifyContent: "center",
    },
    emptyText: { color: COLORS.textLight, fontSize: 14, textAlign: "center" },
    fab: {
        position: "absolute",
        right: 20,
        width: 56,
        height: 56,
        borderRadius: 28,
        backgroundColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
        elevation: 5,
        shadowColor: "#000",
        shadowOpacity: 0.2,
        shadowRadius: 6,
        shadowOffset: { width: 0, height: 3 },
    },
    // Form sheet
    backdrop: { flex: 1, backgroundColor: "rgba(0,0,0,0.4)", justifyContent: "flex-end" },
    sheet: {
        backgroundColor: "#fff",
        borderTopLeftRadius: 20,
        borderTopRightRadius: 20,
        padding: 18,
        maxHeight: "90%",
    },
    sheetHead: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginBottom: 6,
    },
    sheetTitle: { fontSize: 16, fontWeight: "800", color: COLORS.text },
    fieldLabel: { fontSize: 12.5, fontWeight: "700", color: COLORS.textLight, marginBottom: 6 },
    input: {
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: RADIUS.md,
        paddingHorizontal: 14,
        paddingVertical: 11,
        fontSize: 15,
        color: COLORS.text,
    },
    textarea: { height: 90, textAlignVertical: "top" },
    hint: { fontSize: 11.5, color: COLORS.textLight, marginTop: 4 },
    imageRow: { flexDirection: "row", gap: 12, alignItems: "center" },
    imagePickBox: {
        width: 88,
        height: 88,
        borderRadius: RADIUS.md,
        backgroundColor: "#f3f4f6",
        borderWidth: 1,
        borderColor: "#e5e7eb",
        alignItems: "center",
        justifyContent: "center",
        overflow: "hidden",
    },
    imagePreview: { width: "100%", height: "100%" },
    imageBtn: {
        flexDirection: "row",
        alignItems: "center",
        gap: 6,
        backgroundColor: "#f9fafb",
        borderWidth: 1,
        borderColor: "#e5e7eb",
        borderRadius: RADIUS.md,
        paddingVertical: 9,
        paddingHorizontal: 12,
    },
    imageBtnText: { fontSize: 13, fontWeight: "700", color: COLORS.primaryDark },
    rowThumb: { width: 42, height: 42, borderRadius: 8, backgroundColor: "#f3f4f6" },
    switchRow: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        marginTop: 16,
    },
    chip: {
        paddingHorizontal: 14,
        paddingVertical: 8,
        borderRadius: RADIUS.pill,
        backgroundColor: "#f3f4f6",
        borderWidth: 1,
        borderColor: "#eee",
    },
    chipOn: { backgroundColor: COLORS.primaryDark, borderColor: COLORS.primaryDark },
    chipText: { fontSize: 13, fontWeight: "600", color: COLORS.text },
    chipTextOn: { color: "#fff" },
    submitBtn: {
        backgroundColor: COLORS.primaryDark,
        borderRadius: 14,
        paddingVertical: 15,
        alignItems: "center",
        marginTop: 14,
    },
    submitText: { color: "#fff", fontWeight: "800", fontSize: 16 },
});
