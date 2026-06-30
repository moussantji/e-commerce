import React from "react";
import { StyleSheet } from "react-native";
import { LinearGradient } from "expo-linear-gradient";

/**
 * Thème central de l'application.
 * Couleur principale demandée : linear-gradient(135deg, #667eea 0%, #764ba2 100%)
 * Pour changer l'identité visuelle de toute l'app, modifiez uniquement ce fichier.
 */
export const COLORS = {
    primary: "#667eea",
    primaryDark: "#764ba2",
    // Dégradé "135deg" : start = haut-gauche, end = bas-droite
    gradient: ["#667eea", "#764ba2"],
    gradientStart: { x: 0, y: 0 },
    gradientEnd: { x: 1, y: 1 },

    accent: "#ff5b78",
    badge: "#ff3b5c",
    success: "#22c55e",

    bg: "#f3f4f6",
    card: "#ffffff",
    text: "#111827",
    textLight: "#6b7280",
    muted: "#9ca3af",
    border: "#eaeaf2",

    soft: "#f0eefc", // violet très clair (fonds d'icônes)
    softBorder: "#e2dcfb",
    star: "#ffb800",
    white: "#ffffff",
};

/** Fond dégradé réutilisable (en-têtes de navigation, bannières...). */
export function GradientBackground({ style }) {
    return (
        <LinearGradient
            colors={COLORS.gradient}
            start={COLORS.gradientStart}
            end={COLORS.gradientEnd}
            style={[StyleSheet.absoluteFill, style]}
        />
    );
}

export const RADIUS = { sm: 8, md: 14, lg: 20, xl: 28, pill: 999 };
