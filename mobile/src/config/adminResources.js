/**
 * Configuration déclarative des ressources gérables par l'administrateur
 * dans l'app mobile. Utilisée par AdminHubScreen (menu) et AdminManageScreen
 * (liste + formulaire générique de création/édition).
 *
 * Types de champs supportés : text, textarea, number, email, password,
 * switch, select, date.
 *   - select.options : tableau statique [{ value, label }] OU une clé
 *     dynamique ("categories" | "brands") chargée via /admin/manage/options.
 *   - createRequired : champ requis uniquement à la création (ex : mot de passe).
 */

export const ADMIN_RESOURCES = [
    {
        key: "products",
        title: "Produits",
        icon: "cube-outline",
        primary: (it) => it.name,
        subtitle: (it) =>
            `${it.price != null ? it.price + " FCFA" : ""}${
                it.stock != null ? ` · stock ${it.stock}` : ""
            }${it.category ? ` · ${it.category}` : ""}`,
        fields: [
            { key: "image", label: "Image du produit", type: "image" },
            { key: "name", label: "Nom", type: "text", required: true },
            { key: "description", label: "Description", type: "textarea" },
            { key: "price", label: "Prix (FCFA)", type: "number", required: true },
            { key: "sale_price", label: "Prix promo (FCFA)", type: "number" },
            { key: "stock", label: "Stock", type: "number" },
            { key: "category_id", label: "Catégorie", type: "select", options: "categories" },
            { key: "brand_id", label: "Marque", type: "select", options: "brands" },
            { key: "is_active", label: "Actif", type: "switch", default: true },
        ],
    },
    {
        key: "categories",
        title: "Catégories",
        icon: "grid-outline",
        primary: (it) => it.name,
        subtitle: (it) => (it.is_active ? "Active" : "Inactive"),
        fields: [
            { key: "image", label: "Image de la catégorie", type: "image" },
            { key: "name", label: "Nom", type: "text", required: true },
            { key: "description", label: "Description", type: "textarea" },
            { key: "parent_id", label: "Catégorie parente", type: "select", options: "categories" },
            { key: "is_active", label: "Active", type: "switch", default: true },
        ],
    },
    {
        key: "caracteristiques",
        title: "Caractéristiques",
        icon: "options-outline",
        primary: (it) => it.name,
        subtitle: (it) =>
            `${it.type || ""}${it.unite ? ` (${it.unite})` : ""}${
                it.is_filterable ? " · filtrable" : ""
            }`,
        fields: [
            { key: "name", label: "Nom", type: "text", required: true },
            { key: "type", label: "Type (ex : couleur, taille)", type: "text" },
            { key: "unite", label: "Unité (ex : cm, kg)", type: "text" },
            { key: "is_filterable", label: "Filtrable", type: "switch", default: false },
        ],
    },
    {
        key: "brands",
        title: "Marques",
        icon: "pricetags-outline",
        primary: (it) => it.name,
        subtitle: (it) => (it.is_active ? "Active" : "Inactive"),
        fields: [
            { key: "name", label: "Nom", type: "text", required: true },
            { key: "description", label: "Description", type: "textarea" },
            { key: "website", label: "Site web", type: "text" },
            { key: "is_active", label: "Active", type: "switch", default: true },
        ],
    },
    {
        key: "tags",
        title: "Tags",
        icon: "bookmark-outline",
        primary: (it) => it.name,
        subtitle: (it) => it.description || "",
        fields: [
            { key: "name", label: "Nom", type: "text", required: true },
            { key: "description", label: "Description", type: "textarea" },
        ],
    },
    {
        key: "shipping-methods",
        title: "Méthodes de livraison",
        icon: "car-outline",
        primary: (it) => it.method_name,
        subtitle: (it) =>
            `${it.price != null ? it.price + " FCFA" : ""}${
                it.delivery_time ? ` · ${it.delivery_time}` : ""
            }`,
        fields: [
            { key: "image", label: "Logo", type: "image" },
            { key: "method_name", label: "Nom", type: "text", required: true },
            { key: "price", label: "Prix (FCFA)", type: "number", required: true },
            { key: "delivery_time_min", label: "Délai min. (jours)", type: "number" },
            { key: "delivery_time_max", label: "Délai max. (jours)", type: "number" },
            { key: "description", label: "Description", type: "textarea" },
            { key: "is_active", label: "Active", type: "switch", default: true },
        ],
    },
    {
        key: "payment-methods",
        title: "Méthodes de paiement",
        icon: "card-outline",
        primary: (it) => it.method_name,
        subtitle: (it) =>
            `${it.provider_name || ""}${it.is_active ? "" : " · inactive"}`,
        fields: [
            { key: "image", label: "Logo", type: "image" },
            { key: "method_name", label: "Nom", type: "text", required: true },
            { key: "provider_name", label: "Fournisseur (ex : Orange, Wave)", type: "text" },
            { key: "account_number", label: "Numéro du compte", type: "text" },
            { key: "instructions", label: "Instructions", type: "textarea" },
            { key: "description", label: "Description", type: "textarea" },
            { key: "is_active", label: "Active", type: "switch", default: true },
        ],
    },
    {
        key: "coupons",
        title: "Coupons",
        icon: "ribbon-outline",
        primary: (it) => it.code,
        subtitle: (it) =>
            `${it.type === "percentage" ? it.value + " %" : it.value + " FCFA"}${
                it.expires_at ? ` · exp. ${it.expires_at}` : ""
            }`,
        fields: [
            { key: "code", label: "Code", type: "text", required: true, autoCapitalize: "characters" },
            {
                key: "type",
                label: "Type de remise",
                type: "select",
                default: "percentage",
                options: [
                    { value: "percentage", label: "Pourcentage (%)" },
                    { value: "fixed", label: "Montant fixe (FCFA)" },
                ],
            },
            { key: "value", label: "Valeur", type: "number", required: true },
            { key: "starts_at", label: "Début (AAAA-MM-JJ)", type: "date" },
            { key: "expires_at", label: "Expiration (AAAA-MM-JJ)", type: "date" },
            { key: "usage_limit", label: "Limite d'utilisation", type: "number" },
            { key: "is_active", label: "Actif", type: "switch", default: true },
        ],
    },
    {
        key: "users",
        title: "Utilisateurs",
        icon: "people-outline",
        primary: (it) => it.name,
        subtitle: (it) => `${it.email || ""} · ${it.role || "customer"}`,
        fields: [
            { key: "name", label: "Nom", type: "text", required: true },
            { key: "email", label: "Email", type: "email", required: true },
            { key: "tel", label: "Téléphone", type: "text" },
            {
                key: "role",
                label: "Rôle",
                type: "select",
                default: "customer",
                options: [
                    { value: "customer", label: "Client" },
                    { value: "admin", label: "Administrateur" },
                ],
            },
            {
                key: "status",
                label: "Statut",
                type: "select",
                default: "active",
                options: [
                    { value: "active", label: "Actif" },
                    { value: "inactive", label: "Inactif" },
                ],
            },
            {
                key: "password",
                label: "Mot de passe",
                type: "password",
                createRequired: true,
                hint: "Laisser vide pour ne pas changer",
            },
        ],
    },
];

export function getAdminResource(key) {
    return ADMIN_RESOURCES.find((r) => r.key === key);
}
