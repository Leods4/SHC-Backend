# Documentação da API - Sistema de Horas Complementares (SHC) - v2

## Visão Geral
API RESTful desenvolvida para servir como backend de um sistema de gerenciamento de atividades complementares.

- **Framework:** Laravel 12  
- **Banco de Dados:** PostgreSQL  
- **Autenticação:** Laravel Sanctum (Stateless, API Tokens)  
- **Padrões:** PSR-12, Controllers enxutos, Services e Policies  
- **Logs:** Histórico automático e auditoria detalhada  

---

## Configuração e Execução Local

### Pré-requisitos
- PHP 8.2+
- Composer
- PostgreSQL

### Notas
- A API foi projetada prioritariamente para ambiente local.
- Disponibilizar arquivo `.env.example` com variáveis essenciais.

### CORS
Ajustar `config/cors.php` conforme a origem do frontend:
```php
paths: ['api/*'],
allowed_origins: ['http://localhost:3000', 'null'],


---

## Funcionalidades e Implementação

### 3.1 Autenticação e Autorização

#### Autenticação (AuthController)
- Implementa login, logout e recuperação de senha.
- Registro de usuários é ação **administrativa**.

#### Regras de Login
- Login usa CPF como identificador.
- Usuário desativado (soft delete) não pode logar.
- Senha padrão: data de nascimento (hash gerado).
- Recomenda-se troca da senha após 1º acesso.

#### Policies
| Policy | Regra |
|-------|-------|
| UsuarioPolicy | Usuário edita a si mesmo; apenas ADMIN altera role_id. Desativação via soft delete. |
| CertificadoPolicy | ALUNO pode editar apenas quando status = ENTREGUE. ADMIN/SECRETARIA/COORDENADOR podem avaliar. |

#### Lógica do Coordenador
- Possui `curso_id`
- Só atua em alunos com o mesmo `curso_id`

---

### 3.1.1 Fluxo de Token (Sanctum)
| Rota | Descrição |
|------|-----------|
| POST /api/auth/login | Retorna token |
| POST /api/auth/logout | Revoga token atual |

---

### 3.1.2 Desativação de Usuário (Soft Delete)
- Rota: `DELETE /api/usuarios/{id}`
- Model utiliza `use SoftDeletes;`
- Usuários desativados não aparecem em consultas e não podem logar.

---

### 3.2 Gestão de Certificados e Uploads

- Upload via multipart/form-data
- Armazenamento: `storage/app/certificados`
- Apenas PDF até 5MB
- Data de emissão não pode ser futura
- Suporta filtros na listagem (?status=APROVADO&aluno_id=123)

---

### 3.3 Histórico e Auditoria
- Implementado via Observers (UsuarioObserver, CertificadoObserver)
- Registro armazenado em `historico_alteracoes`

---

### 3.4 Cálculo de Horas
- Rota: `GET /api/usuarios/{id}/progresso`
- Retorna soma das horas validadas
- Também incluso em `/api/auth/me` para aluno

---

### 3.5 Arquitetura
- Status do certificado definidos em constantes
- Service Layer: UsuarioService, CertificadoService, UserSyncService
- Providers: AuthServiceProvider (Policies) e AppServiceProvider (Observers)

---

## Endpoints da API

### Respostas de Erro Padrão

{
"message": "Os dados fornecidos são inválidos.",
"errors": {
"cpf": ["O CPF informado já está em uso."]
}
}



### Rotas Públicas
| Método | Rota | Descrição |
|-------|------|-----------|
| POST | /api/auth/login | Login via CPF e senha |
| POST | /api/auth/forgot-password | Inicia recuperação |
| POST | /api/auth/reset-password | Conclui redefinição |

### Rotas Protegidas
| Método | Rota | Descrição |
|-------|------|-----------|
| GET | /api/auth/me | Retorna usuário autenticado |
| POST | /api/auth/logout | Revoga token |

#### Usuários
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | /api/usuarios | Lista (com filtros) |
| POST | /api/usuarios | Cria usuário (Admin/Secretaria) |
| GET | /api/usuarios/{id} | Exibe usuário |
| PUT | /api/usuarios/{id} | Atualiza usuário |
| DELETE | /api/usuarios/{id} | Desativa usuário (soft delete) |
| GET | /api/usuarios/{id}/progresso | Progresso de horas |
| GET | /api/usuarios/{id}/historico | Histórico |

#### Certificados
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | /api/certificados | Lista certificados |
| POST | /api/certificados | Submete certificado (upload) |
| GET | /api/certificados/{id} | Exibe certificado |
| PUT | /api/certificados/{id} | Atualiza (permitido só com status ENTREGUE) |
| DELETE | /api/certificados/{id} | Deleta (mesma regra acima) |
| POST | /api/certificados/{id}/avaliacao | Avalia certificado |
| GET | /api/certificados/{id}/historico | Histórico |
| GET | /api/certificados/{id}/download | Download PDF |

#### Administração
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | /api/cursos | Lista cursos |
| POST | /api/cursos | Cria curso |
| PUT | /api/cursos/{id} | Atualiza curso |
| DELETE | /api/cursos/{id} | Remove curso |
| GET | /api/categorias | Lista categorias |
| POST | /api/categorias | Cria categoria |
| PUT | /api/categorias/{id} | Atualiza categoria |
| DELETE | /api/categorias/{id} | Remove categoria |
| POST | /api/admin/sync-users | Importa/sincroniza usuários externos |

---

## Modelos de Dados (Resumo)

### Certificado (Status)

ENTREGUE
APROVADO
REPROVADO
APROVADO_COM_RESSALVAS



### Tabelas Principais
- roles
- cursos
- categorias
- usuarios
- certificados
- historico_alteracoes

---

## Validações (Form Requests)

### Usuário
- Campos básicos obrigatórios
- `curso_id` e `fase` obrigatórios apenas para ALUNO e COORDENADOR
- Senha padrão gerada automaticamente na criação

### Certificados
- PDF até 5MB
- Data não futura
- Dono só pode alterar quando status = ENTREGUE
- Avaliação só para ADMIN/SECRETARIA/COORDENADOR

---

## Regras de Segurança

- Validação de CPF
- Throttling de login
- Auditoria automática
- Policies aplicadas rigorosamente
- `.env.example` incluído

---


