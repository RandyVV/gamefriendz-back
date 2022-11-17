# Projet Game FriendZ

## Installation

- Installer _Symfony Skeleton_ avec la commande:
composer create-project symfony/website-skeleton my-project

- Utiliser la commande composer install
- Créer la base de donnée gamefriendz

## Récupération du code

- Faire un git pull depuis la branche Dev
- Utiliser la commande:
bin/console doctrine:migrations:migrate
- Utiliser la commande:
php bin/console doctrine:fixtures:load

## Générer le token JWT

- Utiliser la commande:
php bin/console lexik:jwt:generate-keypair