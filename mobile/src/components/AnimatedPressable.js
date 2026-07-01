import React, { useRef } from "react";
import { Animated, Pressable } from "react-native";

/**
 * Bouton/carte tactile avec animation d'appui (léger "scale down" au toucher).
 * Remplace TouchableOpacity pour un retour tactile plus vivant.
 */
export default function AnimatedPressable({
    children,
    style,
    onPress,
    onLongPress,
    scaleTo = 0.96,
    disabled = false,
    ...rest
}) {
    const scale = useRef(new Animated.Value(1)).current;

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
            <Animated.View style={[style, { transform: [{ scale }] }]}>
                {children}
            </Animated.View>
        </Pressable>
    );
}
