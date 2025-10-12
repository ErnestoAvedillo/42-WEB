ip=$(ip addr | grep metric | awk '{print $2}' | cut -d/ -f1)
export APP_ADDR=$ip
cp .env_base .env
echo "APP_ADDR=$APP_ADDR" >> .env