# Clonest

Clonest est un clone de [Pinterest](https://www.pinterest.fr/) en version minifié.

Ce projet a pour but de montrer mes connaissances du framework [Symfony](https://symfony.com/).

## Installation

Utiliser le gestionnaire de paquet [Composer](https://getcomposer.org/) pour installer toutes les dépendences du projet.
```bash
composer install
```

Utiliser le gestionnaire de paquet [Yarn](https://yarnpkg.com/) ou [npm](https://www.npmjs.com/) pour installer toutes les dépendences du projet.
```bash
yarn install

npm install
```

Générer le dossier public/build.
```bash
yarn encore production --progress

npm run build
```

Modifier les informations de connexion à la base de données dans le fichier .env.
```dotenv
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
```

Créer la base de données.
```bash
 php bin/console doctrine:database:create
```

Lancer les migrations.
```bash
 php bin/console doctrine:migrations:migrate
```

Appliquer les fixtures.
```bash
 php bin/console doctrine:fixtures:load -n
```

Lancer le serveur PHP.
```bash
 php -S localhost:8000 -t public
```

Utiliser un compte utilisateur généré par les fixtures (mot de passe: a_strong_password).
