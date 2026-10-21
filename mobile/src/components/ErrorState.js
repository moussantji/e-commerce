import React from "react";
import { View, Text, StyleSheet } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import AnimatedPressable from "./AnimatedPressable";
import { COLORS, RADIUS } from "../theme";

/**
 * État d'erreur commun à tous les écrans.
 *
 * Affiche un message compréhensible (jamais une erreur technique brute) et,
 * si un callback est fourni, un bouton « Réessayer ».
 */
export default function ErrorState({
    title,
    message,
    onRetry,
    retryLabel = "Réessayer",
    onSecondary,
    secondaryLabel,
    icon,
    compact = false,
    fullScreen = false,
    style,
}) {
    const insets = useSafeAreaInsets();
    const text = `${title || ""} ${message || ""}`;
    const offline = /connexion|r[ée]seau|internet|hors ligne/i.test(text);
    const finalIcon = icon || (offline ? "cloud-offline-outline" : "alert-circle-outline");
    const finalTitle =
        title || (offline ? "Pas de connexion" : "Oups, un problème est survenu");

    return (
        <View
            style={[
                styles.wrap,
                compact && styles.compact,
                fullScreen && {
                    paddingTop: Math.max(insets.top, 28),
                    paddingBottom: Math.max(insets.bottom, 28),
                },
                style,
            ]}
        >
            <View style={styles.halo}>
                <View style={styles.iconCircle}>
                    <Ionicons name={finalIcon} size={compact ? 28 : 34} color={COLORS.primaryDark} />
                </View>
            </View>

            <Text style={[styles.title, compact && styles.titleCompact]}>{finalTitle}</Text>
            {message ? <Text style={styles.message}>{message}</Text> : null}

            {onRetry ? (
                <AnimatedPressable
                    style={styles.retry}
                    scaleTo={0.97}
                    onPress={onRetry}
                    accessibilityRole="button"
                    accessibilityLabel={retryLabel}
                >
                    <LinearGradient
                        colors={COLORS.gradient}
                        start={COLORS.gradientStart}
                        end={COLORS.gradientEnd}
                        style={styles.retryGradient}
                    >
                        <Ionicons name="refresh" size={17} color="#fff" />
                        <Text style={styles.retryText}>{retryLabel}</Text>
                    </LinearGradient>
                </AnimatedPressable>
            ) : null}

            {onSecondary && secondaryLabel ? (
                <AnimatedPressable
                    style={styles.secondary}
                    scaleTo={0.97}
                    onPress={onSecondary}
                    accessibilityRole="button"
                    accessibilityLabel={secondaryLabel}
                >
                    <Text style={styles.secondaryText}>{secondaryLabel}</Text>
                </AnimatedPressable>
            ) : null}
        </View>
    );
}

const styles = StyleSheet.create({
    wrap: {
        flexGrow: 1,
        alignItems: "center",
        justifyContent: "center",
        paddingHorizontal: 28,
        paddingVertical: 32,
        backgroundColor: COLORS.bg,
    },
    compact: {
        flexGrow: 0,
        paddingVertical: 24,
        backgroundColor: "transparent",
    },
    halo: {
        width: 108,
        height: 108,
        borderRadius: 54,
        backgroundColor: COLORS.soft,
        alignItems: "center",
        justifyContent: "center",
        marginBottom: 18,
    },
    iconCircle: {
        width: 76,
        height: 76,
        borderRadius: 38,
        backgroundColor: "#fff",
        alignItems: "center",
        justifyContent: "center",
        elevation: 2,
        shadowColor: "#4c1d95",
        shadowOpacity: 0.12,
        shadowRadius: 10,
        shadowOffset: { width: 0, height: 4 },
    },
    title: {
        fontSize: 19,
        fontWeight: "800",
        color: COLORS.text,
        textAlign: "center",
    },
    titleCompact: { fontSize: 17 },
    message: {
        fontSize: 14,
        lineHeight: 21,
        color: COLORS.textLight,
        textAlign: "center",
        marginTop: 8,
        maxWidth: 330,
    },
    retry: {
        marginTop: 22,
        borderRadius: RADIUS.pill,
        overflow: "hidden",
        minWidth: 170,
    },
    retryGradient: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        gap: 8,
        paddingVertical: 13,
        paddingHorizontal: 26,
    },
    retryText: { color: "#fff", fontWeight: "800", fontSize: 14.5 },
    secondary: { marginTop: 14, paddingVertical: 8, paddingHorizontal: 12 },
    secondaryText: {
        color: COLORS.primaryDark,
        fontWeight: "700",
        fontSize: 13.5,
        textAlign: "center",
    },
});
