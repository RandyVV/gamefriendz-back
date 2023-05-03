# Projet Game FriendZ

## ⚙️ Installation

### Back-end

#### Installer les dépendances du projet

```bash
composer install
```

#### Base de données

Éditer le fichier`.env` avec les bonnes informations de connexion à la base de données :

```
DATABASE_URL="mysql://USER:PASSWORD@HOST:PORT/DATABASE?serverVersion=VERSION&charset=utf8mb4"
```

Lancer les migrations pour construire les tables :

```bash
php bin/console doctrine:migrations:migrate
```

##### Seeding (optionnel)

```bash
php bin/console doctrine:fixtures:load
```

#### Générer la clé SSL pour l'encodage des JWT

```bash
php bin/console lexik:jwt:generate-keypair
```

### Front-end

#### Installer les dépendances

```bash
npm install
```

## ⏯️ Lancer le projet

### Démarrer le serveur de développement

```bash
php -S HOST:PORT -t public
```

### Générer le CSS et le JS pour le développement

En mode serveur (avec rechargement automatique de modules - HMR) :
```bash
npm run dev-server
```

Une seule fois :
```bash
npm run dev
```

Avec un watcher (regénère le CSS et le JS si on modifie un fichier) :
```
npm run watch
```

## 📦 Préparer le projet pour la production

### Configurer Symfony pour la production

Modifier le fichier `.env` pour passer en mode production :

```
APP_END=prod
```

⚠️ Penser à modifier les informations de connexion à la base de données avec celles de la base de production !

### Générer les ressource CSS et JS optimisées

```bash
npm run build
```
