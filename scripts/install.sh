#!/usr/bin/env bash

set -e

############################################
# Setup configurations
############################################
cp config/app.default.php config/app.php
cp webroot/default.htaccess webroot/.htaccess


############################################
# Build and/or pull containers
############################################
docker-compose pull
docker-compose build


############################################
# Seed database
############################################
docker-compose -f docker-compose.install.yml run -d --name=cd_mysql_seed mysql

# wait until the container is ready
until [ "`docker inspect -f {{.State.Running}} cd_mysql_seed`" == "true" ]; do
    sleep 1;
done;

docker exec cd_mysql_seed bash /tmp/seed-db.sh


############################################
# Install composer dependencies
############################################
docker-compose -f docker-compose.install.yml run composer


############################################
# Install node dependencies
############################################
docker-compose -f docker-compose.install.yml run node

# stop everything
docker-compose -f docker-compose.install.yml down


############################################
# Just some user info
############################################
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[1;34m'
NC='\033[0m'

echo -e "${GREEN}Yupii, installation successfull!${NC}"
echo -e "${YELLOW}NOTE:${NC} You'll need to add the ${RED}proprietary fonts${NC}, to get this working properly."
echo -e "Ask ${YELLOW}admin at gruene dot ch${NC} for more information."
echo -e "\n"
echo -e "Now run ${BLUE}docker-compose up -d${NC} and visit ${BLUE}http://localhost:8000${NC}"
echo -e "Login with the email ${BLUE}admin@admin.admin${NC} and ${BLUE}Admin2018${NC} as password"
