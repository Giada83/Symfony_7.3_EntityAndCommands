REQUISITI DI SISTEMA
Symfony v.7.3
PHP >= 8.2
Composer - https://getcomposer.org/download/
PostgreSQL o MySQL
Symfony CLI (opzionale) - https://symfony.com/download
Per visualizzare le informazioni sul progetto : php bin/console about
---------------------------------------------------------------------
IMPOSTARE IL PROGETTO
1. Clonare il repository
2. Installare le dipendenze : composer install
3. Impostare il file .env o .env.local per configurare il database
   Esempio:
   DATABASE_URL="postgresql://postgres:root@127.0.0.1:5432/symfony?serverVersion=17&charset=utf8"
4. Creare il db : php bin/console doctrine:database:create
5. Applicare le migrazioni esistenti : php bin/console doctrine:migrations:migrate
--------------------------------------------------------------------------------------------------
PRINCIPALI PACCHETTI UTILIZZATI
ORM doctrine : composer require symfony/orm-pack
maker-bundle : composer require --dev symfony/maker-bundle
validator    : composer require symfony/validator
-----------------------------------------------------------
CONSOLE COMMANDS
Lanciare il comando e seguire le istruzioni 
Creare nuovo autore   : php bin/console app:create-author
Creare nuovo articolo : php bin/console app:create-article
Visualizzare articolo : php bin/console app:show-article