<?php

declare(strict_types=1);

namespace App\Command;

use App\Config\ConfigQdrant;
use Codewithkyrian\Transformers\Transformers;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Qdrant\Api\Collections;
use Hyperf\Qdrant\Api\Points;
use Hyperf\Qdrant\Connection\HttpClient;
use Hyperf\Qdrant\Struct\Collections\Enums\Distance;
use Hyperf\Qdrant\Struct\Collections\VectorParams;
use Hyperf\Qdrant\Struct\Points\ExtendedPointId;
use Hyperf\Qdrant\Struct\Points\Point\PointStruct;
use Hyperf\Qdrant\Struct\Points\VectorStruct;
use Psr\Container\ContainerInterface;

use function Codewithkyrian\Transformers\Pipelines\pipeline;

#[Command]
class GenerateDatabaseQdrant extends HyperfCommand
{
    private array $data = [
        'A bright blue sky stretches over the peaceful countryside.',
        'The ancient oak tree stands tall and strong in the middle of the meadow.',
        'Children laugh and play as the sun sets over the bustling playground.',
        'A gentle breeze rustles the leaves of the nearby forest, creating a soothing sound.',
        'The aroma of fresh bread fills the cozy kitchen as it bakes in the oven.',
    ];

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('generate:database');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Generate Database in Qdrant');
    }

    public function handle()
    {
        Transformers::setup()
            ->apply();
        $extractor = pipeline('embeddings', 'Xenova/all-MiniLM-L6-v2');

        $client = new HttpClient(new ConfigQdrant());
        $collections = new Collections($client);
        $collections->createCollection('collection', new VectorParams(384, Distance::COSINE));

        foreach ($this->data as $key => $value) {
            $embedding = $extractor($value, normalize: true, pooling: 'mean');

            $points = new Points($client);
            $points->setWait(true);
            $points->upsertPoints('collection', [
                new PointStruct(
                    new ExtendedPointId($key + 10000),
                    new VectorStruct($embedding[0]),
                    [
                        'description' => $value,
                    ],
                ),
            ]);
        }
    }
}
