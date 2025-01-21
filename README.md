# Introduction

This is a skeleton application using the Hyperf framework. This application is meant to be used as a starting place for those looking to get their feet wet with Hyperf Framework.

# Requirements

Hyperf has some requirements for the system environment, it can only run under Linux and Mac environment, but due to the development of Docker virtualization technology, Docker for Windows can also be used as the running environment under Windows.

The various versions of Dockerfile have been prepared for you in the [hyperf/hyperf-docker](https://github.com/hyperf/hyperf-docker) project, or directly based on the already built [hyperf/hyperf](https://hub.docker.com/r/hyperf/hyperf) Image to run.

When you don't want to use Docker as the basis for your running environment, you need to make sure that your operating environment meets the following requirements:

- PHP >= 8.1
- Any of the following network engines
   - Swoole PHP extension >= 5.0，with `swoole.use_shortname` set to `Off` in your `php.ini`
   - Swow PHP extension >= 1.3

- JSON PHP extension
- Pcntl PHP extension
- OpenSSL PHP extension （If you need to use the HTTPS）
- PDO PHP extension （If you need to use the MySQL Client）
- Redis PHP extension （If you need to use the Redis Client）
- Protobuf PHP extension （If you need to use the gRPC Server or Client）

# Installation using Composer

The easiest way to create a new Hyperf project is to use [Composer](https://getcomposer.org/). If you don't have it already installed, then please install as per [the documentation](https://getcomposer.org/download/).

To create your new Hyperf project:

```bash
composer create-project hyperf/hyperf-skeleton path/to/install
```

If your development environment is based on Docker you can use the official Composer image to create a new Hyperf project:

```bash
docker run --rm -it -v $(pwd):/app composer create-project --ignore-platform-reqs hyperf/hyperf-skeleton path/to/install
```

# Getting started

Once installed, you can run the server immediately using the command below.

```bash
cd path/to/install
php bin/hyperf.php start
```

Or if in a Docker based environment you can use the `docker-compose.yml` provided by the template:

```bash
cd path/to/install
docker-compose up
```

This will start the cli-server on port `9501`, and bind it to all network interfaces. You can then visit the site at `http://localhost:9501/` which will bring up Hyperf default home page.

## Hints

- A nice tip is to rename `hyperf-skeleton` of files like `composer.json` and `docker-compose.yml` to your actual project name.
- Take a look at `config/routes.php` and `app/Controller/IndexController.php` to see an example of a HTTP entrypoint.

**Remember:** you can always replace the contents of this README.md file to something that fits your project description.

## Kafka REST Proxy for Producing JSON Messages Using SSL Certificate

### Changes Summary:

1. **Non-SSL:** When SSL is not needed, you only need to set `KAFKA_SECURITY_PROTOCOL_ENABLE=false`, and the other SSL-related environment variables can be omitted.

   Example for non-SSL:

   ```bash
   docker run -e KAFKA_SECURITY_PROTOCOL_ENABLE=false -it -p 9501:9501 anavpdel/kafka-rest-proxy
   ```

2. **Custom Response:** If you need a custom response, you can set `CUSTOM_RESPONSE_JSON` in the environment.

   Example for custom response:

   ```bash
   -e CUSTOM_RESPONSE_JSON='{"code":0, "message":"Notified successfully."}'
   ```

   This will return:

   ```json
   {
       "code": 0,
       "message": "Notified successfully."
   }
   ```



### Using Docker CLI

To run the Kafka REST Proxy with SSL enabled:

```bash
docker run -it -p 9501:9501 \
  --name kafka-rest-proxy \
  -e KAFKA_SECURITY_PROTOCOL_ENABLE=true \
  -e KAFKA_SSL_KEY_LOCATION=/ssl/server.key \
  -e KAFKA_SSL_CERTIFICATE_LOCATION=/ssl/server.crt \
  -e KAFKA_SSL_CA_LOCATION=/ssl/ca.crt \
  -e KAFKA_BROKERS=kafka:9093 \
  anavpdel/kafka-rest-proxy
```

To run without SSL (disable SSL):

```bash
docker run -e KAFKA_SECURITY_PROTOCOL_ENABLE=false -it -p 9501:9501 anavpdel/kafka-rest-proxy
```

**Note:** The following environment variables are required when SSL is enabled:

- `KAFKA_SECURITY_PROTOCOL_ENABLE`
- `KAFKA_SSL_KEY_LOCATION`
- `KAFKA_SSL_CERTIFICATE_LOCATION`
- `KAFKA_SSL_CA_LOCATION`



### Using Docker Compose

```yaml
kafka-rest-proxy:
  container_name: hyperf-kafka-producer
  image: anavpdel/kafka-rest-proxy
  volumes:
    - ./ssl:/ssl
  ports:
    - 9508:9501
  stdin_open: true
  tty: true
  environment:
    - APP_ENV=dev
    - SCAN_CACHEABLE=false
    - KAFKA_SECURITY_PROTOCOL_ENABLE=false
    - KAFKA_SSL_KEY_LOCATION=anything
    - KAFKA_SSL_CERTIFICATE_LOCATION=anything
    - KAFKA_SSL_CA_LOCATION=anything
  networks:
    - internal
```

You can send a POST JSON payload to:

```
http://localhost:9501/topics/{topicName}
```

For example, POST to `http://localhost:9501/topics/hyperf-rest-proxy`:

```json
{
    "something": "hello world"
}
```

This sends JSON data to `hyperf-rest-proxy`.

### Additional Configuration

- **Default Memory Limit:** 1GB
- **Default Timezone:** Asia/Kathmandu

To customize these values, you can add:

```bash
-e memory_limit=4G -e timezone=asia/shanghai
```
