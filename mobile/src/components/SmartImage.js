import React from "react";
import { Image } from "expo-image";

/**
 * Image optimisée basée sur expo-image :
 *  - cache mémoire + disque (plus de re-téléchargement → affichage quasi instantané
 *    aux lancements suivants)
 *  - fondu progressif à l'apparition
 *  - placeholder flou léger pendant le chargement
 *
 * Remplace <Image> de react-native pour les images distantes (produits…).
 * Accepte soit une string d'URL, soit un objet { uri }.
 */
const BLURHASH = "L5H2EC=PM+yV0g-mq.wG9c010J}I";

export default function SmartImage({
    source,
    style,
    contentFit = "cover",
    transition = 250,
    onLoad,
    ...rest
}) {
    const src = typeof source === "string" ? { uri: source } : source;

    return (
        <Image
            source={src}
            style={style}
            contentFit={contentFit}
            transition={transition}
            cachePolicy="memory-disk"
            placeholder={BLURHASH}
            onLoad={onLoad}
            {...rest}
        />
    );
}
