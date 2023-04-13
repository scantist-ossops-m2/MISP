#!/bin/sh

set -e

MISP_READY_STATUS_FLAG='/tmp/.MISP_READY_STATUS_FLAG'

rm -f "${MISP_READY_STATUS_FLAG}"

[ -z "$MYSQL_HOST" ] && MYSQL_HOST=db
[ -z "$MYSQL_PORT" ] && MYSQL_PORT=3306
[ -z "$MISP_DB_USER" ] && MISP_DB_USER=misp
[ -z "$MISP_DB_PASSWORD" ] && MISP_DB_PASSWORD=misp
[ -z "$MISP_DB" ] && MISP_DB=misp3
[ -z "$MYSQL_PWD" ] && MYSQL_PWD=$MISP_DB_PASSWORD
[ -z "$MYSQLCMD" ] && MYSQLCMD="mysql --defaults-file=/etc/mysql/conf.d/misp.cnf -P $MYSQL_PORT -h $MYSQL_HOST -r -N $MISP_DB"

# create mysql default config
cat << EOF > /etc/mysql/conf.d/misp.cnf
[client]
user=$MISP_DB_USER
password=$MYSQL_PWD
EOF


run_all_migrations() {
	./bin/cake migrations migrate
	./bin/cake migrations migrate -p tags
	./bin/cake migrations migrate -p ADmad/SocialAuth
}

delete_model_cache() {
	echo >&2 "Deleting cakephp cache..."
	rm -rf ./tmp/cache/models/*
	rm -rf ./tmp/cache/persistent/*
}

init_user() {
	# TODO: change to use cake shell instead of direct SQL

	# Check if admin organisation exists
	ADMIN_ORG_COUNT=`echo "SELECT COUNT(*) FROM organisations WHERE type='ADMIN';" | ${MYSQLCMD} | tr -d '\n'`
	if [ "$ADMIN_ORG_COUNT" -gt 0 ]; then
		echo >&2 "Admin organisation already exists, skipping organisation creation...";
		return
	fi
	
	echo >&2 "Creating default organisation...";
	# If ADMIN_ORG_UUID is not set, get a UUID from the DB
	if [ -z "$ADMIN_ORG_UUID" ]; then
		ADMIN_ORG_UUID=`echo "SELECT uuid();" | ${MYSQLCMD} | tr -d '\n'`
	fi

	# Create default Organisation
	echo "INSERT INTO organisations (name, date_created, date_modified, description, type, uuid) values ('ORGNAME', now(), now(), 'Automatically generated admin organisation', 'ADMIN', '$ADMIN_ORG_UUID');" | ${MYSQLCMD}

	# Check if admin user exists
	ADMIN_USER_COUNT=`echo "SELECT COUNT(*) FROM users WHERE role_id=1;" | ${MYSQLCMD} | tr -d '\n'`
	if [ "$ADMIN_USER_COUNT" -gt 0 ]; then
		echo >&2 "Admin user already exists, skipping user creation...";
		return
	fi

	ADMIN_ORG_ID=`echo "SELECT id FROM organisations WHERE uuid='${ADMIN_ORG_UUID}';" | ${MYSQLCMD} | tr -d '\n'`

	# Generate password hash and insert Admin user
	echo >&2 "Creating admin user...";
	export ADMIN_INITIAL_PASSWORD_HASH=`php -r "echo password_hash('${ADMIN_INITIAL_PASSWORD}', PASSWORD_DEFAULT);" | tr -d '\n'`
	echo "INSERT INTO users (password, org_id, email, role_id) VALUES ('${ADMIN_INITIAL_PASSWORD_HASH}', ${ADMIN_ORG_ID}, '${ADMIN_EMAIL}', 1);" | ${MYSQLCMD}

	# Get Admin user ID
	ADMIN_USER_ID=`echo "SELECT id FROM users WHERE EMAIL='${ADMIN_EMAIL}';" | ${MYSQLCMD} | tr -d '\n'`

	# Insert Admin user API key
	if [ -z "$ADMIN_USER_API_KEY" ]; then
		echo >&2 "Creating admin user API key...";
		ADMIN_USER_API_KEY_START=`echo ${ADMIN_USER_API_KEY} | head -c 4`
		ADMIN_USER_API_KEY_END=`echo ${ADMIN_USER_API_KEY} | tail -c 5`
		export ADMIN_USER_API_KEY_HASH=`php -r "echo password_hash('${ADMIN_USER_API_KEY}', PASSWORD_DEFAULT);" | tr -d \'`
		echo "INSERT INTO auth_keys (uuid, authkey, authkey_start, authkey_end, created, expiration, user_id) VALUES ((SELECT uuid()), '${ADMIN_USER_API_KEY_HASH}', '${ADMIN_USER_API_KEY_START}', '${ADMIN_USER_API_KEY_END}', 0, 0, ${ADMIN_USER_ID});" | ${MYSQLCMD}
	fi
}

delete_model_cache

# waiting for DB to come up
for try in 1 2 3 4 5 6; do
	echo >&2 "migration - attempt $try"
	run_all_migrations && break || true
	sleep 5
	[ "$try" = "6" ] && exit 1
done

init_user

# Test php-fpm config
php-fpm -t

# Finished bootstrapping, create ready flag file
touch "${MISP_READY_STATUS_FLAG}"

exec php-fpm -F "$@"
