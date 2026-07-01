import { Platform } from "react-native";
import Constants from "expo-constants";
import * as Device from "expo-device";
import * as Notifications from "expo-notifications";
import api from "./api/client";

// Affiche les notifications reçues quand l'app est au premier plan
Notifications.setNotificationHandler({
    handleNotification: async () => ({
        shouldShowAlert: true,
        shouldPlaySound: true,
        shouldSetBadge: true,
    }),
});

/**
 * Demande la permission, récupère le token Expo et l'envoie au backend.
 * À appeler après connexion (quand un token API est disponible).
 */
export async function registerForPushNotifications() {
    try {
        if (!Device.isDevice) return null; // pas de push sur émulateur/simulateur

        if (Platform.OS === "android") {
            await Notifications.setNotificationChannelAsync("default", {
                name: "default",
                importance: Notifications.AndroidImportance.MAX,
                vibrationPattern: [0, 250, 250, 250],
            });
        }

        const { status: existing } = await Notifications.getPermissionsAsync();
        let status = existing;
        if (existing !== "granted") {
            const req = await Notifications.requestPermissionsAsync();
            status = req.status;
        }
        if (status !== "granted") return null;

        const projectId =
            Constants?.expoConfig?.extra?.eas?.projectId ??
            Constants?.easConfig?.projectId;

        const tokenResponse = await Notifications.getExpoPushTokenAsync(
            projectId ? { projectId } : undefined,
        );
        const token = tokenResponse?.data;

        if (token) {
            try {
                await api.post("/me/push-token", { token });
            } catch (e) {
                /* ignore : sera renvoyé au prochain lancement */
            }
        }
        return token;
    } catch (e) {
        return null;
    }
}
