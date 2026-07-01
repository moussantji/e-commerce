# Assets de l'application mobile

## Logo requis : `logo.png`

Le loader de l'application (`src/components/AppLoader.js`) et le splash natif
(`app.json` → `expo.splash.image`) utilisent le fichier :

```
mobile/assets/logo.png
```

**Action requise :** déposez le logo Maden Baoubab (le PNG fourni) à cet emplacement,
sous le nom exact `logo.png`.

- Format : PNG (fond transparent ou blanc)
- Recommandé : largeur ~1200 px pour un rendu net sur tous les écrans
- Le même fichier sert à la fois pour le loader in-app et pour le splash natif Expo.

> Tant que ce fichier n'est pas présent, le bundler Metro renverra une erreur
> « Unable to resolve module ../../assets/logo.png ». Ajoutez simplement l'image
> pour résoudre cela.

### (Optionnel) Icône de l'application

Pour utiliser aussi le logo comme icône d'app, ajoutez `icon.png` (1024×1024)
puis référencez-le dans `app.json` via `expo.icon` et
`expo.android.adaptiveIcon.foregroundImage`.
