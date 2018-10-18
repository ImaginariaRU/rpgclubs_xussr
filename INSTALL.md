# prepare data

mkdir /srv/RPGClubs_xUSSR

cd /srv/RPGClubs_xUSSR

git clone https://github.com/KarelWintersky/rpgclubs_xussr.git .

composer install

npm install grunt -g

npm install uglify-es

npm install

grunt

# prepare configs

Copy config files (from private area) to /var/www/rpgclibs/.config

# copy files

rsync -avr --files-from=files.lst /srv/RPGClubs_xUSSR/ /var/www/rpgclubs_xussr

# set ACL

sudo chown www-data:www-data /var/www/rpgclubs_xussr/* -R