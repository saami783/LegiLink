# Brouillon extrême 

Maquettes figma : https://www.figma.com/design/qPIUk5pVDCkmtRvYhADKXS/LegiLink?node-id=0-88&t=AFlMPjDZdDBnW7iX-1

Vidéo v2 https://youtu.be/cnCeWhOUMQE

Vidéo v1 https://youtu.be/aUD3mx0OSY0

## Tailwind css

Deploiement en prod : https://symfony.com/doc/current/frontend/asset_mapper.html

binaire tailwinds : https://github.com/tailwindlabs/tailwindcss/releases

Commande pour générer un fichier css en fonction du tailwinds utilisé dans les fichiers twig :
`.\bin\tailwindcss.exe -i .\assets\styles\app.css -o .\assets\styles\app.tailwind.css -W`

Pour minimiser le css pour la prod on utilise :
`.\bin\tailwindcss.exe -i .\assets\styles\app.css -o .\assets\styles\app.tailwind.css -m`

symfony console asset-map:compile <br>
symfony console importmap:require 


Quelques sites de composants tailwinds : <br>
- https://flowbite.com/docs/typography/paragraphs/
- https://tailgrids.com/components
- https://preline.co/examples/clients-sections.html
- https://www.creative-tim.com/twcomponents/component/admin-panel-1
- https://www.creative-tim.com/twcomponents/component/feature-1


- npm i @symfony/stimulus-bundle
- php bin/console importmap:require @symfony/stimulus-bundle


- symfony console importmap:require bootstrap/dist/css/bootstrap.min.css --download
- php bin/console importmap:require tailwindcss/forms --download
- symfony console importmap:require jquery --download
- php bin/console importmap:update
- .\bin\tailwindcss.exe init
- php bin/console importmap:require bootstrap --download



@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap');

@tailwind base;
@tailwind components;
@tailwind utilities;

## Documentation 

Pour standardiser les entrées du fichier .md et faciliter le traitement par le script PHP, un format est imposé :

Art [Type][Numéro] [Code]

Où :

[Type] est facultatif et peut être L, R, ou D pour les articles législatifs, réglementaires ou décrets respectivement. S'il n'y a pas de type spécifique, cette partie est omise. <br>
[Numéro] est le numéro de l'article, qui peut inclure des chiffres, des tirets et des points selon le besoin. <br>
[Code] est l'abréviation standardisée du code de loi correspondant. (Se réferer au tableau des abbréviations plus bas dans le fichier.)

Exemple :

- Art 2 CC
- Art 3 CPC
- Art 4 CCom
- Art 12 CPP
- Art 43 CT
- Art 23 CP
- Art 11 CSI
- Art L1 CT
- Art R1142-1 CT
- Art D1142-2 CT

Cette standardisation permet de reconnaître plus facilement les différentes parties de chaque référence d'article de loi.
Elle simplifie lees Expressions Régulières : Ce format uniforme rendra mes expressions régulières beaucoup plus simples et plus fiables.

## Tableau d'abbreviation :

- **CC** : Code Civil
- **CPC** : Code de Procédure Civile
- **CCom** : Code de Commerce
- **CPP** : Code de Procédure Pénale
- **CT** : Code du Travail
- **CP** : Code Pénal
- **CSI** : Code de la Sécurité Intérieure

mail api : retours-legifrance-modernise@dila.gouv.fr
