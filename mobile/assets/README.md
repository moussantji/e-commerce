# Assets de l'application mobile

## Logo requis : `logo.png`

Le loader de l'application (`src/components/AppLoader.js`) et le splash natif
(`app.json` → `expo.splash.image`) utilisent le fichier :

```
mobile/assets/logo.png
```

**Action requise :** déposez le logo Maden Baoubab (le PNG fourni) à cet emplacement,
sous le nom exact `logo.png`.

- Format : PNG à **fond transparent** (le splash et le loader s'affichent sur un fond **violet** `#764ba2`)
- Recommandé : largeur ~1200 px pour un rendu net sur tous les écrans
- Le même fichier sert à la fois pour le loader in-app et pour le splash natif Expo.

> Tant que ce fichier n'est pas présent, le bundler Metro renverra une erreur
> « Unable to resolve module ../../assets/logo.png ». Ajoutez simplement l'image
> pour résoudre cela.

### Icône de l'application : `icon.png`

L'icône d'app (`app.json` → `expo.icon` et `expo.android.adaptiveIcon.foregroundImage`)
utilise :

```
mobile/assets/icon.png
```

**Action requise :** déposez une version **carrée** du logo (1024×1024 px recommandé,
**fond transparent**) sous le nom `icon.png`.

- Sur Android (adaptive icon), le logo est affiché sur un fond **violet** `#764ba2`.
- Astuce : ajoutez un peu de marge autour du logo pour éviter qu'il soit rogné
  par le masque circulaire/arrondi d'Android.
