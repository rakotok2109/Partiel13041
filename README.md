# Partiel Candidat 13041

## Informations concernant le partiel et les réalisations

Suite au mail d'Adrien Morin, le rendu concernant le Développement Web
a été réalisé avec un Backend Symfony et un Front en .html.twig

### Pour accéder au Partiel, comment faire ?

Vous trouverez une archive ZIP contenant l'ensemble de mon projet.
Vous trouverez aussi dans cette archive un fichier partiel13041.sh

Pour rendre le script exécutable veuillez taper la commande dans votre Git Bash : 
**chmod +x partiel13041.sh**

Et exécuter le ensuite avec : 
**./partiel13041.sh**

Dans le cas où cela ne fonctionnerait pas vous avez mon projet complet dans l'archive.

## Importation de la base de donnée

Le projet est relié à une base de donnée pour le stockage des différentes données. 
Le script la créera avec les données fictives automatiquement sinon il faudra taper les commandes suivantes : 
**php bin/console doctrine:database:create**
 
**php bin/console doctrine:migrations:migrate**


# Bonne Correction