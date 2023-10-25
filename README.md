# api_bilemo

Projet numéro 7 de ma formation PHP/Symfony chez Openclassrooms qui consiste à créer une API pour BileMo afin de développer 
leur vitrine de téléphones mobiles.

## Description du projet

Voici les principales fonctionnalités disponibles demandées par le client:

  * consulter la liste des produits BileMo ;
  * consulter les détails d’un produit BileMo ;
  * consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
  * consulter le détail d’un utilisateur inscrit lié à un client ;
  * ajouter un nouvel utilisateur lié à un client ;
  * supprimer un utilisateur ajouté par un client.
  * modifier un utilisateur ajouté par un client.
  
## Contraintes

Les clients de l’API doivent être authentifiés via Oauth ou JWT.

## Prérequis

Php ainsi que Composer doivent être installés sur votre ordinateur afin de pouvoir correctement lancé l'API.

## Installation

  * Téléchargez et dézipper l'archive. Installer le contenu dans le répertoire de votre serveur:
      * Wamp : Répertoire 'www'.
      * Mamp : Répertoire 'htdocs'.
      
  * Configurer les lignes DATABASE_URL dans le fichier .env_local.
  
  * Ensuite placez-vous dans votre répertoire par le biais de votre console de commande (ou terminal) et renseignez la commande suivante:
      * ```bash
        'composer install' pour windows.
        ```
      * ```bash
        'php composer.phar install' pour Mac OS.
        ```
    
* Création de la base de données:

    ```bash
    php bin/console doctrine:database:create
    ```
    
* Création de données fictives pour tester le site:

    ```bash
    php bin/console doctrine:fixtures:load
    ```
    
* Génération des clés d'authentification JWT:

    ```bash
    mkdir -p config/jwt (si la syntaxe n'est correcte : mkdir -p config\jwt) 
    ```
    
    ```bash
    openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    ```
     
    * Renseignez et confirmez la pass phrase 'bilemo_api'  
    
    ```bash
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout 
    ```  
    
    * Renseignez la pass phrase 'password'
    
* Démarrage du serveur de symfony:
  
    ```bash
    php bin/console server:run
    ```

## Documentation Technique

 * http://localhost:8000/api/doc

## Outils utilisés

  * [Symfony](https://symfony.com/)
  * [Composer](https://getcomposer.org/)
  * [Postman](https://www.getpostman.com/)
  
## Auteur

  * Dupré Cédric