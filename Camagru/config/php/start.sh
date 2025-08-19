#! /bin/bash
cd /var/www/html

# Instalar dependencias solo si no existe vendor/
# Verify installation
if composer show | grep -q "mongodb/mongodb" ; then
  echo "mongodb/mongodb package is installed."
else
  composer require mongodb/mongodb
fi

if composer show | grep -q "phpmailer/phpmailer"; then
  echo "phpmailer/phpmailer package is installed."
else
  composer require phpmailer/phpmailer
fi

if composer show | grep -q "ramsey/uuid"; then
  echo "ramsey/uuid package is installed."
else
  composer require ramsey/uuid
fi

exec php-fpm
if [ ! -d "vendor" ]; then
   phpmailer/phpmailer ramsey/uuid
fi

exec php-fpm