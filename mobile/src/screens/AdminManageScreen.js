import React, { useCallback, useEffect, useMemo, useRef, useState } from "react";
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
    Animated,
} from "react-native";
import * as ImagePicker from "expo-image-picker";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import api, { apiError } from "../api/client";
import { COLORS, RADIUS } from "../theme";
import {
    getAdminResource,
    getSellerProductResource,
} from "../config/adminResources";
import FocusTextInput, {
    useFocusBorder,
    webInputStyle,
} from "../components/FocusTextInput";

const MAX_GALLERY_IMAGES = 10;

/** Normalise un asset expo-image-picker pour l'envoi multipart. */
function formImage(asset, suffix = "") {
    const fileName =
        asset.fileName ||
        asset.file?.name ||
        `image_${Date.now()}${suffix}.jpg`;
    const mime = asset.mimeType || asset.file?.type || "image/jpeg";
    return {
        uri: asset.uri,
        name: fileName,
        type: mime,
        // Sur le web, expo-image-picker fournit un vrai File : indispensable
        // car FormData (navigateur) n'accepte pas les descripteurs React Native.
        file: asset.file || null,
    };
}

/** Ajoute une image au FormData (fichier navigateur ou descripteur natif). */
function appendImagePart(fd, key, img) {
    if (Platform.OS === "web" && img.file) {
        fd.append(key, img.file, img.name);
        return;
    }
    fd.append(key, {
        uri: Platform.OS === "ios" ? img.uri.replace("file://", "") : img.uri,
        name: img.name,
        type: img.type,
    });
}

export default function AdminManageScreen({ route, navigation }) {
    const insets = useSafeAreaInsets();
    const resourceKey = route.params?.resource;
    const config =
        resourceKey === "seller-products"
            ? getSellerProductResource()
            : getAdminResource(resourceKey);
    const title = route.params?.title || config?.title || "Gestion";
    const sellerMode = resourceKey === "seller-products";
    const base =
        route.params?.basePath ||
        (sellerMode ? "/seller/products" : `/admin/manage/${resourceKey}`);
    const optionsPath =
        route.params?.optionsPath ||
        (sellerMode ? "/seller/options" : "/admin/manage/options");

    const [items, setItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [loadingMore, setLoadingMore] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(false);
    const [search, setSearch] = useState("");
    const [optionsMap, setOptionsMap] = useState({
        categories: [],
        brands: [],
        tags: [],
    });

    // Formulaire (création / édition)
    const [formOpen, setFormOpen] = useState(false);
    const [editing, setEditing] = useState(null); // objet en cours d'édition, ou null pour création
    const [form, setForm] = useState({});
    const [saving, setSaving] = useState(false);
    const searchFocus = useFocusBorder();

    const needsOptions = useMemo(
        () => (config?.fields || []).some((f) => typeof f.options === "string"),
        [config],
    );

    const requestRef = useRef(0);

    const fetchPage = useCallback(
        async (nextPage = 1, append = false) => {
            const requestId = ++requestRef.current;
            if (append) {
                setLoadingMore(true);
            } else {
                setLoading(true);
                setLoadingMore(false);
            }

            try {
                const { data } = await api.get(base, {
                    params: {
                        ...(search ? { search } : {}),
                        page: nextPage,
                        per_page: 30,
                    },
                });
                if (requestRef.current !== requestId) return;

                const rows = data.data ?? [];
                setItems((current) => (append ? [...current, ...rows] : rows));
                setPage(nextPage);
                setHasMore(nextPage < (data.meta?.last_page ?? 1));
            } catch (e) {
                if (requestRef.current !== requestId) return;
                if (e?.response?.status === 403) {
                    Alert.alert(
                        "Accès refusé",
                        sellerMode
                            ? "Réservé aux vendeurs."
                            : "Réservé aux administrateurs.",
                    );
                    navigation.goBack();
                } else {
                    Alert.alert("Erreur", apiError(e));
                }
                if (!append) {
                    setItems([]);
                    setHasMore(false);
                }
            } finally {
                if (requestRef.current === requestId) {
                    setLoading(false);
                    setLoadingMore(false);
                }
            }
        },
        [base, search, navigation, sellerMode],
    );

    const loadOptions = useCallback(async () => {
        if (!needsOptions) return;
        try {
            const { data } = await api.get(optionsPath);
            setOptionsMap({
                categories: data.categories ?? [],
                brands: data.brands ?? [],
                tags: data.tags ?? [],
            });
        } catch (e) {
            /* ignore */
        }
    }, [needsOptions, optionsPath]);

    const firstLoadRef = useRef(true);

    // Chargement initial immédiat, puis recherche avec un léger debounce.
    useEffect(() => {
        if (firstLoadRef.current) {
            firstLoadRef.current = false;
            fetchPage(1, false);
            return;
        }
        const t = setTimeout(() => fetchPage(1, false), 350);
        return () => clearTimeout(t);
    }, [fetchPage]);

    useEffect(() => {
        loadOptions();
    }, [loadOptions]);

    const loadMore = useCallback(() => {
        if (loading || loadingMore || !hasMore) return;
        fetchPage(page + 1, true);
    }, [loading, loadingMore, hasMore, page, fetchPage]);

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
            else if (f.type === "images" || f.type === "multiselect")
                initial[f.key] = [];
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
            else if (f.type === "multiselect")
                initial[f.key] = Array.isArray(item[f.key])
                    ? item[f.key].map(String)
                    : [];
            else if (f.type === "image")
                // Image existante : on conserve son URL pour l'aperçu, sans la ré-uploader.
                initial[f.key] = item[f.key] ? { url: item[f.key], existing: true } : null;
            else if (f.type === "images") {
                // Galerie existante : { id, url }. L'id sert au retrait ciblé.
                const gallery = Array.isArray(item[f.key])
                    ? item[f.key].map((img) => ({
                          id: img?.id ?? null,
                          url: img?.url ?? img?.image ?? "",
                          existing: true,
                      }))
                    : [];
                // Compatibilité avec une API qui n'expose encore que `image`.
                const fromLegacy =
                    !Array.isArray(item[f.key]) && item.image
                        ? [{ id: null, url: item.image, existing: true }]
                        : [];
                initial[f.key] = [...gallery, ...fromLegacy].filter((img) => img.url);
            } else initial[f.key] = item[f.key] != null ? String(item[f.key]) : "";
        });
        setForm(initial);
        setEditing(item);
        setFormOpen(true);
    };

    const setField = (key, value) => setForm((f) => ({ ...f, [key]: value }));

    // Ouvre directement le formulaire de création depuis le tableau de bord
    // vendeur (« Ajouter un produit »), sans attendre un appui sur le +.
    const autoCreateRef = useRef(null);
    useEffect(() => {
        const token = route.params?.openCreate;
        if (!token || autoCreateRef.current === token) return;
        autoCreateRef.current = token;
        openCreate();
    }, [route.params?.openCreate]);

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
        setField(key, formImage(result.assets[0]));
    };

    const pickImages = async (key) => {
        const current = Array.isArray(form[key]) ? form[key] : [];
        const remaining = MAX_GALLERY_IMAGES - current.length;
        if (remaining <= 0) {
            Alert.alert(
                "Limite atteinte",
                `Un produit peut contenir au maximum ${MAX_GALLERY_IMAGES} images.`,
            );
            return;
        }

        const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
        if (!perm.granted) {
            Alert.alert("Permission requise", "Autorisez l'accès aux photos.");
            return;
        }
        const result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsMultipleSelection: true,
            selectionLimit: remaining,
            quality: 0.7,
        });
        if (result.canceled) return;

        const additions = (result.assets || [])
            .slice(0, remaining)
            .map((asset, index) => formImage(asset, `_${index}`));

        setForm((f) => ({
            ...f,
            [key]: [...(Array.isArray(f[key]) ? f[key] : []), ...additions],
        }));
    };

    const removeImageAt = (key, index) => {
        setForm((f) => ({
            ...f,
            [key]: (Array.isArray(f[key]) ? f[key] : []).filter(
                (_, i) => i !== index,
            ),
        }));
    };

    const buildPayload = () => {
        const payload = {};
        for (const f of config.fields) {
            if (f.type === "image" || f.type === "images") continue; // géré séparément (multipart)
            const v = form[f.key];
            if (f.type === "switch") {
                payload[f.key] = !!v;
            } else if (f.type === "multiselect") {
                payload[f.key] = (Array.isArray(v) ? v : []).map(String);
            } else if (f.type === "number") {
                if (v !== "" && v != null) payload[f.key] = Number(v);
                else if (editing && !f.required) payload[f.key] = null;
            } else {
                const s = typeof v === "string" ? v.trim() : v;
                if (s !== "" && s != null) {
                    payload[f.key] = s;
                } else if (
                    editing &&
                    !f.required &&
                    !f.createRequired &&
                    f.type !== "password"
                ) {
                    // Permet de réellement vider un champ optionnel en édition.
                    // Les colonnes texte NOT NULL (ex. description produit)
                    // doivent recevoir une chaîne vide, pas null.
                    payload[f.key] =
                        f.type === "select" || f.type === "date" ? null : "";
                }
            }
        }
        return payload;
    };

    // Retourne les champs image qui contiennent une NOUVELLE image sélectionnée.
    const pickedImageFields = () =>
        config.fields.filter(
            (f) => f.type === "image" && form[f.key] && form[f.key].uri && !form[f.key].existing,
        );

    // Champs image existants que l'utilisateur a retirés dans le formulaire.
    const removedImageFields = () =>
        editing
            ? config.fields.filter(
                  (f) => f.type === "image" && editing[f.key] && !form[f.key],
              )
            : [];

    // Nouvelles images de galerie sélectionnées (pas encore envoyées).
    const hasNewGalleryImages = () =>
        config.fields.some(
            (f) =>
                f.type === "images" &&
                (form[f.key] || []).some((img) => img?.uri && !img.existing),
        );

    // Identifiants des photos existantes retirées de la galerie.
    const removedGalleryImageIds = () => {
        if (!editing) return [];
        const removed = [];
        config.fields
            .filter((f) => f.type === "images")
            .forEach((f) => {
                const keptIds = new Set(
                    (Array.isArray(form[f.key]) ? form[f.key] : [])
                        .filter((img) => img?.existing && img.id != null)
                        .map((img) => String(img.id)),
                );
                (Array.isArray(editing[f.key]) ? editing[f.key] : []).forEach(
                    (img) => {
                        if (img?.id != null && !keptIds.has(String(img.id))) {
                            removed.push(img.id);
                        }
                    },
                );
            });
        return removed;
    };

    const buildFormData = () => {
        const fd = new FormData();
        for (const f of config.fields) {
            if (f.type === "image" || f.type === "images") continue;
            const v = form[f.key];
            if (f.type === "switch") {
                fd.append(f.key, v ? "1" : "0");
            } else if (f.type === "multiselect") {
                // Laravel attend tags[] pour reconstruire un tableau.
                // Le drapeau `_present` distingue « aucun tag » d'un champ
                // absent, sinon un tableau vide ne peut pas vider la sélection.
                fd.append(`${f.key}_present`, "1");
                (Array.isArray(v) ? v : []).forEach((item) =>
                    fd.append(`${f.key}[]`, String(item)),
                );
            } else if (f.type === "number") {
                if (v !== "" && v != null) fd.append(f.key, String(v));
                else if (editing && !f.required) fd.append(f.key, "");
            } else {
                const s = typeof v === "string" ? v.trim() : v;
                if (s !== "" && s != null) fd.append(f.key, String(s));
                else if (
                    editing &&
                    !f.required &&
                    !f.createRequired &&
                    f.type !== "password"
                ) {
                    // Champ vidé : Laravel le convertit en null, ce qui permet
                    // de l'effacer (sauf description produit, normalisée serveur).
                    fd.append(f.key, "");
                }
            }
        }
        pickedImageFields().forEach((f) => {
            appendImagePart(fd, f.key, form[f.key]);
        });

        // Galerie produit : chaque fichier est envoyé sous `images[]`.
        config.fields
            .filter((f) => f.type === "images")
            .forEach((f) => {
                (form[f.key] || [])
                    .filter((img) => img?.uri && !img.existing)
                    .forEach((img) => appendImagePart(fd, "images[]", img));
            });

        const removedGalleryIds = removedGalleryImageIds();
        removedGalleryIds.forEach((id) => {
            fd.append("remove_images[]", String(id));
        });

        // Image unique retirée (autres ressources que les produits).
        if (removedImageFields().length > 0) {
            fd.append("remove_image", "1");
        }

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
            if (f.type === "images") {
                if (!Array.isArray(v) || v.length === 0) {
                    Alert.alert("Images requises", `Ajoutez au moins une image pour « ${f.label} ».`);
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
            const hasImageChanges =
                pickedImageFields().length > 0 ||
                removedImageFields().length > 0 ||
                hasNewGalleryImages() ||
                removedGalleryImageIds().length > 0;

            if (hasImageChanges) {
                // Envoi multipart (images + champs). PUT est simulé via _method
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
            fetchPage(1, false);
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
                            fetchPage(1, false);
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
        const hasImageField = config.fields.some(
            (f) => f.type === "image" || f.type === "images",
        );
        const thumb = item.image || item.images?.[0]?.url || null;
        return (
            <View style={styles.row}>
                {hasImageField ? (
                    thumb ? (
                        <View>
                            <Image source={{ uri: thumb }} style={styles.rowThumb} />
                            {Array.isArray(item.images) && item.images.length > 1 ? (
                                <View style={styles.rowThumbCount}>
                                    <Text style={styles.rowThumbCountText}>
                                        {item.images.length}
                                    </Text>
                                </View>
                            ) : null}
                        </View>
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

        if (f.type === "multiselect") {
            const selected = new Set(
                (Array.isArray(value) ? value : []).map(String),
            );
            const toggle = (optionValue) => {
                const next = new Set(selected);
                const key = String(optionValue);
                if (next.has(key)) next.delete(key);
                else next.add(key);
                setField(f.key, Array.from(next));
            };
            return (
                <View key={f.key} style={{ marginTop: 12 }}>
                    <Text style={styles.fieldLabel}>{f.label}</Text>
                    {f.hint ? <Text style={styles.hint}>{f.hint}</Text> : null}
                    <View style={styles.multiSelect}>
                        {optionsFor(f).map((opt) => {
                            const on = selected.has(String(opt.value));
                            return (
                                <TouchableOpacity
                                    key={String(opt.value)}
                                    style={[styles.chip, on && styles.chipOn]}
                                    onPress={() => toggle(opt.value)}
                                    activeOpacity={0.8}
                                >
                                    <Text
                                        style={[
                                            styles.chipText,
                                            on && styles.chipTextOn,
                                        ]}
                                    >
                                        {opt.label}
                                    </Text>
                                </TouchableOpacity>
                            );
                        })}
                    </View>
                </View>
            );
        }

        if (f.type === "images") {
            const gallery = Array.isArray(value) ? value : [];
            return (
                <View key={f.key} style={{ marginTop: 12 }}>
                    <Text style={styles.fieldLabel}>
                        {f.label}
                        {f.required || (f.createRequired && !editing) ? " *" : ""}
                    </Text>
                    {f.hint ? <Text style={styles.galleryHint}>{f.hint}</Text> : null}
                    <View style={styles.gallery}>
                        {gallery.map((img, index) => (
                            <View
                                key={`${img.id ?? img.uri ?? img.url}-${index}`}
                                style={styles.galleryItem}
                            >
                                <Image
                                    source={{ uri: img.uri || img.url }}
                                    style={styles.galleryImage}
                                />
                                {index === 0 ? (
                                    <View style={styles.galleryPrimary}>
                                        <Text style={styles.galleryPrimaryText}>
                                            Principale
                                        </Text>
                                    </View>
                                ) : null}
                                <TouchableOpacity
                                    style={styles.galleryRemove}
                                    onPress={() => removeImageAt(f.key, index)}
                                    hitSlop={6}
                                >
                                    <Ionicons name="close" size={16} color="#fff" />
                                </TouchableOpacity>
                            </View>
                        ))}
                        {gallery.length < MAX_GALLERY_IMAGES ? (
                            <TouchableOpacity
                                style={styles.galleryAdd}
                                onPress={() => pickImages(f.key)}
                                activeOpacity={0.8}
                            >
                                <Ionicons
                                    name="images-outline"
                                    size={26}
                                    color={COLORS.primaryDark}
                                />
                                <Text style={styles.galleryAddText}>Ajouter</Text>
                            </TouchableOpacity>
                        ) : null}
                    </View>
                    <Text style={styles.galleryCount}>
                        {gallery.length} / {MAX_GALLERY_IMAGES} images
                    </Text>
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
            const options = optionsFor(f).filter(
                (opt) =>
                    !(
                        editing &&
                        f.key === "parent_id" &&
                        String(opt.value) === String(editing.id)
                    ),
            );
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
                <FocusTextInput
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

            <Animated.View
                style={[styles.searchWrap, { borderColor: searchFocus.borderColor }]}
            >
                <Ionicons name="search" size={18} color="#9ca3af" />
                <TextInput
                    style={[styles.searchInput, webInputStyle]}
                    placeholder="Rechercher..."
                    placeholderTextColor="#9ca3af"
                    value={search}
                    onChangeText={setSearch}
                    onFocus={searchFocus.onFocus}
                    onBlur={searchFocus.onBlur}
                />
            </Animated.View>

            {loading ? (
                <View style={styles.center}>
                    <ActivityIndicator size="large" color={COLORS.primary} />
                </View>
            ) : (
                <FlatList
                    data={items}
                    keyExtractor={(it) => String(it.id)}
                    renderItem={renderItem}
                    onEndReached={loadMore}
                    onEndReachedThreshold={0.4}
                    contentContainerStyle={{ padding: 12, paddingBottom: 30 }}
                    ListFooterComponent={
                        loadingMore ? (
                            <ActivityIndicator
                                style={{ paddingVertical: 16 }}
                                color={COLORS.primary}
                            />
                        ) : null
                    }
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
    gallery: { flexDirection: "row", flexWrap: "wrap", gap: 10 },
    galleryItem: {
        width: 96,
        height: 96,
        borderRadius: RADIUS.md,
        overflow: "hidden",
        backgroundColor: "#f3f4f6",
        borderWidth: 1,
        borderColor: "#e5e7eb",
    },
    galleryImage: { width: "100%", height: "100%" },
    galleryRemove: {
        position: "absolute",
        top: 4,
        right: 4,
        width: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: "rgba(0,0,0,0.62)",
        alignItems: "center",
        justifyContent: "center",
    },
    galleryPrimary: {
        position: "absolute",
        left: 4,
        bottom: 4,
        borderRadius: 6,
        paddingHorizontal: 6,
        paddingVertical: 2,
        backgroundColor: "rgba(0,0,0,0.62)",
    },
    galleryPrimaryText: { color: "#fff", fontSize: 9.5, fontWeight: "700" },
    galleryAdd: {
        width: 96,
        height: 96,
        borderRadius: RADIUS.md,
        borderWidth: 1,
        borderStyle: "dashed",
        borderColor: "#d1d5db",
        backgroundColor: "#f9fafb",
        alignItems: "center",
        justifyContent: "center",
        gap: 4,
    },
    galleryAddText: { fontSize: 11.5, fontWeight: "700", color: COLORS.primaryDark },
    galleryHint: { fontSize: 11.5, color: COLORS.textLight, marginBottom: 8 },
    galleryCount: { fontSize: 11.5, color: COLORS.textLight, marginTop: 6 },
    rowThumb: { width: 42, height: 42, borderRadius: 8, backgroundColor: "#f3f4f6" },
    rowThumbCount: {
        position: "absolute",
        right: -4,
        bottom: -4,
        minWidth: 18,
        height: 18,
        borderRadius: 9,
        paddingHorizontal: 4,
        backgroundColor: COLORS.primaryDark,
        alignItems: "center",
        justifyContent: "center",
        borderWidth: 1.5,
        borderColor: "#fff",
    },
    rowThumbCountText: { color: "#fff", fontSize: 9.5, fontWeight: "800" },
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
    multiSelect: {
        flexDirection: "row",
        flexWrap: "wrap",
        gap: 8,
        paddingVertical: 4,
    },
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
