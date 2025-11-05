<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    /**
     * Cria um novo usuário.
     * A validação dos dados deve ser feita antes (via StoreUsuarioRequest).
     *
     * @param array $data Dados validados do request
     * @return Usuario
     */
    public function criarUsuario(array $data): Usuario
    {
        // Define a senha padrão como a data de nascimento [cite: 13, 96]
        // Formato esperado da data_nascimento: Y-m-d
        // Remove os traços para a senha (ex: 1990-05-15 -> 19900515)
        $senhaPadrao = str_replace('-', '', $data['data_nascimento']);
        
        // Adiciona a senha hasheada aos dados [cite: 13, 96]
        $data['password'] = Hash::make($senhaPadrao);

        // Cria o usuário [cite: 60]
        $usuario = Usuario::create($data);

        return $usuario;
    }

    /**
     * Atualiza um usuário.
     *
     * @param Usuario $usuario O modelo a ser atualizado
     * @param array $data Dados validados do request
     * @return Usuario
     */
    public function atualizarUsuario(Usuario $usuario, array $data): Usuario
    {
        // Se uma nova senha foi enviada, hasheia ela
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $usuario->update($data);

        return $usuario;
    }
}