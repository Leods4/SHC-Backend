Documentação da API - Sistema de Horas Complementares (SHC)1. Visão GeralAPI RESTful desenvolvida para ser o backend de um sistema de gerenciamento de atividades complementares. A API oferece uma interface robusta para ser consumida por uma aplicação frontend (ex: React, Vue, Angular), gerenciando usuários, cursos, e o ciclo de vida de certificados.Framework: Laravel 12Banco de Dados: PostgreSQLAutenticação: Laravel Sanctum (API Tokens)Plataforma de Deploy (Sugerida): Render2. Configuração e Execução LocalPré-requisitos: PHP 8.2+, Composer, PostgreSQL.Clonar o repositório:git clone [https://github.com/Leods4/SHC-Backend.git](https://github.com/Leods4/SHC-Backend.git)
cd SHC-Backend
Instalar dependências:composer install
Configurar o ambiente:Copie o arquivo de exemplo: cp .env.example .envAbra o arquivo .env e configure as variáveis do banco de dados (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).Gerar a chave da aplicação:php artisan key:generate
Executar as migrações do banco de dados:php artisan migrate
Criar o link de armazenamento (ESSENCIAL para uploads):Este comando torna os arquivos enviados (certificados) acessíveis publicamente.php artisan storage:link
Iniciar o servidor local:php artisan serve
A API estará disponível em http://localhost:8000.3. Funcionalidades e Detalhes de ImplementaçãoEsta seção detalha as principais funcionalidades e como as regras de negócio foram implementadas.3.1 🔐 Autenticação e AutorizaçãoAutenticação (AuthController): Implementa registro, login, logout e renovação de tokens (refreshToken) utilizando Laravel Sanctum. As rotas são protegidas pelo middleware auth:sanctum.Autorização (Policies): A especificação pedia para "restringir o acesso... utilizando Policies". A implementação real definiu as seguintes regras de negócio em classes Policy dedicadas:UsuarioPolicy.php: Define que um usuário pode editar a si mesmo, mas apenas um ADMINISTRADOR pode deletar outros usuários. SECRETARIA e COORDENADOR possuem permissões de visualização ampliadas.CursoPolicy.php: Apenas ADMINISTRADOR e SECRETARIA podem criar ou deletar cursos, enquanto COORDENADOR pode atualizá-los.CertificadoPolicy.php: Contém a lógica mais complexa. Apenas ALUNOS podem criar certificados, e só podem editá-los se o status for ENTREGUE. A avaliação é restrita a COORDENADOR, SECRETARIA e ADMINISTRADOR.3.2 📦 Gestão de Certificados e UploadsUploads (CertificadoController): A rota POST /api/certificados processa requisições multipart/form-data para o upload de arquivos. O arquivo é armazenado em storage/app/public/certificados e o caminho é salvo no banco de dados.Validação (StoreCertificadoRequest): A especificação pedia "Validação... via classes FormRequest". A implementação garante que o arquivo seja do tipo PDF e tenha um tamanho máximo de 5MB. Também impede que certificados sejam submetidos com data futura.3.3 🧾 Histórico Automático de AlteraçõesRastreamento (Observers): A especificação pedia para "utilizar Model Observers". Foram criados UsuarioObserver e CertificadoObserver para "escutar" eventos do Eloquent (created, updated, deleted).Como Funciona: No evento updated, o observer utiliza getChanges() para registrar apenas os campos que foram alterados. No evento created, o estado completo do novo registro é salvo. Em todos os casos, Auth::id() é usado para registrar quem realizou a ação, criando um log de auditoria completo e automático.3.4 🧮 Cálculo de Horas e FiltrosCálculo de Progresso (UsuarioController@showProgresso): Endpoint dedicado que soma as horas_validadas de todos os certificados APROVADOS de um aluno e retorna o total, junto com as horas necessárias do curso.Filtros e Buscas (index methods): Os métodos index dos controladores (ex: CertificadoController) suportam filtros via query parameters, como ?status=APROVADO, permitindo buscas dinâmicas na API.3.5 🏗️ Arquitetura e Componentes de LigaçãoEnums (app/Enums): Foram utilizados PHP Enums para garantir a consistência e integridade dos dados para papéis de usuário, status e categorias de certificados. Eles servem como a "fonte da verdade", evitando o uso de strings mágicas no código.Provedores de Serviço (app/Providers):AuthServiceProvider.php: Este arquivo é responsável por "registrar" as Policies. É aqui que mapeamos um Modelo (Usuario::class) à sua respectiva Policy (UsuarioPolicy::class), informando ao Laravel como autorizar ações.AppServiceProvider.php: Este provedor registra os Observers. Ao vincular Usuario::class a UsuarioObserver::class, garantimos que o observer seja acionado automaticamente sempre que um usuário for criado, atualizado ou deletado.Configuração de CORS e Sanctum:Para permitir a comunicação com o frontend, o arquivo config/cors.php foi ajustado para permitir a origem do cliente e suportar credenciais (supports_credentials = true).Adicionalmente, a variável SANCTUM_STATEFUL_DOMAINS no arquivo .env foi configurada para informar ao Sanctum quais domínios frontend podem fazer requisições autenticadas.4. Referência da API (Endpoints)Rotas PúblicasMétodoEndpointDescriçãoPOST/api/auth/registerRegistra um novo usuário.POST/api/auth/loginAutentica um usuário e retorna um token.Rotas Protegidas (Exigem Bearer Token)MétodoEndpointDescriçãoPOST/api/auth/logoutFaz logout do usuário (revoga o token).POST/api/auth/refreshGera um novo token de acesso.GET/api/usuariosLista todos os usuários.POST/api/usuariosCria um novo usuário.GET/api/usuarios/{id}Exibe um usuário específico.PUT/api/usuarios/{id}Atualiza um usuário.DELETE/api/usuarios/{id}Deleta um usuário.GET/api/usuarios/{id}/progressoMostra o progresso de horas do usuário.GET/api/usuarios/{id}/historicoMostra o histórico de alterações do usuário.GET/api/cursosLista todos os cursos.POST/api/cursosCria um novo curso.GET/api/cursos/{id}Exibe um curso específico.PUT/api/cursos/{id}Atualiza um curso.DELETE/api/cursos/{id}Deleta um curso.GET/api/certificadosLista certificados (Aluno só vê os seus).POST/api/certificadosSubmete um novo certificado (com upload).GET/api/certificados/{id}Exibe um certificado específico.PUT/api/certificados/{id}Atualiza um certificado (se ainda não avaliado).DELETE/api/certificados/{id}Deleta um certificado (se ainda não avaliado).PATCH/api/certificados/{id}/avaliarAvalia (aprova/reprova) um certificado.GET/api/certificados/{id}/historicoMostra o histórico de alterações do certificado.5. Estrutura da Aplicação (Diagramas)<details><summary><strong>Clique para ver o Diagrama de Modelos (Completo)</strong></summary>classDiagram
    %% --- Enums ---
    class TipoUsuario {
        <<Enumeration>>
        ALUNO
        COORDENADOR
        SECRETARIA
        ADMINISTRADOR
    }
    class StatusCertificado {
        <<Enumeration>>
        ENTREGUE
        APROVADO
        REPROVADO
        APROVADO_COM_RESSALVAS
    }
    class CategoriaCertificado {
        <<Enumeration>>
        CIENTIFICO_ACADEMICA
        SOCIOCULTURAL
        PRATICA_PROFISSIONAL
    }
    %% --- Models ---
    class Usuario {
        +id: int
        +nome: String
        +email: String
        +senha: String (hash)
        +matricula: String
        +data_nascimento: Date
        +tipo: TipoUsuario
        +dados_adicionais: JSON
        +curso_id: int (nullable)
        +fase: int (nullable)
        +curso(): Curso
        +certificados(): List~Certificado~
        +cursosCoordenados(): List~Curso~
        +historicoResponsavel(): List~HistoricoAlteracao~
        +calcularHorasValidadas(): int
    }
   class Curso { 
        +id: int
        +nome: String
        +horasNecessarias: int
        +alunos(): List~Usuario~
        +coordenadores(): List~Usuario~
    }
    class Certificado {
        +id: int
        +usuario_id: int
        +categoria: CategoriaCertificado
        +status: StatusCertificado
        +carga_horaria_solicitada: int
        +horas_validadas: int
        +nome_certificado: String
        +instituicao: String
        +data_emissao: Date
        +observacao: String
        +arquivo: String
        +requerente(): Usuario
        +historico(): List~HistoricoAlteracao~
    }
    class HistoricoAlteracao {
        +id: int
        +responsavel_id: int
        +historicoable_type: String
        +historicoable_id: int
        +alteracao: JSON
        +observacao: String
        +data_alteracao: Date
        +responsavel(): Usuario
        +historicoable(): morphTo
    }
    %% --- Relacionamentos ---
    Usuario "0..*" -- "0..1" Curso : "pertence a"
    Curso "1" -- "0..*" Usuario : "contém alunos"
    Usuario "1" -- "0..*" Certificado : "requer"
    Usuario "1" -- "0..*" HistoricoAlteracao : "é responsável por"
    Curso "0..*" -- "0..*" Usuario : "é coordenado por"
    Certificado ..> HistoricoAlteracao : "historicoable"
    Usuario ..> HistoricoAlteracao : "historicoable"
</details><details><summary><strong>Clique para ver o Diagrama de Componentes (Completo)</strong></summary>classDiagram
    %% --- Classes Base Laravel ---
    class Controller { <<Base>> }
    class FormRequest { <<Request>> }
    class ModelObserver { <<Observer>> }
    %% --- Models (Stubs) ---
    class Usuario { <<Model>> }
    class Curso { <<Model>> }
    class Certificado { <<Model>> }
    class HistoricoAlteracao { <<Model>> }
    %% --- Controller de Autenticação ---
    class AuthController {
        +register(RegisterRequest $request)
        +login(LoginRequest $request)
        +logout(Request $request)
        +refreshToken(Request $request)
    }
    %% --- Controllers de Recurso ---
    class UsuarioController {
        +index(Request $request)
        +store(StoreUsuarioRequest $request)
        +show(Usuario $usuario)
        +update(UpdateUsuarioRequest $request, Usuario $usuario)
        +destroy(Usuario $usuario)
        +showProgresso(Usuario $usuario)
    }
    class CursoController {
        +index()
        +store(StoreCursoRequest $request)
        +show(Curso $curso)
        +update(UpdateCursoRequest $request, Curso $curso)
        +destroy(Curso $curso)
    }
    class CertificadoController {
        +index(Request $request)
        +store(StoreCertificadoRequest $request)
        +show(Certificado $certificado)
        +update(UpdateCertificadoRequest $request, Certificado $certificado)
        +destroy(Certificado $certificado)
        +avaliar(AvaliarCertificadoRequest $request, Certificado $certificado)
    }
    %% --- Controllers Aninhados ---
    class UsuarioHistoricoController {
        +index(Usuario $usuario)
    }
    class CertificadoHistoricoController {
        +index(Certificado $certificado)
    }
    %% --- Observers para Histórico ---
    class UsuarioObserver {
      +created(Usuario $usuario)
      +updated(Usuario $usuario)
    }
    class CertificadoObserver {
      +created(Certificado $certificado)
      +updated(Certificado $certificado)
    }
    %% --- Herança e Dependências ---
    Controller <|-- AuthController
    Controller <|-- UsuarioController
    Controller <|-- CursoController
    Controller <|-- CertificadoController
    Controller <|-- UsuarioHistoricoController
    Controller <|-- CertificadoHistoricoController
    ModelObserver <|-- UsuarioObserver
    ModelObserver <|-- CertificadoObserver
    
    AuthController ..> Usuario : "cria/autentica"
    AuthController ..> FormRequest : "valida"
    UsuarioController ..> Usuario : "gerencia"
    CursoController ..> Curso : "gerencia"
    CertificadoController ..> Certificado : "gerencia"
    UsuarioObserver ..> Usuario : "observa"
    CertificadoObserver ..> Certificado : "observa"
    UsuarioHistoricoController ..> HistoricoAlteracao : "lê"
    CertificadoHistoricoController ..> HistoricoAlteracao : "lê"
</details>
