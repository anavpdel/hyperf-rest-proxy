#!/bin/sh

# List of required environment variables
REQUIRED_ENV_VARS="KAFKA_SECURITY_PROTOCOL_ENABLE KAFKA_SSL_KEY_LOCATION KAFKA_SSL_CERTIFICATE_LOCATION KAFKA_SSL_CA_LOCATION"

# Check for each required environment variable
for var in $REQUIRED_ENV_VARS; do
  if [ -z "$(eval echo \$$var)" ]; then
    echo "Error: Environment variable $var is not set."
    exit 1
  fi
done

# If all required variables are set, start the application
# exec php /opt/www/bin/hyperf.php start

/bin/sh
