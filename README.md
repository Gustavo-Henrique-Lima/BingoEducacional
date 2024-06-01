<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Como Rodar o Projeto
### Pré-requisitos

- PHP 8.2 ou superior
- Composer 2.0 ou superior
- MySQL 5.7 ou superior

### Passos

1. **Clonar o Repositório:**

   ### Caso esteja com a chave SSH configurada no seu computador
   ```bash
   git clone git@github.com:Gustavo-Henrique-Lima/BingoEducacional.git
   ````

   ### Caso não esteja com a chave SSH configurada no seu computador
    ```bash
    git clone https://github.com/Gustavo-Henrique-Lima/BingoEducacional.git
     ````

2. **Navegue até o diretório do projeto:**

    ```bash
    cd BingoEducacional
    ````

3. **Instale as dependências:**

    ```bash
    composer install
    ````
    
4. **Crie arquivo .env:**
    ```bash
    cp .env.example .env
    ````
    
5. **Atualize as variáveis de ambiente do arquivo .env:**  
    #### As variáveis que precisam ser atualizadas para rodar o projeto estão nas linhas 23 a 28 do arquivo .env, altere ela conforme as configurações da sua máquina (certifique-se de     que a base de dados que você irá informar aqui já exista)
    ```bash
    DB_CONNECTION=seuSgbd
    DB_HOST=127.0.0.1
    DB_PORT=3306 (Porta padrão de alguns SGBDs, verifique o seu e veja se é necessário alterar algo)
    DB_DATABASE=suaBaseDados
    DB_USERNAME=seuUsuario
    DB_PASSWORD=senhaDoSeuUsuario
    ```
6. **Rodar as migrations:**
    #### Após configurar suas variáveis rode as migrations que irão criar as tabelas na sua base de dados.
    ```bash
    php artisan migrate
    ```
7. **Popular a base de dados com dados de teste:**
    #### Após criar as tabelas rode o seeder que irá popular a base de dados com dados de teste.
    ```bash
    php artisan db:seed
    ```
    
8. **Inicie o servidor de desenvolvimento executando os seguintes comandos:**
    ```bash
   php artisan key:generate
   ```

   ```bash
   php artisan serve
   ```
    
#### Feito isso o servidor está rodando na porta 8000
## Funcionalidades
   ### Em breve
        
## Documentação

  O projeto inclui documentação detalhada para facilitar o entendimento e a interação com a aplicação.
  A seguir estão os recursos de documentação disponíveis.

  ### Swagger

   A API é documentada usando o Swagger, que fornece uma interface interativa para explorar os endpoints 
  da aplicação.
  ### Acesso ao Swagger:
  **Com o projeto rodando**
  
  O Swagger pode ser acessado através do link: [Swagger UI](http://localhost:8000/api/documentation#/).
  
  A interface do Swagger oferece uma visão interativa dos endpoints, permitindo testar as operações
  diretamente na documentação.

## Testes 
  ### A aplicação conta com alguns testes unitários e de integração que são responsáveis por verificar e validar as funcionalidades do sistema, os testes estão na pasta /tests, para executar os testes execute o seguinte comando
  ```bash
  php artisan test
  ```
    
