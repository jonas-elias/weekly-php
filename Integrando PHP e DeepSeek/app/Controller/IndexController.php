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

use Codewithkyrian\Transformers\Generation\Streamers\TextStreamer;
use Codewithkyrian\Transformers\Transformers;

use function Codewithkyrian\Transformers\Pipelines\pipeline;

class IndexController extends AbstractController
{
    private mixed $model;

    public function __construct()
    {
        $this->model = pipeline('text-generation', 'onnx-community/DeepSeek-R1-Distill-Qwen-1.5B-ONNX');
    }

    public function search()
    {
        Transformers::setup()
            ->apply();
        $messages = [
            [
                'role' => 'user',
                'content' => 'Generate programming code PHP to write "Hello World"!'
            ],
        ];

        $streamer = TextStreamer::make()->shouldSkipPrompt();
        $generator = $this->model;
        $input = $generator->tokenizer->applyChatTemplate($messages, addGenerationPrompt: true, tokenize: false);
        $output = $generator(
            $input,
            streamer: $streamer,
            maxNewTokens: 1024,
            doSample: true,
            returnFullText: false,
            //    temperature: 0.7,
        );

        print_r($output[0]['generated_text']);

        return $this->response->json($output[0]['generated_text']);
    }
}
