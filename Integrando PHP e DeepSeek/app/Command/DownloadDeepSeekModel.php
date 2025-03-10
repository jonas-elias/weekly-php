<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Codewithkyrian\Transformers\Transformers;

use function Codewithkyrian\Transformers\Pipelines\pipeline;


#[Command]
class DownloadDeepSeekModel extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('download:deepseek');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Download deepseek model command');
    }

    public function handle()
    {
        $this->line('Download started!', 'info');
        Transformers::setup()
            ->apply();
        // pipeline('text-generation', 'Xenova/deepseek-coder-1.3b-base');
        pipeline('text-generation', 'onnx-community/DeepSeek-R1-Distill-Qwen-1.5B-ONNX');
        $this->line('Download finished!', 'info');
    }
}
