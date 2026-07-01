import React from "react";
import { ImageBackground, StyleSheet, View } from "react-native";
import { LinearGradient } from "expo-linear-gradient";
import { COLORS } from "../theme";

/**
 * En-tete hero avec image de fond + voile violet, similaire au Login.
 * Utilisation :
 *   <ScreenHeroHeader image="https://...jpg">
 *       <Text>Mon titre</Text>
 *   </ScreenHeroHeader>
 *
 * Props :
 *  - image : URL (string) ou require(...) d'image locale
 *  - height : hauteur du header (defaut 180)
 *  - overlayOpacity : opacite du voile violet (defaut "medium")
 *  - children : contenu affiche par dessus (titre, icones, etc.)
 */

const OVERLAY_PRESETS = {
    light: ["rgba(102,126,234,0.4)", "rgba(118,75,162,0.65)"],
    medium: ["rgba(102,126,234,0.55)", "rgba(118,75,162,0.82)"],
    heavy: ["rgba(102,126,234,0.7)", "rgba(118,75,162,0.92)"],
};

export default function ScreenHeroHeader({
    image,
    height = 180,
    overlayOpacity = "medium",
    style,
    children,
}) {
    const overlayColors = OVERLAY_PRESETS[overlayOpacity] || OVERLAY_PRESETS.medium;

    if (image) {
        const source = typeof image === "string" ? { uri: image } : image;
        return (
            <ImageBackground
                source={source}
                style={[styles.container, { height }, style]}
                resizeMode="cover"
            >
                <LinearGradient
                    colors={overlayColors}
                    start={{ x: 0, y: 0 }}
                    end={{ x: 1, y: 1 }}
                    style={StyleSheet.absoluteFill}
                />
                <View style={styles.content}>{children}</View>
            </ImageBackground>
        );
    }

    // Fallback: gradient simple sans image
    return (
        <LinearGradient
            colors={COLORS.gradient}
            start={COLORS.gradientStart}
            end={COLORS.gradientEnd}
            style={[styles.container, { height }, style]}
        >
            <View style={styles.content}>{children}</View>
        </LinearGradient>
    );
}

const styles = StyleSheet.create({
    container: {
        width: "100%",
        overflow: "hidden",
    },
    content: {
        flex: 1,
        justifyContent: "flex-end",
        paddingHorizontal: 16,
        paddingBottom: 16,
    },
});
