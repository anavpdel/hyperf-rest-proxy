<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Coroutine\Coroutine;
use Psr\Container\ContainerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Kafka\Producer;
use longlang\phpkafka\Producer\Producer as LongLangProducer;
use Hyperf\Contract\ConfigInterface;

#[Command]
class KafkaProducer extends HyperfCommand
{
    protected $logger;

    public function __construct(protected ConfigInterface $config, protected ContainerInterface $container, protected Producer $producer)
    {
        $this->logger = $container->get(LoggerFactory::class)->get('kafka-process');

        parent::__construct('kafka:produce');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
        $this->name = 'kafka';

        $p = new LongLangProducer($this->config->get('kafka.' . $this->name));

        print_r($p);

        // print_r($this->producer->makePro getConfig());
        // $wg = new \Hyperf\Coroutine\WaitGroup();
        // // Counter increase 2
        // $wg->add(2);
        // // Create coroutine A
        // Coroutine::create(function () use ($wg) {
        //     // some code
        //     // Counter decrease 1
        //     Coroutine::sleep(2);
        //     echo '1';
        //     $wg->done();
        // });
        // // Create coroutine B
        // Coroutine::create(function () use ($wg) {
        //     // some code
        //     // Counter decrease 1
        //     Coroutine::sleep(1);
        //     echo '2';
        //     $wg->done();
        // });
        // Coroutine::defer(function () {
        //     echo ("end1-");
        // });


        // // Wait for coroutine A and coroutine B finished
        // // $wg->wait();
        // Coroutine::defer(function () {
        //     echo ("end   -- 1");
        // });
        // Coroutine::defer(function () {
        //     echo ("end2");
        // });

        // $wg->done();



        $this->producer->send('biz_kafka', $this->getMessagePayload());
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
