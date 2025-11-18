# Documentação da API - SHC (Sistema de Horas Complementares) - v1.1

===========================================================
1. VISÃO GERAL
===========================================================

Base URL:                http://localhost:8000/api
Framework:               Laravel 12
Autenticação:            Bearer Token (Laravel Sanctum)
Formato de Resposta:     JSON (application/json)


===========================================================
2. CONFIGURAÇÃO INICIAL
===========================================================

1. Clone o repositório:
   git clone <repo_url>
   cd shc-backend
   composer install

2. Configure o arquivo .env:
   Copie .env.example → .env e ajuste banco de dados.

3. Execute as migrations:
   php artisan migrate --seed

   → Será criado um usuário admin:
     Email: admin@fmp.edu.br
     Senha: admin123



===========================================================
3. AUTENTICAÇÃO E SEGURANÇA
===========================================================

Cabeçalhos obrigatórios:
----------------------------------------------------------------
Accept: application/json
Authorization: Bearer {token}
----------------------------------------------------------------

TABELA — Auth Endpoints
----------------------------------------------------------------
Método   | Endpoint              | Descrição                          | Acesso
---------|------------------------|--------------------------------------|---------
POST     | /auth/login           | Realiza login e retorna token       | Público
POST     | /auth/logout          | Revoga token                        | Autenticado
POST     | /auth/change-password | Altera senha                        | Autenticado
----------------------------------------------------------------

Exemplo payload:
{
  "cpf": "000.000.000-00",
  "password": "senha_secreta"
}



===========================================================
4. CERTIFICADOS — ENDPOINTS
===========================================================

TABELA — Endpoints de Certificados
-------------------------------------------------------------------------------------
Método   | Endpoint                     | Descrição                               | Acesso
---------|-------------------------------|-------------------------------------------|--------------------
GET      | /certificados                 | Lista certificados (com filtros)         | Autenticado
POST     | /certificados                 | Envia novo certificado                   | Aluno
GET      | /certificados/{id}            | Detalhes do certificado                  | Dono/Coord/Admin
PATCH    | /certificados/{id}/avaliar    | Aprovar/Reprovar certificado             | Coordenador
-------------------------------------------------------------------------------------


-----------------------------------------------------------
Filtros de busca GET /certificados
-----------------------------------------------------------
status        → ENTREGUE, APROVADO, REPROVADO  
aluno_id      → filtra por aluno  
search        → busca por nome/CPF  
data_inicio   → YYYY-MM-DD  
data_fim      → YYYY-MM-DD  
curso_id      → filtra por curso  


-----------------------------------------------------------
Regras por Perfil
-----------------------------------------------------------
Aluno:            filtros aplicam somente ao próprio aluno  
Coordenador:      pode filtrar por aluno_id e status=ENTREGUE  
Secretaria/Admin: acesso geral, filtros amplos  


-----------------------------------------------------------
Payload — Envio de Certificado (multipart/form-data)
-----------------------------------------------------------
categoria  
nome_certificado  
instituicao  
data_emissao (Y-m-d)  
carga_horaria_solicitada (int)  
arquivo (.pdf, até 10MB)


-----------------------------------------------------------
Payload — Avaliação do Coordenador
-----------------------------------------------------------
{
  "status": "APROVADO",
  "horas_validadas": 10,
  "observacao": "Validação ok."
}



===========================================================
5. USUÁRIOS — CRUD / PERFIL
===========================================================

TABELA — Endpoints de Usuários
----------------------------------------------------------------------------------------
Método   | Endpoint                  | Descrição                                | Acesso
---------|----------------------------|--------------------------------------------|--------------------
GET      | /usuarios                 | Lista usuários                             | Admin/Secretaria
POST     | /usuarios                 | Cria novo usuário                          | Admin/Secretaria
PUT      | /usuarios/{id}            | Atualiza dados                             | Admin/Sec/Próprio
DELETE   | /usuarios/{id}            | Remove usuário                             | Admin/Secretaria
GET      | /usuarios/{id}/progresso  | Retorna progresso de horas                 | Regra*
POST     | /usuarios/avatar          | Atualiza foto do próprio usuário           | Próprio Usuário
----------------------------------------------------------------------------------------

Regras de Edição:
- Admin/Secretaria → podem alterar tudo  
- Próprio usuário → apenas nome + email  
- Senha → /auth/change-password  
- Avatar → /usuarios/avatar  


Payload exemplo:
{
  "nome": "João Silva Editado",
  "email": "joao.novo@email.com",
  "tipo": "ALUNO",
  "curso_id": 1,
  "fase": 6
}


Modelo de retorno:
{
  "id": 1,
  "nome": "João Silva",
  "email": "joao@email.com",
  "tipo": "ALUNO",
  "curso": {
    "id": 1,
    "nome": "Direito"
  },
  "fase": 5
}



===========================================================
6. EXEMPLOS DE USO
===========================================================

1. Coordenador vendo certificados de um aluno:
   GET /api/certificados?aluno_id=42
   Authorization: Bearer {token}

2. Secretaria buscando aluno por nome:
   GET /api/certificados?search=Maria&curso_id=3

3. Aluno editando seus dados:
   PUT /api/usuarios/10
   Authorization: Bearer {token}

   {
     "nome": "Maria Souza Alterado",
     "email": "maria@email.com",
     "tipo": "ALUNO",
     "curso_id": 3,
     "fase": 4
   }



===========================================================
7. CONFIGURAÇÕES E CURSOS
===========================================================

TABELA — Endpoints de Configurações
------------------------------------------------------------
Método   | Endpoint         | Descrição                 | Acesso
---------|-------------------|----------------------------|--------
GET      | /configuracoes   | Retorna regras do sistema | Admin
PUT      | /configuracoes   | Atualiza regras           | Admin
GET      | /cursos          | Lista cursos              | Autenticado
------------------------------------------------------------



===========================================================
8. DICIONÁRIO DE DADOS (ENUMS)
===========================================================

Tipo de Usuário:
- ALUNO
- COORDENADOR
- SECRETARIA
- ADMINISTRADOR

Status do Certificado:
- ENTREGUE
- APROVADO
- REPROVADO
- APROVADO_COM_RESSALVAS



===========================================================
9. ERROS
===========================================================

Erros comuns:
- 401 → Token inválido/ausente
- 403 → Sem permissão
- 422 → Erro de validação

Exemplo 422:
{
  "message": "The given data was invalid.",
  "errors": {
    "cpf": ["O campo cpf é obrigatório."]
  }
}
