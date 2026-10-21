import React from "react";
import { Animated, StyleSheet, TextInput } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { COLORS } from "../theme";
import { useFocusBorder, webInputStyle } from "./FocusTextInput";

/**
 * Champ des écrans Connexion / Inscription.
 *
 * Le contour du champ est animé au focus : gris neutre → violet léger.
 * Sur web, on retire aussi l'outline bleu natif du navigateur.
 */
export default function AuthInput({
    icon,
    rightElement,
    onFocus,
    onBlur,
    style,
    ...inputProps
}) {
    const focus = useFocusBorder();

    return (
        <Animated.View style={[styles.wrap, { borderColor: focus.borderColor }]}>
            <Ionicons name={icon} size={18} color="#9ca3af" />
            <TextInput
                {...inputProps}
                style={[styles.input, webInputStyle, style]}
                placeholderTextColor={COLORS.muted}
                onFocus={(event) => {
                    focus.onFocus();
                    onFocus?.(event);
                }}
                onBlur={(event) => {
                    focus.onBlur();
                    onBlur?.(event);
                }}
            />
            {rightElement}
        </Animated.View>
    );
}

const styles = StyleSheet.create({
    wrap: {
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        borderWidth: 1,
        borderRadius: 12,
        paddingHorizontal: 14,
        marginBottom: 14,
        backgroundColor: "#f9fafb",
    },
    input: {
        flex: 1,
        paddingVertical: 14,
        fontSize: 15,
        color: COLORS.text,
    },
});
