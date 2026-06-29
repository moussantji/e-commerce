import React from "react";
import { View, ActivityIndicator } from "react-native";
import { NavigationContainer } from "@react-navigation/native";
import { createNativeStackNavigator } from "@react-navigation/native-stack";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { Ionicons } from "@expo/vector-icons";

import { useAuth } from "../context/AuthContext";
import { useCart } from "../context/CartContext";
import LoginScreen from "../screens/LoginScreen";
import RegisterScreen from "../screens/RegisterScreen";
import HomeScreen from "../screens/HomeScreen";
import CategoriesScreen from "../screens/CategoriesScreen";
import OrdersScreen from "../screens/OrdersScreen";
import CartScreen from "../screens/CartScreen";
import AccountScreen from "../screens/AccountScreen";
import ProductListScreen from "../screens/ProductListScreen";
import ProductDetailScreen from "../screens/ProductDetailScreen";

const ORANGE = "#FF6A00";
const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

const ICONS = {
    Accueil: "home",
    Catégories: "grid",
    Commande: "receipt",
    Panier: "cart",
    Compte: "person",
};

function Tabs() {
    const { cart } = useCart();
    return (
        <Tab.Navigator
            screenOptions={({ route }) => ({
                headerStyle: { backgroundColor: ORANGE },
                headerTintColor: "#fff",
                headerTitleStyle: { fontWeight: "800" },
                tabBarActiveTintColor: ORANGE,
                tabBarInactiveTintColor: "#9ca3af",
                tabBarStyle: { height: 58, paddingBottom: 6, paddingTop: 6 },
                tabBarLabelStyle: { fontSize: 11 },
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
            <Tab.Screen name="Catégories" component={CategoriesScreen} />
            <Tab.Screen
                name="Commande"
                component={OrdersScreen}
                options={{ title: "Mes commandes" }}
            />
            <Tab.Screen
                name="Panier"
                component={CartScreen}
                options={{
                    tabBarBadge: cart.count > 0 ? cart.count : undefined,
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
                headerStyle: { backgroundColor: ORANGE },
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
                name="ProductDetail"
                component={ProductDetailScreen}
                options={({ route }) => ({
                    title: route.params?.name || "Produit",
                })}
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
                <ActivityIndicator size="large" color={ORANGE} />
            </View>
        );
    }

    return (
        <NavigationContainer>
            {token ? <AppStack /> : <AuthStack />}
        </NavigationContainer>
    );
}
