import React from "react";
import { ImageBackground, StyleSheet, View } from "react-native";
import { LinearGradient } from "expo-linear-gradient";
import { LOGIN_BG_IMAGE } from "../config";
import { COLORS } from "../theme";

/**
 * Fond des écrans Connexion / Inscription.
 * - Si LOGIN_BG_IMAGE est défini (URL ou require local) → image en fond + voile orange.
 * - Sinon → dégradé orange.
 *
 * 👉 Pour une VIDÉO en fond : installez `expo-av` puis remplacez l'ImageBackground
 *    par <Video> (voir mobile/README.md, section "Fond vidéo").
 */
export default function AuthBackground({ children }) {
    if (LOGIN_BG_IMAGE) {
        const source =
            typeof LOGIN_BG_IMAGE === "string"
                ? { uri: LOGIN_BG_IMAGE }
                : LOGIN_BG_IMAGE;
        return (
            <ImageBackground
                source={source}
                style={styles.fill}
                resizeMode="cover"
            >
                {/* Voile violet semi-transparent : garde le texte/la carte lisibles */}
                <LinearGradient
                    colors={["rgba(102,126,234,0.55)", "rgba(118,75,162,0.85)"]}
                    style={StyleSheet.absoluteFill}
                />
                <View style={styles.fill}>{children}</View>
            </ImageBackground>
        );
    }

    return (
        <LinearGradient
            colors={COLORS.gradient}
            start={COLORS.gradientStart}
            end={COLORS.gradientEnd}
            style={styles.fill}
        >
            {children}
        </LinearGradient>
    );
}

const styles = StyleSheet.create({
    fill: { flex: 1 },
});
