<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use Swoole\Coroutine\Channel;

#[Command]
class LoadTestCommand extends HyperfCommand
{
    public function __construct(
        protected ContainerInterface $container,
    ) {
        parent::__construct('load:test');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Load Test Demo Command');
    }

    public function handle()
    {
        $host     = getenv('REDIS_HOST') ?: '127.0.0.1';
        $min      = (int)(getenv('MIN_CLIENTS') ?: 10);
        $max      = (int)(getenv('MAX_CLIENTS') ?: 100);
        $interval = (int)(getenv('STAGE_INTERVAL') ?: 5);
        $delay    = (int)(getenv('REQUEST_DELAY_MS') ?: 1);

        $redis = $this->container->get(Redis::class);
        $current = $min;

        while ($current <= $max) {
            $this->info("{$host}: estágio com {$current} corrotinas");
            $start = time();

            $clients = new Channel($current);

            while ((time() - $start) < $interval) {
                $clients->push(true);

                Coroutine::create(function () use ($redis, $clients, $delay) {
                    usleep($delay);

                    $uuid = Uuid::uuid4()->toString();
                    $data = json_encode(['uuid' => $uuid]);
                    $ttl  = 20;

                    try {
                        $redis->set($uuid, $data, ['ex' => $ttl]);
                    } catch (\Throwable $e) {
                        echo "Redis set failed: {$e->getMessage()}\n";
                    }

                    try {
                        $redis->get($uuid);
                    } catch (\Throwable $e) {
                        echo "Redis get failed: {$e->getMessage()}\n";
                    }

                    $clients->pop();
                });
            }

            while ($clients->length() > 0) {
                Coroutine::sleep(0.01);
            }

            $current++;
        }
    }
}
