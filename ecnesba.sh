
echo "Téléchargement de l'application ::   [0/100%]";
echo "Téléchargement de l'application ::   [3/100%]";
echo "Téléchargement de l'application ::   [5/100%]";
echo "Téléchargement de l'application ::   [6/100%]";
echo "Téléchargement de l'application ::   [7/100%]";
echo "Téléchargement de l'application ::   [8/100%]";
echo "Téléchargement de l'application ::   [9/100%]";
git clone https://github.com/JBBrun/ProjetWeb_4ACFA_Brun_Demolon.git
echo "Téléchargement de l'application ::   DONE [15/100%]";
echo "Renomme Fichier destination :: DONE[20/100%]";
mv ProjetWeb_4ACFA_Brun_Demolon ecnesba
echo "Redirection Fichier destination :: DONE[25/100%]";
cd ecnesba
echo "Installation de l'application :: COMPOSER[45/100%]";
echo "Installation de l'application :: COMPOSER[48/100%]";
echo "Installation de l'application :: COMPOSER[51/100%]";
echo "Installation de l'application :: COMPOSER[52/100%]";
echo "Installation de l'application :: COMPOSER[55/100%]";
echo "Installation de l'application :: COMPOSER[58/100%]";
echo "Installation de l'application :: COMPOSER[61/100%]";
php composer.phar install
echo "Installation de l'application :: BOWER[75/100%]";
echo "Installation de l'application :: BOWER[78/100%]";
echo "Installation de l'application :: BOWER[82/100%]";
bower install
echo "Installation de la base de données :: BOWER[84/100%]";
echo "Installation de la base de données :: BOWER[88/100%]";
php app/console doctrine:schema:update --force 
echo "Création Administrateur:: [90/100%]";
read -p "Entrez username: " name
name=${name:-admin}
echo $name
read -p "Entrez Email: " email
email=${email:-admin}
echo $email
read -p "Entrez Password: " password
password=${passwordname:-admin}
echo $password
php app/console fos:user:create $name $email $password
echo "Création Administrateur:: GRANT [95/100%]";
php app/console fos:user:promote $name ROLE_ADMIN
echo "FINALISATION ::  [95/100%]";
echo "FINALISATION ::  [96/100%]";
echo "FINALISATION ::  [97/100%]";
echo "FINALISATION ::  [98/100%]";
echo "FINALISATION ::  [99/100%]";
php app/console assets:install
chmod 777 -R app/cache app/logs
echo "INSTALLATION TERMINE ::  [100%/100%]";



