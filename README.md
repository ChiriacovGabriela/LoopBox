# LoopBox

LoopBox est un site de streaming audio. Il permet non seulement d'écouter de la musique mais aussi d'importer ses propres chanson et de les partager avec les autres utilisateurs.
On peut acceder au site sans être connecté à un compte utilisateur pour écouter les musiques disponibles.
Le site est disponible en anglais et en français.

## Prérequis
Vous devez être connecté à l'Internet pour pouvoir naviguer sur notre site.

## Technologies
La grande majorité de notre site est codé en PHP avec les templates en Twig.
Nous avons ajouté un système de filtre à l'aide de JavaScript.

## Fonctionnalités

### Création de compte
Lors de la création du compte, il est demandé de renseigner son prénom, son nom, ses préfences musicales, son email et un mot de passe. L'email doit être unique pour chaque compte.
A la suite de votre création de compte, vous recevrez un mail de confirmation.
Pour voir le mail envoyé, il faut se connecter à Mailtrap avec le compte google : loopboxregister@gmail.com (mdp : loopbox2023)
https://mailtrap.io/inboxes/2161878/messages
Également, pour recevoir le mail, il faut d'abord exécuter la ligne de commande suivante:
php bin/console messenger:consume async
dans le terminal.

Vous pouvez vous connecter sur le compte: yessicerlyn@gmail.com; mdp:qPt4R26Zr3SdwFQ

### Ajout de chansons, playlists et albums
Une fois connecté, il est possible d'importer des chansons, des albums et de créer des playlists.
Les images de couverture des chansons, playlists ou albums doivent être importées au format jpeg. (2mo maximum)
Les musiques doivent être importées au format mp3. (6mo maximum)
Il est possible d'importer autant de musique que vous le souhaitez.

### Likes et commentaires
Lorsque vous écoutez une musique, il est possible de la liker et de la commenter. Les chansons que vous avez liké s'ajoutent automatiquement dans votre playlist Favoris.

### Multilingue 
L'ensemble de pages de notre site est disponible dans deux langues: anglais et français.

### Organisation
https://docs.google.com/spreadsheets/d/1Wnbul110U-vXBZlOg_KTy3_M8YD3c1825SdkyZD0ZMo/edit?usp=sharing