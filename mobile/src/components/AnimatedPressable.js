import React, { useEffect, useRef } from "react";
import { Animated, Pressable } from "react-native";

/**
 * Bouton/carte tactile animé :
 *  - effet d'appui ("scale down" au toucher)
 *  - (optionnel) entrée en fondu + glissement quand on passe `index`
 *    → idéal pour animer des éléments de grille (FlatList numColumns) SANS
 *      casser la mise en page, car l'animation est appliquée sur la vue qui
 *      porte déjà le `style` de la cellule.
 */
export default function AnimatedPressable({
    children,
    style,
    onPress,
    onLongPress,
    scaleTo = 0.96,
    disabled = false,
    index = null,
    enterOffset = 16,
    enterDuration = 420,
    ...rest
}) {
    const animateEnter = index != null;

    const scale = useRef(new Animated.Value(1)).current;
    const opacity = useRef(new Animated.Value(animateEnter ? 0 : 1)).current;
    const translateY = useRef(
        new Animated.Value(animateEnter ? enterOffset : 0),
    ).current;

    useEffect(() => {
        if (!animateEnter) return;
        const delay = Math.min(index, 12) * 55;
        Animated.parallel([
            Animated.timing(opacity, {
                toValue: 1,
                duration: enterDuration,
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
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const animateTo = (value) =>
        Animated.spring(scale, {
            toValue: value,
            friction: 6,
            tension: 140,
            useNativeDriver: true,
        }).start();

    return (
        <Pressable
            onPressIn={() => !disabled && animateTo(scaleTo)}
            onPressOut={() => !disabled && animateTo(1)}
            onPress={onPress}
            onLongPress={onLongPress}
            disabled={disabled}
            {...rest}
        >
            <Animated.View
                style={[
                    style,
                    { opacity, transform: [{ translateY }, { scale }] },
                ]}
            >
                {children}
            </Animated.View>
        </Pressable>
    );
}
