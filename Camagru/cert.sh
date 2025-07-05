export COUNTRY="SP"
export STATE="Barcelona"
export LOCATION="42Campus"
export ORG="Eavedill"
export ORGUNIT="Camagru"
export NAME="localhost"

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
 -keyout ./selfsigned.key \
 -out ./selfsigned.crt \
 -subj "/C=$COUNTRY/ST=$STATE/L=$LOCATION/O=$ORG/OU=$ORGUNIT/CN=$NAME"