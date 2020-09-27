# Github-notifier

# Instructions d'installation :
+ Clonez ou téléchargez le contenu du dépôt GitHub : git clone https://github.com/mdoutreluingne/AgenceSymfony.git
+ Editez le fichier situé à la racine intitulé ".env" afin de remplacer les valeurs de paramétrage de la base de données.
+ Installez les dépendances du projet avec : composer install et yarn install
+ Créez la base de données avec la commande suivante : php bin/console doctrine:database:create
+ Lancer les migrations avec la commande : php bin/console doctrine:migrations:migrate
+ Créerune tache planifier pour lancer le script api_github dans /Command
+ Afin de lancer le serveur, lancez la commande: symfony server:start
+ Bravo, c'est désormais accessible à l'adresse : localhost:8000 !
