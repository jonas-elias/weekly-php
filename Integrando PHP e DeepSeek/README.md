# Integrando PHP e DeepSeek

### Descrição

A aplicação refere-se a criação de uma API de para o uso do modelo deepseek no PHP através da arquitetura [ONNX](https://github.com/onnx/onnx).

### Instalação

Up dos serviços:
```shell
docker compose up -d
```

Acessando o serviço:
```shell
docker exec -it $containerID /bin/bash
```

Instalação dos vendors:
```shell
composer install
```

Realizando o download do modelo neural:
```shell
brew install git-lfs

GIT_LFS_SKIP_SMUDGE=1 git clone https://huggingface.co/onnx-community/DeepSeek-R1-Distill-Qwen-1.5B-ONNX

rename file "model_quantized.onnx" to "decoder_model_merged_quantized.onnx"
```

Iniciando a aplicação:
```shell
php bin/hyperf.php start
```

### Uso

http://localhost:9502/search

Testando a busca semântica:
```shell
curl --location 'http://localhost:9502/search'
```
