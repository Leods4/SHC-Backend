<?php

namespace App\Observers;

use App_Models_Usuario as Usuario; // Correção de namespace se o modelo for 'app/Models/Usuario.php'
use App\Models\Usuario as AppUsuario;
use App\Observers\Traits\RegistraHistorico;

class UsuarioObserver
{
    use RegistraHistorico;

    public function created(AppUsuario $usuario): void
    {
        $this->registrar($usuario, 'CREATED');
    }

    public function updated(AppUsuario $usuario): void
    {
        $this->registrar($usuario, 'UPDATED');
    }

    public function deleted(AppUsuario $usuario): void
    {
        $this->registrar($usuario, 'DELETED'); // Soft delete
    }
}