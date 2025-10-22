# Sistema de Horas Complementares (SHC) - Backend

## 1\. Sobre o Projeto

Esta é uma API RESTful desenvolvida em Laravel 12 para ser o backend de um sistema de gerenciamento de atividades complementares.

A API oferece uma interface robusta para ser consumida por uma aplicação frontend (como React, Vue ou Angular), permitindo o gerenciamento de usuários, cursos e todo o ciclo de vida dos certificados de horas complementares.

## 2\. Tecnologias Utilizadas

  * **Framework:** Laravel 12
  * **Banco de Dados:** PostgreSQL
  * **Autenticação:** Laravel Sanctum (API Tokens)
  * **Linguagem:** PHP 8.2+
  * **Gerenciador de Dependências:** Composer

## 3\. Principais Funcionalidades

A API implementa diversas regras de negócio e padrões de arquitetura para garantir um sistema robusto e seguro.

  * **Autenticação e Autorização:**
      * Sistema completo de registro, login, logout e renovação de tokens (refresh token) usando Laravel Sanctum.
      * Todas as rotas de recursos são protegidas pelo middleware `auth:sanctum`.
      * Uso de *Policies* (ex: `UsuarioPolicy`, `CertificadoPolicy`) para definir regras de negócio granulares sobre quem pode visualizar, criar, atualizar ou deletar recursos.
  * **Gestão de Certificados e Uploads:**
      * Endpoint para upload de certificados (`POST /api/certificados`) que processa requisições `multipart/form-data`.
      * Os arquivos são armazenados com segurança em `storage/app/public/certificados`.
      * Validação robusta via *FormRequest* (`StoreCertificadoRequest`) que garante que o arquivo seja PDF, tenha no máximo 5MB e que a data de emissão não seja futura.
  * **Histórico Automático de Alterações (Log de Auditoria):**
      * Uso de *Model Observers* (`UsuarioObserver`, `CertificadoObserver`) para "escutar" eventos do Eloquent como `created`, `updated` e `deleted`.
      * O sistema registra automaticamente *quem* realizou a ação (`Auth::id()`) e, em casos de atualização, registra apenas os campos que foram alterados (`getChanges()`).
  * **Cálculo de Progresso e Filtros:**
      * Endpoint dedicado (`GET /api/usuarios/{id}/progresso`) que soma todas as `horas_validadas` de certificados com status `APROVADO` de um aluno.
      * Os endpoints de listagem (ex: `GET /api/certificados`) suportam filtros via *query parameters* (ex: `?status=APROVADO`) para buscas dinâmicas.
  * **Arquitetura e Integridade de Dados:**
      * Uso de **PHP Enums** para papéis de usuário, status e categorias de certificados, garantindo a consistência dos dados e evitando "strings mágicas" no código.
      * Registro centralizado de Policies no `AuthServiceProvider` e de Observers no `AppServiceProvider`.

## 4\. Configuração e Execução Local

Siga os passos abaixo para configurar e executar o projeto em seu ambiente local.

**Pré-requisitos:**

  * PHP 8.2+
  * Composer
  * PostgreSQL

**Passos de Instalação:**

1.  Clone o repositório:

    ```bash
    git clone https://github.com/Leods4/SHC-Backend.git
    cd SHC-Backend
    ```

2.  Instale as dependências do Composer:

    ```bash
    composer install
    ```

3.  Configure o arquivo de ambiente:

    ```bash
    cp .env.example .env
    ```

4.  Abra o arquivo `.env` e configure suas variáveis de banco de dados (PostgreSQL):

    ```ini
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=sua_db
    DB_USERNAME=seu_usuario
    DB_PASSWORD=sua_senha
    ```

5.  Gere a chave da aplicação:

    ```bash
    php artisan key:generate
    ```

6.  Execute as migrações do banco de dados:

    ```bash
    php artisan migrate
    ```

7.  **ESSENCIAL:** Crie o link simbólico para o armazenamento. Isso torna os arquivos de upload (certificados) acessíveis publicamente:

    ```bash
    php artisan storage:link
    ```

8.  Inicie o servidor local:

    ```bash
    php artisan serve
    ```

A API estará disponível em `http://localhost:8000`.

### Configuração para Frontend (CORS e Sanctum)

Para permitir que sua aplicação frontend consuma esta API:

1.  Ajuste o arquivo `config/cors.php` para permitir a origem do seu cliente (ex: `http://localhost:3000`).
2.  Adicione o domínio do seu frontend à variável `SANCTUM_STATEFUL_DOMAINS` no seu arquivo `.env` para permitir requisições autenticadas.

## 5\. Referência da API (Endpoints)

A API possui rotas públicas para autenticação e rotas protegidas para o gerenciamento dos recursos.

### Rotas Públicas (Autenticação)

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `POST` | `/api/auth/register` | Registra um novo usuário. |
| `POST` | `/api/auth/login` | Autentica um usuário e retorna um token. |
| `POST` | `/api/auth/logout` | Faz logout do usuário (revoga o token). |
| `POST` | `/api/auth/refresh` | Gera um novo token de acesso. |

### Rotas Protegidas (Usuários)

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `GET` | `/api/usuarios` | Lista todos os usuários. |
| `POST` | `/api/usuarios` | Cria um novo usuário. |
| `GET` | `/api/usuarios/{id}` | Exibe um usuário específico. |
| `PUT` | `/api/usuarios/{id}` | Atualiza um usuário. |
| `DELETE` | `/api/usuarios/{id}` | Deleta um usuário. |
| `GET` | `/api/usuarios/{id}/progresso` | Mostra o progresso de horas do usuário. |
| `GET` | `/api/usuarios/{id}/historico` | Mostra o histórico de alterações do usuário. |

### Rotas Protegidas (Cursos)

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `GET` | `/api/cursos` | Lista todos os cursos. |
| `POST` | `/api/cursos` | Cria um novo curso. |
| `GET` | `/api/cursos/{id}` | Exibe um curso específico. |
| `PUT` | `/api/cursos/{id}` | Atualiza um curso. |
| `DELETE` | `/api/cursos/{id}` | Deleta um curso. |

### Rotas Protegidas (Certificados)

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `GET` | `/api/certificados` | Lista certificados (Aluno só vê os seus). |
| `POST` | `/api/certificados` | Submete um novo certificado (com upload). |
| `GET` | `/api/certificados/{id}` | Exibe um certificado específico. |
| `PUT` | `/api/certificados/{id}` | Atualiza um certificado (se ainda não avaliado). |
| `DELETE` | `/api/certificados/{id}` | Deleta um certificado (se ainda não avaliado). |
| `PATCH` | `/api/certificados/{id}/avaliar` | Avalia (aprova/reprova) um certificado. |
| `GET` | `/api/certificados/{id}/historico` | Mostra o histórico de alterações do certificado. |

## 5\. Referência da API (Endpoints)

### Enumerações (Tipos pré-definidos)

| TipoUsuario |
| :--- |
| ALUNO |
| COORDENADOR |
| SECRETARIA |
| ADMINISTRADOR |

| StatusCertificado |
| :--- |
| ENTREGUE |
| APROVADO |
| REPROVADO |
| APROVADO_COM_RESSALVAS |

| CategoriaCertificado |
| :--- |
| CIENTIFICO_ACADEMICA |
| SOCIOCULTURAL |
| PRATICA_PROFISSIONAL |

---

### Modelos (Entidades Principais)

#### Usuario
**Atributos:**
- +id: int  
- +nome: String  
- +email: String  
- +senha: String (hash)  
- +matricula: String  
- +data_nascimento: Date  
- +tipo: TipoUsuario  
- +dados_adicionais: JSON  
- +curso_id: int (opcional)  
- +fase: int (opcional)  

**Métodos/Relacionamentos:**
- +curso(): Curso (pertence a)  
- +certificados(): List<Certificado> (requer)  
- +cursosCoordenados(): List<Curso> (é coordenado por)  
- +historicoResponsavel(): List<HistoricoAlteracao> (é responsável por)  
- +calcularHorasValidadas(): int  

---

#### Curso
**Atributos:**
- +id: int  
- +nome: String  
- +horasNecessarias: int  

**Métodos/Relacionamentos:**
- +alunos(): List<Usuario> (contém alunos)  
- +coordenadores(): List<Usuario> (é coordenado por)  

---

#### Certificado
**Atributos:**
- +id: int  
- +usuario_id: int  
- +categoria: CategoriaCertificado  
- +status: StatusCertificado  
- +carga_horaria_solicitada: int  
- +horas_validadas: int  
- +nome_certificado: String  
- +instituicao: String  
- +data_emissao: Date  
- +observacao: String  
- +arquivo: String  

**Métodos/Relacionamentos:**
- +requerente(): Usuario  
- +historico(): List<HistoricoAlteracao>  

---

#### HistoricoAlteracao
**Atributos:**
- +id: int  
- +responsavel_id: int  
- +historicoable_type: String  
- +historicoable_id: int  
- +alteracao: JSON  
- +observacao: String  
- +data_alteracao: Date  

**Métodos/Relacionamentos:**
- +responsavel(): Usuario  
- +historicoable(): morphTo (Relacionamento polimórfico)  

---

2. Diagrama de Componentes (Controllers e Lógica)

### Controller de Autenticação
**AuthController**
- +register(RegisterRequest $request)  
- +login(LoginRequest $request)  
- +logout(Request $request)  
- +refreshToken(Request $request)  

---

### Controllers de Recurso (CRUD)

**UsuarioController**
- +index(Request $request)  
- +store(StoreUsuarioRequest $request)  
- +show(Usuario $usuario)  
- +update(UpdateUsuarioRequest $request, Usuario $usuario)  
- +destroy(Usuario $usuario)  
- +showProgresso(Usuario $usuario)  

**CursoController**
- +index()  
- +store(StoreCursoRequest $request)  
- +show(Curso $curso)  
- +update(UpdateCursoRequest $request, Curso $curso)  
- +destroy(Curso $curso)  

**CertificadoController**
- +index(Request $request)  
- +store(StoreCertificadoRequest $request)  
- +show(Certificado $certificado)  
- +update(UpdateCertificadoRequest $request, Certificado $certificado)  
- +destroy(Certificado $certificado)  
- +avaliar(AvaliarCertificadoRequest $request, Certificado $certificado)  

---

### Controllers Aninhados (para Histórico)

**UsuarioHistoricoController**
- +index(Usuario $usuario)  

**CertificadoHistoricoController**
- +index(Certificado $certificado)  

---

### Observers (Gatilhos de Eventos)

**UsuarioObserver**
- +created(Usuario $usuario)  
- +updated(Usuario $usuario)  

**CertificadoObserver**
- +created(Certificado $certificado)  
- +updated(Certificado $certificado)  
