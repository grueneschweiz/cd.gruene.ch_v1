#!/usr/bin/env bash

set -e

# wait until MySQL is really available
maxcounter=45

counter=1
while ! mysql -uroot -p"$MYSQL_ROOT_PASSWORD" > /dev/null 2>&1; do
    sleep 1
    counter=`expr $counter + 1`
    if [ $counter -gt $maxcounter ]; then
        >&2 echo "We have been waiting for MySQL too long already; failing."
        exit 1
    fi;
    echo "Waiting for MySQL to get ready..."
done
echo "Yay, MySQL is up and ready"


# seed the database
echo "Start seeding..."
mysql "$MYSQL_DATABASE" -uroot -p"$MYSQL_ROOT_PASSWORD" < /tmp/seed.sql
echo "Seeding successful"