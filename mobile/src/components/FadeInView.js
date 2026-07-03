import React, { useEffect, useRef } from "react";
import { Animated } from "react-native";

/**
 * Enveloppe animée : fait apparaître son contenu en fondu + léger glissement
 * vertical au montage. Idéal pour animer l'entrée d'écrans ou d'éléments de
 * liste (avec un `delay` échelonné selon l'index).
 */
export default function FadeInView({
    children,
    delay = 0,
    duration = 420,
    offset = 14,
    style,
}) {
    const opacity = useRef(new Animated.Value(0)).current;
    const translateY = useRef(new Animated.Value(offset)).current;

    useEffect(() => {
        Animated.parallel([
            Animated.timing(opacity, {
                toValue: 1,
                duration,
                delay,
                useNativeDriver: true,
            }),
            Animated.spring(translateY, {
                toValue: 0,
                delay,
                friction: 7,
                tension: 60,
                useNativeDriver: true,
            }),
        ]).start();
    }, [opacity, translateY, delay, duration]);

    return (
        <Animated.View style={[style, { opacity, transform: [{ translateY }] }]}>
            {children}
        </Animated.View>
    );
}
