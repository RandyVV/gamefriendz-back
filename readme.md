# Projet Game FriendZ

## Installation

- Installer _Symfony Skeleton_ avec la commande:
composer create-project symfony/website-skeleton my-project

- Utiliser la commande composer install

## Récupération du code

- Faire un git pull depuis la branche Dev
- créer la base de donnée gamefriendz
- Utiliser la commande:
bin/console make:migrations:migrate
- Utiliser la commande:
php bin/console doctrine:fixtures:load