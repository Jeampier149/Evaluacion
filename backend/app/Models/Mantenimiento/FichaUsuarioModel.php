<?php

namespace App\Models\Mantenimiento;

class FichaUsuarioModel
{
    public ?string $codigo;
    public ?string $nombres;
    public ?string $perfil;
    public ?string $estado;


    public function setCodigo(?string $codigo): FichaUsuarioModel
    {
        $this->codigo = $codigo;
        return $this;
    }

    public function setNombres(?string $nombres): FichaUsuarioModel
    {
        $this->nombres = $nombres;
        return $this;
    }

    public function setPerfil(?string $perfil): FichaUsuarioModel
    {
        $this->perfil = $perfil;
        return $this;
    }


    public function setEstado(?string $estado): FichaUsuarioModel
    {
        $this->estado = $estado;
        return $this;
    }
   


    public function validarFicha(): array
    {
        $errores = [];
        $estado = true;

        if (trim($this->codigo) === '') {
            $estado = false;
            $errores[] = 'Ingrese un código.';
        }

        if (trim($this->nombres) === '') {
            $estado = false;
            $errores[] = 'Ingrese los nombres.';
        }

        if (trim($this->perfil) === '') {
            $estado = false;
            $errores[] = 'Ingrese un perfil.';
        }

        return [$estado, $errores];
    }

}
