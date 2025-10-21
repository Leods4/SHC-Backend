Documentação da API - Sistema de Horas Complementares (SHC)

1. Visão Geral

API RESTful desenvolvida para ser o backend de um sistema de gerenciamento de atividades complementares. A API oferece uma interface robusta para ser consumida por uma aplicação frontend (ex: React, Vue, Angular), gerenciando usuários, cursos, e o ciclo de vida de certificados.

Framework: Laravel 12

Banco de Dados: PostgreSQL

Autenticação: Laravel Sanctum (API Tokens)

Plataforma de Deploy (Sugerida): Render

2. Configuração e Execução Local

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


Criar o link de armazenamento (ESSENCIAL para uploads):
Este comando torna os arquivos enviados (certificados) acessíveis publicamente.

php artisan storage:link


Iniciar o servidor local:

php artisan serve


A API estará disponível em http://localhost:8000.

3. Funcionalidades e Detalhes de Implementação

Esta seção detalha as principais funcionalidades e como as regras de negócio foram implementadas.

3.1 🔐 Autenticação e Autorização

Autenticação (AuthController): Implementa registro, login, logout e renovação de tokens (refreshToken) utilizando Laravel Sanctum. As rotas são protegidas pelo middleware auth:sanctum.

Autorização (Policies): A especificação pedia para "restringir o acesso... utilizando Policies". A implementação real definiu as seguintes regras de negócio em classes Policy dedicadas:

UsuarioPolicy.php: Define que um usuário pode editar a si mesmo, mas apenas um ADMINISTRADOR pode deletar outros usuários. SECRETARIA e COORDENADOR possuem permissões de visualização ampliadas.

CursoPolicy.php: Apenas ADMINISTRADOR e SECRETARIA podem criar ou deletar cursos, enquanto COORDENADOR pode atualizá-los.

CertificadoPolicy.php: Contém a lógica mais complexa. Apenas ALUNOS podem criar certificados, e só podem editá-los se o status for ENTREGUE. A avaliação é restrita a COORDENADOR, SECRETARIA e ADMINISTRADOR.

3.2 📦 Gestão de Certificados e Uploads

Uploads (CertificadoController): A rota POST /api/certificados processa requisições multipart/form-data para o upload de arquivos. O arquivo é armazenado em storage/app/public/certificados e o caminho é salvo no banco de dados.

Validação (StoreCertificadoRequest): A especificação pedia "Validação... via classes FormRequest". A implementação garante que o arquivo seja do tipo PDF e tenha um tamanho máximo de 5MB. Também impede que certificados sejam submetidos com data futura.

3.3 🧾 Histórico Automático de Alterações

Rastreamento (Observers): A especificação pedia para "utilizar Model Observers". Foram criados UsuarioObserver e CertificadoObserver para "escutar" eventos do Eloquent (created, updated, deleted).

Como Funciona: No evento updated, o observer utiliza getChanges() para registrar apenas os campos que foram alterados. No evento created, o estado completo do novo registro é salvo. Em todos os casos, Auth::id() é usado para registrar quem realizou a ação, criando um log de auditoria completo e automático.

3.4 🧮 Cálculo de Horas e Filtros

Cálculo de Progresso (UsuarioController@showProgresso): Endpoint dedicado que soma as horas_validadas de todos os certificados APROVADOS de um aluno e retorna o total, junto com as horas necessárias do curso.

Filtros e Buscas (index methods): Os métodos index dos controladores (ex: CertificadoController) suportam filtros via query parameters, como ?status=APROVADO, permitindo buscas dinâmicas na API.

3.5 🏗️ Arquitetura e Componentes de Ligação

Enums (app/Enums): Foram utilizados PHP Enums para garantir a consistência e integridade dos dados para papéis de usuário, status e categorias de certificados. Eles servem como a "fonte da verdade", evitando o uso de strings mágicas no código.

Provedores de Serviço (app/Providers):

AuthServiceProvider.php: Este arquivo é responsável por "registrar" as Policies. É aqui que mapeamos um Modelo (Usuario::class) à sua respectiva Policy (UsuarioPolicy::class), informando ao Laravel como autorizar ações.

AppServiceProvider.php: Este provedor registra os Observers. Ao vincular Usuario::class a UsuarioObserver::class, garantimos que o observer seja acionado automaticamente sempre que um usuário for criado, atualizado ou deletado.

Configuração de CORS e Sanctum:

Para permitir a comunicação com o frontend, o arquivo config/cors.php foi ajustado para permitir a origem do cliente e suportar credenciais (supports_credentials = true).

Adicionalmente, a variável SANCTUM_STATEFUL_DOMAINS no arquivo .env foi configurada para informar ao Sanctum quais domínios frontend podem fazer requisições autenticadas.

4. Referência da API (Endpoints)

4.1 Rotas Públicas

POST /api/auth/register

Registra um novo usuário.

POST /api/auth/login

Autentica um usuário e retorna um token.

4.2 Autenticação (Rotas Protegidas)

POST /api/auth/logout

Faz logout do usuário (revoga o token).

POST /api/auth/refresh

Gera um novo token de acesso.

4.3 Usuários (Rotas Protegidas)

GET /api/usuarios

Lista todos os usuários.

POST /api/usuarios

Cria um novo usuário.

GET /api/usuarios/{id}

Exibe um usuário específico.

PUT /api/usuarios/{id}

Atualiza um usuário.

DELETE /api/usuarios/{id}

Deleta um usuário.

GET /api/usuarios/{id}/progresso

Mostra o progresso de horas do usuário.

GET /api/usuarios/{id}/historico

Mostra o histórico de alterações do usuário.

4.4 Cursos (Rotas Protegidas)

GET /api/cursos

Lista todos os cursos.

POST /api/cursos

Cria um novo curso.

GET /api/cursos/{id}

Exibe um curso específico.

PUT /api/cursos/{id}

Atualiza um curso.

DELETE /api/cursos/{id}

Deleta um curso.

4.5 Certificados (Rotas Protegidas)

GET /api/certificados

Lista certificados (Aluno só vê os seus).

POST /api/certificados

Submete um novo certificado (com upload).

GET /api/certificados/{id}

Exibe um certificado específico.

PUT /api/certificados/{id}

Atualiza um certificado (se ainda não avaliado).

DELETE /api/certificados/{id}

Deleta um certificado (se ainda não avaliado).

PATCH /api/certificados/{id}/avaliar

Avalia (aprova/reprova) um certificado.

GET /api/certificados/{id}/historico

Mostra o histórico de alterações do certificado.

5. Estrutura da Aplicação

5.1 Estrutura de Dados

Enumerações (Tipos pré-definidos)

TipoUsuario (Enum):

ALUNO

COORDENADOR

SECRETARIA

ADMINISTRADOR

StatusCertificado (Enum):

ENTREGUE

APROVADO

REPROVADO

APROVADO_COM_RESSALVAS

CategoriaCertificado (Enum):

CIENTIFICO_ACADEMICA

SOCIOCULTURAL

PRATICA_PROFISSIONAL

Modelos (Entidades Principais)

Usuario

Atributos: id, nome, email, senha (hash), matricula, data_nascimento, tipo, dados_adicionais (JSON), curso_id, fase.

Relacionamentos: curso(), certificados(), cursosCoordenados(), historicoResponsavel().

Curso

Atributos: id, nome, horasNecessarias.

Relacionamentos: alunos(), coordenadores().

Certificado

Atributos: id, usuario_id, categoria, status, carga_horaria_solicitada, horas_validadas, nome_certificado, instituicao, data_emissao, observacao, arquivo.

Relacionamentos: requerente(), historico().

HistoricoAlteracao

Atributos: id, responsavel_id, historicoable_type, historicoable_id, alteracao (JSON), observacao, data_alteracao.

Relacionamentos: responsavel(), historicoable() (polimórfico).

5.2 Componentes e Lógica

Controllers

AuthController: register(), login(), logout(), refreshToken()

UsuarioController: index(), store(), show(), update(), destroy(), showProgresso()

CursoController: index(), store(), show(), update(), destroy()

CertificadoController: index(), store(), show(), update(), destroy(), avaliar()

UsuarioHistoricoController: index()

CertificadoHistoricoController: index()

Observers (Gatilhos de Eventos)

UsuarioObserver: created(), updated()

CertificadoObserver: created(), updated()
