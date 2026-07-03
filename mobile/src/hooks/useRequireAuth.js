import { Alert } from "react-native";
import { useNavigation } from "@react-navigation/native";
import { useAuth } from "../context/AuthContext";

/**
 * Garde d'authentification pour le mode invité (comportement Kikuu).
 *
 * Retourne une fonction `ensureAuth(message?)` :
 *  - si l'utilisateur est connecté → renvoie true (l'action peut continuer)
 *  - sinon → affiche une alerte "Connexion requise" avec un bouton qui ouvre
 *    l'écran de connexion, et renvoie false.
 *
 * Exemple :
 *   const ensureAuth = useRequireAuth();
 *   const onFavorite = () => {
 *       if (!ensureAuth("Connectez-vous pour ajouter aux favoris.")) return;
 *       ...
 *   };
 */
export default function useRequireAuth() {
    const { token } = useAuth();
    const navigation = useNavigation();

    return (message = "Connectez-vous pour continuer.") => {
        if (token) return true;
        Alert.alert("Connexion requise", message, [
            { text: "Annuler", style: "cancel" },
            {
                text: "Se connecter",
                onPress: () => navigation.navigate("Login"),
            },
        ]);
        return false;
    };
}
