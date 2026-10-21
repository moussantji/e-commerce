import React, { useRef } from "react";
import { Animated, Platform, TextInput } from "react-native";
import { Ionicons } from "@expo/vector-icons";

export const FOCUS_BORDER_IDLE = "#e5e7eb";
export const FOCUS_BORDER_ACTIVE = "rgba(118, 75, 162, 0.55)";

// `Animated.TextInput` n'est pas exporté par React Native / React Native Web.
const AnimatedTextInput = Animated.createAnimatedComponent(TextInput);

/**
 * Animation de focus commune à tous les champs de l'application.
 * Le contour passe du gris neutre à un violet léger en 180 ms.
 */
export function useFocusBorder() {
    const focus = useRef(new Animated.Value(0)).current;

    const animate = (toValue) => {
        Animated.timing(focus, {
            toValue,
            duration: 180,
            useNativeDriver: false,
        }).start();
    };

    return {
        borderColor: focus.interpolate({
            inputRange: [0, 1],
            outputRange: [FOCUS_BORDER_IDLE, FOCUS_BORDER_ACTIVE],
        }),
        onFocus: () => animate(1),
        onBlur: () => animate(0),
    };
}

/** Supprime l'outline bleu natif du navigateur sur les champs web. */
export const webInputStyle =
    Platform.OS === "web"
        ? { outlineStyle: "none", outlineWidth: 0, outlineColor: "transparent" }
        : null;

/**
 * TextInput avec bordure animée. À utiliser pour les champs qui portent déjà
 * leur propre bordure (styles.input, styles.payInput...).
 */
export default function FocusTextInput({
    style,
    onFocus,
    onBlur,
    ...inputProps
}) {
    const focus = useFocusBorder();

    return (
        <AnimatedTextInput
            {...inputProps}
            onFocus={(event) => {
                focus.onFocus();
                onFocus?.(event);
            }}
            onBlur={(event) => {
                focus.onBlur();
                onBlur?.(event);
            }}
            style={[style, webInputStyle, { borderColor: focus.borderColor }]}
        />
    );
}

/**
 * Champ avec icône dont la bordure du conteneur est animée. Utile pour les
 * formulaires où l'icône et le champ partagent le même cadre.
 */
export function FocusField({
    icon,
    iconColor = "#9ca3af",
    rightElement,
    containerStyle,
    inputStyle,
    onFocus,
    onBlur,
    ...inputProps
}) {
    const focus = useFocusBorder();

    return (
        <Animated.View style={[containerStyle, { borderColor: focus.borderColor }]}>
            {icon ? <Ionicons name={icon} size={18} color={iconColor} /> : null}
            <TextInput
                {...inputProps}
                onFocus={(event) => {
                    focus.onFocus();
                    onFocus?.(event);
                }}
                onBlur={(event) => {
                    focus.onBlur();
                    onBlur?.(event);
                }}
                style={[inputStyle, webInputStyle]}
            />
            {rightElement}
        </Animated.View>
    );
}
