# Forum Symfony 4
Portage du projet de forum PHP sur le framework Symfony 4

## Mise en place du projet
- git clone du dépot
- composer update

### Base de données - MySQL
- Modifier le fichier .env avec les bons identifiants pour une base de données (DATABASE_URL)
- php bin/console make:migration
- php bin/console doctrine:migration:migrate

### Remplir le forum (Utilisateurs, sujet et messages)
- php bin/console doctrine:fixtures:load
- Rentrer un nombre quelconque

### Mise en marche
- php -S localhost:8000 -t public
