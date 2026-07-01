import React, { useEffect, useRef, useState } from "react";
import { Animated } from "react-native";
import { NavigationContainer } from "@react-navigation/native";
import { createNativeStackNavigator } from "@react-navigation/native-stack";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { Ionicons } from "@expo/vector-icons";

import { useAuth } from "../context/AuthContext";
import { useCart } from "../context/CartContext";
import { COLORS, GradientBackground } from "../theme";
import AppLoader from "../components/AppLoader";
import LoginScreen from "../screens/LoginScreen";
import RegisterScreen from "../screens/RegisterScreen";
import HomeScreen from "../screens/HomeScreen";
import CategoriesScreen from "../screens/CategoriesScreen";
import DealsScreen from "../screens/DealsScreen";
import OrdersScreen from "../screens/OrdersScreen";
import CartScreen from "../screens/CartScreen";
import AccountScreen from "../screens/AccountScreen";
import ProductListScreen from "../screens/ProductListScreen";
import ProductDetailScreen from "../screens/ProductDetailScreen";
import SearchScreen from "../screens/SearchScreen";
import WriteReviewScreen from "../screens/WriteReviewScreen";
import WishlistScreen from "../screens/WishlistScreen";
import RecentlyViewedScreen from "../screens/RecentlyViewedScreen";
import EditProfileScreen from "../screens/EditProfileScreen";
import SettingsScreen from "../screens/SettingsScreen";
import AccountSecurityScreen from "../screens/AccountSecurityScreen";
import PaymentScreen from "../screens/PaymentScreen";
import AdminPaymentsScreen from "../screens/AdminPaymentsScreen";
import AdminOrdersScreen from "../screens/AdminOrdersScreen";
import OrderDetailScreen from "../screens/OrderDetailScreen";
import NotificationsScreen from "../screens/NotificationsScreen";
import AddressesScreen from "../screens/AddressesScreen";
import * as Notifications from "expo-notifications";
import { registerForPushNotifications } from "../push";
import CouponsScreen from "../screens/CouponsScreen";
import WalletScreen from "../screens/WalletScreen";

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

const ICONS = {
    Accueil: "home",
    Catégories: "grid",
    "Bons Plans": "pricetag",
    Panier: "bag",
    Compte: "person",
};

/** Icône d'onglet animée : léger rebond/agrandissement quand elle devient active. */
function AnimatedTabIcon({ base, color, size, focused }) {
    const scale = useRef(new Animated.Value(1)).current;
    useEffect(() => {
        Animated.spring(scale, {
            toValue: focused ? 1.18 : 1,
            friction: 5,
            tension: 160,
            useNativeDriver: true,
        }).start();
    }, [focused, scale]);
    return (
        <Animated.View style={{ transform: [{ scale }] }}>
            <Ionicons
                name={focused ? base : `${base}-outline`}
                size={size}
                color={color}
            />
        </Animated.View>
    );
}

function Tabs() {
    const { cart } = useCart();
    return (
        <Tab.Navigator
            screenOptions={({ route }) => ({
                headerTransparent: true,
                headerBackground: () => <GradientBackground />,
                headerTintColor: "#fff",
                headerTitleStyle: { fontWeight: "800" },
                tabBarActiveTintColor: COLORS.primaryDark,
                tabBarInactiveTintColor: "#9ca3af",
                tabBarStyle: { height: 58, paddingBottom: 6, paddingTop: 6 },
                tabBarLabelStyle: { fontSize: 11, fontWeight: "600" },
                tabBarHideOnKeyboard: true,
                tabBarIcon: ({ color, size, focused }) => (
                    <AnimatedTabIcon
                        base={ICONS[route.name] || "ellipse"}
                        color={color}
                        size={size}
                        focused={focused}
                    />
                ),
            })}
        >
            <Tab.Screen
                name="Accueil"
                component={HomeScreen}
                options={{ headerShown: false }}
            />
            <Tab.Screen
                name="Catégories"
                component={CategoriesScreen}
                options={{ headerShown: false }}
            />
            <Tab.Screen
                name="Bons Plans"
                component={DealsScreen}
                options={{ headerShown: false }}
            />
            <Tab.Screen
                name="Panier"
                component={CartScreen}
                options={{
                    headerShown: false,
                    tabBarBadge: cart.count > 0 ? cart.count : undefined,
                    tabBarBadgeStyle: { backgroundColor: COLORS.badge },
                }}
            />
            <Tab.Screen
                name="Compte"
                component={AccountScreen}
                options={{ headerShown: false }}
            />
        </Tab.Navigator>
    );
}

function AppStack() {
    return (
        <Stack.Navigator
            screenOptions={{
                headerStyle: { backgroundColor: COLORS.primary },
                headerTintColor: "#fff",
                headerTitleStyle: { fontWeight: "800" },
                animation: "slide_from_right",
                animationDuration: 260,
            }}
        >
            <Stack.Screen
                name="Tabs"
                component={Tabs}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="ProductList"
                component={ProductListScreen}
                options={{ title: "Produits" }}
            />
            <Stack.Screen
                name="Search"
                component={SearchScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="ProductDetail"
                component={ProductDetailScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="WriteReview"
                component={WriteReviewScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="Wishlist"
                component={WishlistScreen}
                options={{ title: "Liste de souhait" }}
            />
            <Stack.Screen
                name="RecentlyViewed"
                component={RecentlyViewedScreen}
                options={{ title: "Vu récemment" }}
            />
            <Stack.Screen
                name="EditProfile"
                component={EditProfileScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="Settings"
                component={SettingsScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="AccountSecurity"
                component={AccountSecurityScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="Payment"
                component={PaymentScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="AdminPayments"
                component={AdminPaymentsScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="AdminOrders"
                component={AdminOrdersScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="Notifications"
                component={NotificationsScreen}
                options={{ title: "Notifications" }}
            />
            <Stack.Screen
                name="Orders"
                component={OrdersScreen}
                options={{ title: "Mes commandes" }}
            />
            <Stack.Screen
                name="OrderDetail"
                component={OrderDetailScreen}
                options={{ headerShown: false }}
            />
            <Stack.Screen
                name="Addresses"
                component={AddressesScreen}
                options={{ title: "Gestion des adresses" }}
            />
            <Stack.Screen
                name="Coupons"
                component={CouponsScreen}
                options={{ title: "Mes bons" }}
            />
            <Stack.Screen
                name="Wallet"
                component={WalletScreen}
                options={{ title: "Mon portefeuille" }}
            />
        </Stack.Navigator>
    );
}

function AuthStack() {
    return (
        <Stack.Navigator screenOptions={{ headerShown: false, animation: "fade" }}>
            <Stack.Screen name="Login" component={LoginScreen} />
            <Stack.Screen name="Register" component={RegisterScreen} />
        </Stack.Navigator>
    );
}

// Redirige selon le lien d'une notification push
function routeFromLink(nav, link) {
    if (!nav || !link) return;
    switch (link.type) {
        case "order":
            nav.navigate("OrderDetail", { id: link.id });
            break;
        case "product":
            nav.navigate("ProductDetail", { id: link.id });
            break;
        case "cart":
            nav.navigate("Tabs", { screen: "Panier" });
            break;
        case "wallet":
            nav.navigate("Wallet");
            break;
        case "admin_payment":
        case "admin_wallet":
            nav.navigate("AdminPayments");
            break;
        default:
            nav.navigate("Notifications");
            break;
    }
}

export default function RootNavigator() {
    const { token, loading } = useAuth();
    const navRef = useRef(null);

    // Temps d'affichage minimum du loader (logo) pour un rendu soigné
    const [minReady, setMinReady] = useState(false);
    useEffect(() => {
        const t = setTimeout(() => setMinReady(true), 1500);
        return () => clearTimeout(t);
    }, []);

    // Enregistre le token push dès qu'on est connecté
    useEffect(() => {
        if (token) {
            registerForPushNotifications();
        }
    }, [token]);

    // Ouvre l'écran concerné quand on tape une notification push
    useEffect(() => {
        const sub = Notifications.addNotificationResponseReceivedListener((response) => {
            const link = response?.notification?.request?.content?.data?.link;
            routeFromLink(navRef.current, link);
        });
        return () => sub.remove();
    }, []);

    if (loading || !minReady) {
        return <AppLoader />;
    }

    return (
        <NavigationContainer ref={navRef}>
            {token ? <AppStack /> : <AuthStack />}
        </NavigationContainer>
    );
}
