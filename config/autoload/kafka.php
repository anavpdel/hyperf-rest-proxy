<?php

declare(strict_types=1);


use Hyperf\Kafka\Constants\KafkaStrategy;
use function Hyperf\Support\env;

return [
    'default' => [
        'enable' => true,
        'connect_timeout' => -1,
        'send_timeout' => -1,
        'recv_timeout' => -1,
        'client_id' => '',
        'max_write_attempts' => 3,
        'brokers' => [
            env('KAFKA_BROKERS', 'kafka:9092'),
        ],
        'bootstrap_servers' => env('KAFKA_BROKERS', 'kafka:9092'),
        'update_brokers' => true,
        'acks' => -1,
        'producer_id' => -1,
        'producer_epoch' => -1,
        'partition_leader_epoch' => -1,
        'interval' => 0,
        'session_timeout' => 60,
        'rebalance_timeout' => 60,
        'replica_id' => -1,
        'rack_id' => '',
        'group_retry' => 5,
        'group_retry_sleep' => 1,
        'group_heartbeat' => 3,
        'offset_retry' => 5,
        'auto_create_topic' => true,
        'partition_assignment_strategy' => KafkaStrategy::RANGE_ASSIGNOR,
        'sasl' => [],
        'ssl' => [
            'open' => env('KAFKA_SECURITY_PROTOCOL_ENABLE'),
            'cafile' => env('KAFKA_SSL_CA_LOCATION'),
            'keyFile' => env('KAFKA_SSL_KEY_LOCATION'),
            'certFile' => env('KAFKA_SSL_CERTIFICATE_LOCATION')
        ],
        'client' => \longlang\phpkafka\Client\SwooleClient::class,
        'socket' => \longlang\phpkafka\Socket\SwooleSocket::class,
        'timer' => \longlang\phpkafka\Timer\SwooleTimer::class,
        'consume_timeout' => 600,
        'exception_callback' => null,
    ],
];
