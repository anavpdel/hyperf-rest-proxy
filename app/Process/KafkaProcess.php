<?php

declare(strict_types=1);

namespace App\Process;

use Hyperf\Di\Container;
use Hyperf\Kafka\Producer;
use Psr\Log\LoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Process\AbstractProcess;
use Psr\Container\ContainerInterface;
use Hyperf\Process\Annotation\Process;

#[Process(name: 'Producer')]
class KafkaProcess extends AbstractProcess
{
    protected $logger;

    public function __construct(protected ContainerInterface $container, protected Producer $producer)
    {
        $this->logger = $container->get(LoggerFactory::class)->get('kafka-process');
    }
    public function handle(): void
    {
        $this->logger->info("Processed run");

        // print_r($this->getMessagePayload());
        // $this->producer->send('hyperf', $this->getMessagePayload());
    }

    public function getMessagePayload(): string
    {
        return json_encode([
            'name' => 'Biz Message',
            'data' => [
                "promotion_id" => uniqid(),
                "mobile" => '9841941111',
                "message_id" => uniqid(),
                "message" => "hello Kafka from command",
                "processed_at" => date('Y-m-d H:i:s')
            ]
        ]);
    }
}