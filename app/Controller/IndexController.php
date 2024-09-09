<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Config\ConfigQdrant;
use Codewithkyrian\Transformers\Transformers;
use Hyperf\Qdrant\Api\Points;
use Hyperf\Qdrant\Connection\HttpClient;
use Hyperf\Qdrant\Struct\Points\VectorStruct;
use Hyperf\Qdrant\Struct\Points\WithPayload;

use function Codewithkyrian\Transformers\Pipelines\pipeline;

class IndexController extends AbstractController
{
    public function search()
    {
        $client = new HttpClient(new ConfigQdrant());
        $points = new Points($client);

        Transformers::setup()
            ->apply();
        $extractor = pipeline('embeddings', 'Xenova/all-MiniLM-L6-v2');

        $input = $this->request->query('q');
        if (! $input) {
            return $this->response->json([
                'error' => 'filter q not found'
            ])->withStatus(400);
        }

        $embedding = $extractor($input, normalize: true, pooling: 'mean');

        $response = $points->searchPoints(
            'collection',
            new VectorStruct($embedding[0]),
            3,
            withPayload: new WithPayload(true),
        );

        return $this->response->json($response);
    }
}
