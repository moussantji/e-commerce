import React from "react";
import { View, StyleSheet } from "react-native";
import { StatusBar } from "expo-status-bar";
import { reloadAppAsync } from "expo";
import ErrorState from "./ErrorState";
import { COLORS } from "../theme";

/**
 * Filet de sécurité global : si un écran plante, on affiche une page d'erreur
 * propre au lieu de laisser React Native afficher un écran rouge/technique.
 */
export default class ErrorBoundary extends React.Component {
    state = { error: null };

    static getDerivedStateFromError(error) {
        return { error };
    }

    componentDidCatch(error, info) {
        // Visible dans les logs Metro, jamais affiché tel quel à l'utilisateur.
        console.error("Erreur inattendue de l'application :", error, info?.componentStack);
    }

    handleRetry = () => {
        this.setState({ error: null });
    };

    handleReload = () => {
        if (typeof reloadAppAsync !== "function") return;
        reloadAppAsync("Récupération après erreur").catch(() => {});
    };

    render() {
        if (this.state.error) {
            return (
                <View style={styles.screen}>
                    <StatusBar style="dark" />
                    <ErrorState
                        fullScreen
                        title="L'application a rencontré un problème"
                        message="Pas d'inquiétude : vos produits enregistrés restent disponibles. Réessayez, ou rechargez l'application si le problème persiste."
                        onRetry={this.handleRetry}
                        onSecondary={this.handleReload}
                        secondaryLabel="Recharger l'application"
                    />
                </View>
            );
        }

        return this.props.children;
    }
}

const styles = StyleSheet.create({
    screen: { flex: 1, backgroundColor: COLORS.bg },
});
