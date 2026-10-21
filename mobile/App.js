import React from "react";
import { StatusBar } from "expo-status-bar";
import { SafeAreaProvider } from "react-native-safe-area-context";
import { AuthProvider } from "./src/context/AuthContext";
import { CartProvider } from "./src/context/CartContext";
import { WishlistProvider } from "./src/context/WishlistContext";
import ErrorBoundary from "./src/components/ErrorBoundary";
import RootNavigator from "./src/navigation";

export default function App() {
    return (
        <SafeAreaProvider>
            <ErrorBoundary>
                <AuthProvider>
                    <CartProvider>
                        <WishlistProvider>
                            <StatusBar style="light" />
                            <RootNavigator />
                        </WishlistProvider>
                    </CartProvider>
                </AuthProvider>
            </ErrorBoundary>
        </SafeAreaProvider>
    );
}
