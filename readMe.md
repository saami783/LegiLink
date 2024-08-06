LegiLink - Combinaison des mots "Législation" et "Link" (lien en anglais), ce nom est direct et facile à retenir. Il évoque clairement l'objectif de relier les utilisateurs aux textes de loi pertinents.


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
- symfony console importmap:require bootstrap/dist/css/bootstrap.min.css 
- php bin/console importmap:require tailwindcss/forms


- symfony console importmap:require jquery
- php bin/console importmap:update
- .\bin\tailwindcss.exe init
- php bin/console importmap:require bootstrap