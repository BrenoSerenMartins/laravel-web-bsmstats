# BSMStats

BSMStats é uma aplicação web que fornece estatísticas e análises para o jogo competitivo League of Legends. A plataforma permite aos usuários pesquisar por invocadores, visualizar seu histórico de partidas e analisar seu desempenho através de dashboards interativos.

## Principais Funcionalidades

*   **Pesquisa de Invocadores:** Pesquise por jogadores de League of Legends pelo nome de invocador e tag line.
*   **Histórico de Partidas:** Exibe uma lista de partidas recentes para um determinado invocador.
*   **Visão Detalhada da Partida:** Visualize informações detalhadas sobre uma partida específica.
*   **Leaderboards:** Apresenta um ranking com os melhores jogadores.
*   **Informações de Campeões e Itens:** Fornece detalhes sobre campeões e itens do jogo.
*   **Sincronização de Dados:** Utiliza Jobs do Laravel para sincronizar dados da API da Riot em segundo plano, garantindo uma experiência de usuário fluida.

## Tecnologias Utilizadas

*   **Backend:** Laravel 12, PHP 8.2
*   **Frontend:** Blade templates, TailwindCSS
*   **Banco de Dados:** SQLite (padrão), compatível com MySQL/PostgreSQL
*   **Cache:** Laravel Cache
*   **Integração de API:** API da Riot Games para buscar dados de invocadores e partidas.

## Instalação e Execução

**Pré-requisitos:**

*   PHP 8.2 ou superior
*   Composer
*   Node.js e npm

**Passos para Instalação:**

1.  Clone o repositório.
2.  Instale as dependências do PHP: `composer install`
3.  Instale as dependências do frontend: `npm install`
4.  Crie um arquivo `.env` copiando o `.env.example`.
5.  Gere uma chave para a aplicação: `php artisan key:generate`
6.  Configure seu banco de dados no arquivo `.env`.
7.  Execute as migrações do banco de dados: `php artisan migrate`
8.  Configure sua chave da API da Riot em `config/services.php` ou no seu arquivo `.env`.

**Executando a Aplicação:**

*   **Servidor de Desenvolvimento:** `php artisan serve`
*   **Compilar assets do Frontend:** `npm run dev`
*   **Executor da Fila (Queue):** `php artisan queue:work`

## Testes

*   Para rodar a suíte de testes, execute: `php artisan test`

## Convenções de Desenvolvimento

*   **Rotas:** Definidas em `routes/web.php`.
*   **Controllers:** Localizados em `app/Http/Controllers`.
*   **Models:** Eloquent models estão no diretório `app/Models`.
*   **Views:** Templates Blade estão no diretório `resources/views`.
*   **Integração com API:** O `SummonerController` e os Jobs em background cuidam das interações com a API da Riot.
*   **Cache:** A aplicação utiliza o cache do Laravel extensivamente para melhorar a performance nas interações com a API da Riot.
*   **Jobs em Background:** A busca de dados na API da Riot é enfileirada como jobs para evitar longos tempos de carregamento para o usuário.