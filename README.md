Backend do Sistema de Horas Complementares (SHC)

API RESTful desenvolvida em Laravel 12 com PostgreSQL para o gerenciamento de atividades complementares de alunos.

Visão Geral

Esta API serve como backend para uma aplicação frontend (ex: React, Vue), oferecendo um sistema robusto para gerenciar usuários (com diferentes papéis), cursos, e a submissão e avaliação de certificados de horas complementares.

Tecnologias Utilizadas

Framework: Laravel 12

Banco de Dados: PostgreSQL

Autenticação: Laravel Sanctum (API Tokens)

Linguagem: PHP 8.2+

Funcionalidades Principais

Autenticação e Autorização: Sistema completo de registro, login, logout e refresh de token. As permissões são baseadas em papéis (Aluno, Coordenador, Secretaria, Administrador).

Gerenciamento de Usuários: CRUD completo para usuários.

Gerenciamento de Cursos: CRUD completo para cursos.

Gerenciamento de Certificados:

Upload de arquivos (PDF) para submissão de certificados.

Fluxo de avaliação (Entregue -> Aprovado/Reprovado).

Validação de carga horária.

Histórico de Alterações: Rastreamento automático de todas as criações e alterações em usuários e certificados.

Cálculo de Progresso: Endpoint para calcular o total de horas validadas de um aluno e comparar com as horas necessárias do seu curso.

Como Executar o Projeto Localmente

Pré-requisitos: PHP 8.2+, Composer, PostgreSQL.

Clonar o repositório:

git clone [https://github.com/Leods4/SHC-Backend.git](https://github.com/Leods4/SHC-Backend.git)
cd SHC-Backend


Instalar dependências:

composer install


Configurar o ambiente:

Copie o arquivo de exemplo: cp .env.example .env

Abra o arquivo .env e configure as variáveis do banco de dados (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

Gerar a chave da aplicação:

php artisan key:generate


Executar as migrações do banco de dados:

php artisan migrate


Iniciar o servidor local:

php artisan serve


A API estará disponível em http://localhost:8000.

Estrutura da API (Endpoints)

Rotas Públicas

Método

Endpoint

Descrição

POST

/api/auth/register

Registra um novo usuário.

POST

/api/auth/login

Autentica um usuário e retorna um token.

Rotas Protegidas (Exigem Bearer Token)

Método

Endpoint

Descrição

Papéis Autorizados (Exemplo)

POST

/api/auth/logout

Faz logout do usuário (revoga o token).

Todos autenticados

POST

/api/auth/refresh

Gera um novo token de acesso.

Todos autenticados

GET

/api/usuarios

Lista todos os usuários.

Admin, Secretaria

POST

/api/usuarios

Cria um novo usuário.

Admin, Secretaria

GET

/api/usuarios/{id}

Exibe um usuário específico.

Dono, Admin, Coord, Sec

PUT

/api/usuarios/{id}

Atualiza um usuário.

Dono, Admin, Secretaria

DELETE

/api/usuarios/{id}

Deleta um usuário.

Admin

GET

/api/usuarios/{id}/progresso

Mostra o progresso de horas do usuário.

Dono, Admin, Coord, Sec

GET

/api/usuarios/{id}/historico

Mostra o histórico de alterações do usuário.

Admin, Coord, Sec

GET

/api/cursos

Lista todos os cursos.

Todos autenticados

POST

/api/cursos

Cria um novo curso.

Admin, Secretaria

GET

/api/cursos/{id}

Exibe um curso específico.

Todos autenticados

PUT

/api/cursos/{id}

Atualiza um curso.

Admin, Coord, Sec

DELETE

/api/cursos/{id}

Deleta um curso.

Admin

GET

/api/certificados

Lista certificados (Aluno só vê os seus).

Todos autenticados

POST

/api/certificados

Submete um novo certificado (com upload).

Aluno

GET

/api/certificados/{id}

Exibe um certificado específico.

Dono, Admin, Coord, Sec

PUT

/api/certificados/{id}

Atualiza um certificado (se ainda não avaliado).

Dono (se 'ENTREGUE')

DELETE

/api/certificados/{id}

Deleta um certificado (se ainda não avaliado).

Dono (se 'ENTREGUE'), Admin

PATCH

/api/certificados/{id}/avaliar

Avalia (aprova/reprova) um certificado.

Admin, Coord, Sec

GET

/api/certificados/{id}/historico

Mostra o histórico de alterações do certificado.

Dono, Admin, Coord, Sec

Testes

Para executar a suíte de testes automatizados, rode o seguinte comando:

php artisan test
