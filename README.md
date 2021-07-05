# Endpoint CSV ~ Fabio Cabral

[![](https://img.shields.io/badge/Nginx-1.25-green.svg)](https://registry.hub.docker.com/_/nginx)
[![php 7.3](https://img.shields.io/badge/PHP-7.4-blueviolet.svg)](https://www.php.net/manual/en/index.php)
[![Laravel 8.40](https://img.shields.io/badge/Laravel-8.40-red.svg)](https://laravel.com/docs/8.x)
[![PostgreeSQL](https://img.shields.io/badge/PostgreSQL-13.3-blue)](https://registry.hub.docker.com/_/postgres)
[![Redis](https://img.shields.io/badge/Redis-8.40-red.svg)](https://registry.hub.docker.com/_/redis)
[![Build with PHPStorm](https://img.shields.io/badge/Build_in-PHPStorm-blue.svg)](https://www.jetbrains.com/phpstorm/)
[![Build with PHPStorm](https://img.shields.io/badge/Manager_in-DataGrid-blue.svg)](https://www.jetbrains.com/datagrip/)
[![Tests](https://img.shields.io/badge/Tests-Passing-green)](https://shields.io/)

> [Fase técnica](https://github.com/molacorban/interviews/blob/main/php-challenge/REAME.md) para Dev PHP na [molacorban.com.br](https://www.molacorban.com.br/)

<p align="center"><a href="https://www.molacorban.com.br/" target="_blank"><img src="https://www.molacorban.com.br/img/LOGO_MOLACORBAN.png" width="200" alt="molacorban.com.br"></a></p>

## Requisitos

* [Git](https://git-scm.com/downloads)
* [docker](www.docker.com)

## Instalação

### 1. Deploy do Projeto

Execute os comandos a seguir no terminal (caso esteja na plataforma Windows, recomendo utilizar o pacote [Cmder](https://cmder.net/)).

> Em caso de Linux, vou considerar que partimos da pasta raiz do usuário ```cd ~``` para evitar chateações com permissões de pasta, mas fica ao seu critério seguir de outra pasta.

Clone este projeto.

```bash 
git clone https://github.com/fc9/tests-molacorban.git teste-fabio
```

Após, entre na pasta criada.

```bash 
cd teste-fabio
```

### 2. Crie e inicie os containers

Execute o comando:

> Atenção: caso tenha alguns serviços rodando nas portas 5436 (PostgreSQL), 9000 (PHP), 6382 (Redis) ou 8000 (apache2, nginx...) seria bom pausá-los; ou edite o arquivo *docker-compose.yml* se souber como fazê-lo.

```bash
docker-compose up -d
```

### Pronto!

# Orientações

Este teste foi feito utilizado o Laravel com [Passport](https://laravel.com/docs/8.x/passport) que fornece uma implementação de servidor OAuth2 completa. Uma serie de endpoints estão pré-definidos e conceitos cuja explicação vai além desta amostra, logo, vamos ficar apenas naquilo que interessa ao teste. 

## Fluxo de acesso

Para ter acesso aos end-points da API é necessário cumpri algumas etapas:

1 - Ter um usuário ativo, i.e., um **login** e **senha**;

2 - Obter uma autorização de acesso de cliente, um **Authorization Grant**;

3 - Utilizar suas credênciais de usuário (1) e sua autorização (2) para solicitar um **Access Token**;

4 - Enviar seus token de accesso (3) em todas requisições à API. 

### 1/4 Criando um usuário

Crie um usuário em ```http://127.0.0.1:8000/register```. 

> Apesar de adorar front end, não tive tempo para criar algo customizado, então usei o modelo padrão do framework.

### 2/4 Obtendo um autorização de acesso de cliente.

Se você está lendo este texto, ainda não desenvolvi um subsistema para disponibilizar um formulário bacaninha para solicitação de autorização de cliente. 

Então, via console, na pasta raiz deste projeto (```~/teste-fabio```) execute o comando abaixo:

```bash
docker exec -it php php artisan passport:client --password
```
Dê um nome para seu cliente e aperte [ENTER]. Na pergunta seguinte apenas confirme ([ENTER]). Você obterá algo como:

```bash
Password grant client created successfully.
Client ID: 9
Client secret: lEXqs8jrhz0Ls2KjR6pxeuc34vu64U0XsmMmNGV5
```
Agora temos um Authorization Grant pra chamar de nosso.

### 3/4 Otendo o token

Para obter o token é necessário acessa via POST o endpoint ```http://127.0.0.1:8000/oauth/token``` é enviar suas credenciais de usuário, autorização de cliente, que tipo de acesso temos autorização e o escopo.

Mas calma, segue um exemplo em [CUrl](https://curl.se/) de como fazer isto:

```bash
curl --location --request POST 'http://localhost:800000/oauth/token' \
--form 'grant_type="password"' \
--form 'client_id="9"' \
--form 'client_secret="lEXqs8jrhz0Ls2KjR6pxeuc34vu64U0XsmMmNGV5"' \
--form 'username="test@example.net"' \
--form 'password="qu41qu3rc0i54"' \
--form 'scope=""'
```

Estando tudo certo, você obterá um retorno em json parecido com isto:

```json
{
  "token_type": "Bearer",
  "expires_in": 86400,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhd...",
  "refresh_token": "def5020040de22a186ebcd25e4e447b52806052b2..."
}
```

Como se pode ver, o tokem é do tipo *Bearer*, durará um dia (86400 segundos), seu *access token* (\o/) e um token auxiliar para solicitar um novo access token quando este expirar.

### 4/4 Acessando a API.

A API está disponível em ```http://127.0.0.1:8000/api/v1/batches/```.

Em todas as solicitações **deve ser enviado no cabeçalho da requisição o access token**.

API mode, o retorno sempre será em formato Json (```application/json```), mesmo em caso de erro.

## Enviando um arquivo .CSV

Envie uma requisição POST para:

```bash
http://127.0.0.1:8000/api/v1/batches/
```

Nos dados do seu formulário envie um input do tipo *file* com nome "file". Não tem mistério, segue um exemplo:

```bash
curl --location --request POST 'http://127.0.0.1:8000/api/v1/batches/' \
--header 'Authorization: Bearer [COLOQUE SEU ACCESS TOKEN AQUI]' \
--header 'Content-Type: multipart/form-data' \
--form 'file=@"/home/fulano/Documents/data.csv"'
```

Estando tudo certo:

```json
{
  "success": true,
  "type": "App\\Models\\Batch",
  "uuid": "f2e4b830-c590-4e52-89fb-1949abba8175",
  "links": {
    "self": "http://127.0.0.1:8000/api/v1/batches/f2e4b830-c590-4e52-89fb-1949abba8175"
  }
}
```

Obtivemos um identificador [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) do "lote" (*Batch*) de compras enviadas no arquivo CSV, além de um link que podemos utilizar para recuperar os dados salvos.  

> Alguns retornos negativos podem ocorrer caso envie um arquivo não compatível com CSV, ou não enviar nada, ou... enfim, não vou sitar cada caso. Fica a cargo do *tester* verificar isto.
 
### Consultando com UUID

Envie uma requisição GET com o UUID para o endpoint:

```bash
http://127.0.0.1:8000/api/v1/batches/{UUID}
```

> Troque o UUID pelo que obteve (ou não) ao enviar o arquivo CSV no passo anterior. Eu precisava mencionar isto?! haha 

#### Consultas avançadas

A API tem suporte a paginação, filtragem por colunas, ordenação e limitação de campos. Seguem alguns exemplos de consultas via comandos [CUrl](https://curl.se/).

#### Básica

```bash
curl --location --request GET 'http://localhost:8000/api/v1/batches/{UUID}'
```
Para testes, recomendo utilizar a impressão "amigável".

```bash
curl --location --request GET 'http://localhost:8000/api/v1/batches/{UUID}&pretty'
```

#### Paginando

```bash
curl --location --request GET 'http://localhost:8000/api/v1/batches/{UUID}?page=2&per_page=25'
```

#### Limitar campos retornados

```bash
curl --location --request GET 'http://localhost:8000/api/v1/batches/{UUID}?fields=cliente,document,valor_original,valor_final'
```

#### Classificar colunas

```bash
curl --location --request GET 'http://localhost:8000/api/v1/batches/{UUID}?sort=cliente,valor_original_desc,valor_final_ASC,data_pgto'
```

#### Filtrar por colunas

```bash
curl --location --request GET 'http://localhost:8000/api/v1/batches/{UUID}?filter=cliente:roberto%20carlos,nome_categoria:titulocap'
```

#### Completa

```bash
curl --location --request GET 'http://localhost:8000/api/v1/batches/{UUID}?fields=cliente,document,valor_original,valor_final&sort=cliente,valor_original_desc,valor_final_ASC,data_pgto&filter=cliente:roberto%20carlos,nome_categoria:titulocap&pretty'
```

## Retornos em caso de falha

Há um suporte básico a erros de digitação e consultas mal formadas. Isto, deixo novamente aos cuidados do *tester*.

## Processamento assincrono 

Quando um arquivo é enviado é disparado um evento assincrono para carregá-lo para o banco de dados. Como este processo pode demorar indefinidamente, ao consultar um UUID ele retornará de acordo com status atual do lote:

1. **Em arquivo** - Aguardando para ser processado.
   
2. **Carregando** - Processamento iniciou mas ainda não terminou. Tente novamente mais tarde.
   
3. **Erro** - O arquivo estava corrompido e/ou foi encontrado algum erro no formato do mesmo ou os dados não foram validados. Você verá os detalhes. 

4. **Pronto** - Neste caso, você obtém a consulta paginada em si dos dados salvos no banco.

> Observação: todo lote CSV com status de erro é descartado no processamento e nada é salvo no banco de dados.

Periodicamente, um *cleaner* irá remover todos os arquivos de lote já processados.

## Testes

Para rodar os testes execute:

```bash
docker exec -it php php artisan test
```

## Autor

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/fc9">
        <img src="https://instagram.fbfh3-3.fna.fbcdn.net/v/t51.2885-19/s320x320/185446473_755112928485818_1561027276031897416_n.jpg?tp=1&_nc_ht=instagram.fbfh3-3.fna.fbcdn.net&_nc_ohc=R4aumIfNxR8AX-_JbKR&edm=ABfd0MgBAAAA&ccb=7-4&oh=6b71ab4b286fe5fbfcbb2c5d4da77aec&oe=60DDD7CC&_nc_sid=7bff83" width="120px;" alt="Fabio Cabral"/>
        <br />
        <sub><b>Fabio Cabral</b></sub>
      </a>
    </td>
  </tr>
</table>

Para obter uma explicação detalhada sobre como as coisas funcionam mande um "oi" para  **me@fabiocabral.dev**.

[![Open Source? Yes!](https://badgen.net/badge/Open%20Source%20%3F/Yes%21/blue?icon=github)](https://github.com/Naereen/badges/)
