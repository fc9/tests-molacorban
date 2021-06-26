#!/bin/bash

echo "Dando permissões de acesso";
docker exec -it php chown -R www-data:www-data storage/

echo "Instalando o composer"
docker exec -it php composer install

echo "Criando arquivo de configuração de ambiente"
cp ./src/.env.example ./src/.env

echo "Criando uma chave para a aplicação"
docker exec -it php php artisan key:generate

echo "Carregando as migrations e seeds"
docker exec -it php php artisan migrate

echo "Pronto! Ambiente concluido!"
echo ""
echo "Acesse http://localhost:8000/ e cadastre um novo usuário em \"Login\""