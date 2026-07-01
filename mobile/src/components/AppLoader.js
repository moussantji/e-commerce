import React, { useEffect, useRef } from "react";
import { View, Image, Animated, ActivityIndicator, StyleSheet } from "react-native";
import { COLORS } from "../theme";

/**
 * Écran de chargement de l'application affichant le logo Maden Baoubab.
 *
 * Le logo doit être placé dans : mobile/assets/logo.png
 * (voir mobile/assets/README.md)
 */
export default function AppLoader() {
    const fade = useRef(new Animated.Value(0)).current;
    const scale = useRef(new Animated.Value(0.9)).current;

    useEffect(() => {
        Animated.parallel([
            Animated.timing(fade, {
                toValue: 1,
                duration: 600,
                useNativeDriver: true,
            }),
            Animated.spring(scale, {
                toValue: 1,
                friction: 6,
                tension: 40,
                useNativeDriver: true,
            }),
        ]).start();
    }, [fade, scale]);

    return (
        <View style={styles.container}>
            <Animated.Image
                source={require("../../assets/logo.png")}
                style={[styles.logo, { opacity: fade, transform: [{ scale }] }]}
                resizeMode="contain"
            />
            <ActivityIndicator
                size="small"
                color={COLORS.primaryDark ?? COLORS.primary}
                style={styles.spinner}
            />
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: "#ffffff",
        justifyContent: "center",
        alignItems: "center",
        paddingHorizontal: 32,
    },
    logo: {
        width: 260,
        height: 170,
    },
    spinner: {
        marginTop: 28,
    },
});
