ip=$(hostname -I | awk '{print $1}')
export APP_ADDR=$ip
cp .env_base .env
echo "APP_ADDR=$APP_ADDR" >> .env
echo "Añadido APP_ADDR=$APP_ADDR a .env"