cd /srv/RPGClubs_xUSSR

git pull

composer install

npm install grunt -g

npm install uglify-es

npm install

grunt

cd /var/www/rpgclubs_xussr

# сохраняем конфиги

rm -rf `ls /var/www/rpgclubs_xussr/ | grep -v '.config$'`

cd /srv/RPGClubs_xUSSR

# copy files

rsync -avr --files-from=install-files.lst /srv/RPGClubs_xUSSR/ /var/www/rpgclubs_xussr

# set ACL

sudo chown www-data:www-data /var/www/rpgclubs_xussr/* -R
