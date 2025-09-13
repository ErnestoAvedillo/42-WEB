#! /bin/bash
cd /var/www/html

# Instalar dependencias solo si no existe vendor/
# Verify installation
if [ ! -d "vendor" ]; then
  composer install
fi

# Verificar si mongodb/mongodb está instalado
if composer show | grep -q "mongodb/mongodb" ; then
  echo "mongodb/mongodb package is installed."
else
  composer require mongodb/mongodb
fi

# Verificar si phpmailer/phpmailer está instalado
if composer show | grep -q "phpmailer/phpmailer"; then
  echo "phpmailer/phpmailer package is installed."
else
  composer require phpmailer/phpmailer
fi

# Verificar si ramsey/uuid está instalado
# Ramsey UUID es un paquete que proporciona una implementación de UUID (Identificadores Únicos Universales) en PHP.
if composer show | grep -q "ramsey/uuid"; then
  echo "ramsey/uuid package is installed."
else
  composer require ramsey/uuid
fi

# Verificar si endroid/qr-code está instalado
# endroid/qr-code es un paquete que proporciona una implementación de generación de códigos QR en PHP.
if composer show | grep -q "endroid/qr-code"; then
  echo "endroid/qr-code package is installed."
else
  composer require endroid/qr-code
fi

# Verificar si spomky-labs/otphp está instalado
# spomky-labs/otphp es un paquete que proporciona una implementación de OTP (One-Time Password) en PHP.
if composer show | grep -q "spomky-labs/otphp"; then
  echo "spomky-labs/otphp package is installed."
else
  composer require spomky-labs/otphp
fi


exec php-fpm