# Busca Semântica e I.A com PHP - weekly-php

### Descrição

A aplicação refere-se a criação de uma API de busca vetorial com PHP utilizando um modelo de embeddings treinado com a arquitetura [ONNX](https://github.com/onnx/onnx).

### Instalação

Up dos serviços:
```shell
docker compose up -d
```

Acessando o serviço:
```shell
docker exec -it $containerID /bin/bash
```

Realizando o download do modelo neural:
```shell
php bin/hyperf.php download:embedding
```

Gerando o banco de dados vetorial no Qdrant:
```shell
php bin/hyperf.php generate:database
```

Iniciando a aplicação:
```shell
php bin/hyperf.php start
```

### Uso

http://localhost:9502/search?q=Input%20semantic%20search

Testando a busca semântica:
```shell
curl --location 'http://localhost:9502/search?q=Input%20semantic%20search'
```
