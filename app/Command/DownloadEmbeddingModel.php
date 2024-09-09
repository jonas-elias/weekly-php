<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Codewithkyrian\Transformers\Transformers;

use function Codewithkyrian\Transformers\Pipelines\pipeline;


#[Command]
class DownloadEmbeddingModel extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('download:embedding');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Download embedding model command');
    }

    public function handle()
    {
        $this->line('Download start!', 'info');
        Transformers::setup()
            ->apply();
        // pipeline('feature-extraction', 'Xenova/bert-base-uncased');
        pipeline('embeddings', 'Xenova/all-MiniLM-L6-v2');
        $this->line('Download finish!', 'info');
    }
}
