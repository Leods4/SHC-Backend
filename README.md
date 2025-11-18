```markdown
Documentação da API - SHC (Sistema de Horas Complementares)

1. Visão Geral  
Base URL: http://localhost:8000/api  
Framework: Laravel 12  
Autenticação: Token Bearer (Laravel Sanctum)  
Formato de Resposta: JSON (application/json)

2. Configuração Inicial  
Para rodar o projeto localmente, siga os passos abaixo:

Clone e Instale Dependências:  
**Bash**
```

git clone <repo_url>
cd shc-backend
composer install

```

Variáveis de Ambiente:  
Copie o `.env.example` para `.env` e configure o banco de dados.

Banco de Dados e Seed:  
Execute as migrations e o seed para criar os perfis e o usuário administrador inicial.  
**Bash**
```

php artisan migrate --seed

```

Isso criará um usuário `admin@fmp.edu.br` com senha `admin123`.

3. Autenticação e Segurança  
A API utiliza tokens de acesso (Sanctum). O front-end deve armazenar o token recebido no login (ex: no localStorage) e enviá-lo em todas as requisições subsequentes.

**Cabeçalhos Obrigatórios:**  
**HTTP**
```

Accept: application/json
Authorization: Bearer {seu_token_aqui}

```

### 🔐 Auth Endpoints

| Método | Endpoint              | Descrição                                    | Acesso       |
|-------|------------------------|-----------------------------------------------|--------------|
| POST  | /auth/login           | Realiza login e retorna Token + Dados         | Público      |
| POST  | /auth/logout          | Revoga o token atual                          | Autenticado  |
| POST  | /auth/change-password | Altera a senha do usuário logado              | Autenticado  |

**Exemplo de Payload (Login)**  
**JSON**
```

{
"cpf": "000.000.000-00",
"password": "senha_secreta"
}

```

4. Recursos e Endpoints  

### 🎓 Certificados (Atividades Complementares)

| Método | Endpoint                     | Descrição                                      | Permissão                 |
|--------|-------------------------------|------------------------------------------------|----------------------------|
| GET    | /certificados                | Lista certificados (dinâmico por perfil)       | Autenticado               |
| POST   | /certificados                | Envia novo certificado (multipart/form-data)   | Aluno                     |
| GET    | /certificados/{id}           | Detalhes de um certificado                     | Dono/Coord/Admin          |
| PATCH  | /certificados/{id}/avaliar   | Aprova/Reprova certificado                     | Coordenador               |

**Payload: Enviar Certificado (Aluno)**  
Tipo: multipart/form-data  
- categoria: string  
- nome_certificado: string  
- instituicao: string  
- data_emissao: date (Y-m-d)  
- carga_horaria_solicitada: int  
- arquivo: file (.pdf, max 10MB)

**Payload: Avaliar Certificado (Coordenador)**  
**JSON**
```

{
"status": "APROVADO",
"horas_validadas": 10,
"observacao": "Validação ok."
}

```

---

### 👥 Usuários (CRUD)

| Método | Endpoint                 | Descrição                            | Permissão  |
|--------|---------------------------|----------------------------------------|------------|
| GET    | /usuarios                 | Lista usuários (?tipo=ALUNO)          | Admin/Sec  |
| POST   | /usuarios                 | Cria novo usuário                      | Admin/Sec  |
| PUT    | /usuarios/{id}            | Atualiza usuário                       | Admin/Sec  |
| DELETE | /usuarios/{id}            | Remove usuário                         | Admin/Sec  |
| GET    | /usuarios/{id}/progresso  | Horas aprovadas vs necessárias         | Ver Regra* |
| POST   | /usuarios/avatar          | Atualiza avatar do usuário logado      | Próprio Usuário |

*Regra de Progresso: Admin/Sec veem todos; Coord vê do seu curso; Aluno vê apenas o seu.*

**Modelo de Usuário (JSON Response)**  
```

{
"id": 1,
"nome": "João Silva",
"email": "[joao@email.com](mailto:joao@email.com)",
"tipo": "ALUNO",
"curso": {
"id": 1,
"nome": "Direito"
},
"fase": 5
}

```

---

### ⚙️ Configurações e Auxiliares

| Método | Endpoint        | Descrição                         | Permissão |
|--------|------------------|------------------------------------|-----------|
| GET    | /configuracoes  | Retorna regras de negócio          | Admin     |
| PUT    | /configuracoes  | Atualiza regras de negócio         | Admin     |
| GET    | /cursos         | Lista cursos disponíveis           | Autenticado |

---

5. Dicionário de Dados (Enums)

**Tipo de Usuário (tipo)**  
- ALUNO  
- COORDENADOR  
- SECRETARIA  
- ADMINISTRADOR  

**Status do Certificado (status)**  
- ENTREGUE  
- APROVADO  
- REPROVADO  
- APROVADO_COM_RESSALVAS  

---

6. Tratamento de Erros

A API retorna códigos HTTP padrão:

- **401 Unauthorized**: Token inválido ou ausente  
- **403 Forbidden**: Sem permissão  
- **422 Unprocessable Entity**: Erros de validação  

**Exemplo (422):**  
```

{
"message": "The given data was invalid.",
"errors": {
"cpf": ["O campo cpf é obrigatório."]
}
}
