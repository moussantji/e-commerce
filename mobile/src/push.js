import { Platform } from "react-native";
import Constants from "expo-constants";
import * as Device from "expo-device";
import * as Notifications from "expo-notifications";
import api from "./api/client";

// Depuis SDK 53, le push a été retiré d'Expo Go : on détecte cet
// environnement pour ne pas déclencher d'erreur (le push fonctionne
// normalement dans un build de dev ou l'APK autonome).
const isExpoGo = Constants.executionEnvironment === "storeClient";

// Affiche les notifications reçues quand l'app est au premier plan
// (API SDK 54+ : shouldShowBanner / shouldShowList remplacent shouldShowAlert)
Notifications.setNotificationHandler({
    handleNotification: async () => ({
        shouldShowBanner: true,
        shouldShowList: true,
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
        // Push non supporté dans Expo Go (SDK 53+) : on sort proprement.
        // Ça marchera dans le development build et dans l'APK.
        if (isExpoGo) return null;
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
