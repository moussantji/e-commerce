import React from "react";
import { View, ActivityIndicator } from "react-native";
import { NavigationContainer } from "@react-navigation/native";
import { createNativeStackNavigator } from "@react-navigation/native-stack";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { Ionicons } from "@expo/vector-icons";

import { useAuth } from "../context/AuthContext";
import { useCart } from "../context/CartContext";
import { COLORS, GradientBackground } from "../theme";
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
import OrderDetailScreen from "../screens/OrderDetailScreen";
import NotificationsScreen from "../screens/NotificationsScreen";
import AddressesScreen from "../screens/AddressesScreen";
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
                tabBarIcon: ({ color, size, focused }) => {
                    const base = ICONS[route.name] || "ellipse";
                    return (
                        <Ionicons
                            name={focused ? base : `${base}-outline`}
                            size={size}
                            color={color}
                        />
                    );
                },
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
        <Stack.Navigator screenOptions={{ headerShown: false }}>
            <Stack.Screen name="Login" component={LoginScreen} />
            <Stack.Screen name="Register" component={RegisterScreen} />
        </Stack.Navigator>
    );
}

export default function RootNavigator() {
    const { token, loading } = useAuth();

    if (loading) {
        return (
            <View
                style={{
                    flex: 1,
                    justifyContent: "center",
                    alignItems: "center",
                }}
            >
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    return (
        <NavigationContainer>
            {token ? <AppStack /> : <AuthStack />}
        </NavigationContainer>
    );
}
