<?php

namespace App\Models\Utilidades;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compresor7ZipModel extends Model
{
    use HasFactory;

    private bool $isLinux = false;
    private bool $estado = true;
    private string $error = '';

    private string $rutaArchivoComprimido;
    private string $nombreArchivo;
    private array $archivos = [];
    private array $carpetas = [];
    private bool $isOpen = false;
    private string $formato = '7z';

    /**
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (PHP_OS === 'Linux') {
            $this->isLinux = true;
        }

        $this->comprobar7ZIP();
    }

    /**
     * @throws Exception
     */
    public function comprobar7ZIP(): void
    {
        if ($this->isLinux) {
            exec('which 7z', $output);
            if (sizeof($output) === 0) {
                throw new Exception('No se encuentra instalado 7Zip. Instale p7zip-full.');
            }
        }
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Seleccione el formato del comprimido. 7z,zip,gzip,bzip2,tar (Defecto: 7z)
     * @param string $formato
     * @return $this
     */
    public function setFormato(string $formato): Compresor7ZipModel
    {
        $formatos = ['7z', 'zip', 'gzip', 'bzip2', 'tar'];
        if (in_array($formato, $formatos)) {
            $this->formato = $formato;
        }
        return $this;
    }

    /**
     * Apertura un comprimido
     * @param string $nombre Nombre del comprimido (No incluir el formato)
     * @param string $ruta Ruta en la cual se guardara el comprimido (Defecto: storage/app/temp)
     * @return void
     */
    public function crearComprimido(string $nombre, string $ruta = ''): void
    {
        if (!$this->isOpen) {
            $this->nombreArchivo = $nombre;
            $this->rutaArchivoComprimido = trim($ruta) === '' ? storage_path('app/temp') : $ruta;
            $this->isOpen = true;
        }
    }

    /**
     * Agregue los archivos que desea comprimir
     * @param string $archivo
     * @return void
     */
    public function agregarArchivo(string $archivo): void
    {
        $this->archivos[] = $archivo;
    }

    /**
     * Agregue una carpeta que desea comprimir
     * @param string $carpeta Ruta de carpeta a comprimir
     * @return void
     */
    public function agregarCarpeta(string $carpeta): void
    {
        $this->carpetas[] = $carpeta;
    }

    /**
     * Genera el comprimido con los parámetros ingresados
     * @return void
     */
    public function cerrarComprimido(): void
    {
        $flag = true;
        if ($this->isLinux) {
            $exec = '7z';
        } else {
            $exec = resource_path('misc') . '/7zr.exe';
        }

        if (!$this->validarArchivos()) {
            $flag = false;
        }

        if (!$this->validarCarpeta()) {
            $flag = false;
        }

        if ($flag) {
            $cadena = implode(' ', $this->archivos);
            $carpetas = array_map(function ($item) {
                return $item . '/*';
            }, $this->carpetas);
            $cadenaCarpeta = implode(' ', $carpetas);
            $comando = $exec;
            // Agregar formato
            $comando = $comando . ' -t' . $this->formato;
            // Agregar nombre y ruta del comprimido
            $comando = $comando . ' a ' . $this->rutaArchivoComprimido . '/' . $this->nombreArchivo;
            // Agregar archivos
            $comando = $comando . ' ' . $cadena;
            // Agregar carpetas
            $comando = ' ' . $comando . ' ' . $cadenaCarpeta;
            exec($comando, $output, $code);
            $this->executionCode($code, $comando);
        }

        $this->nombreArchivo = '';
        $this->archivos = [];
        $this->isOpen = false;
    }

    private function validarArchivos(): bool
    {
        $flag = true;
        foreach ($this->archivos as $archivo) {
            if (!file_exists($archivo)) {
                $flag = false;
                $this->estado = false;
                $this->error = 'Archivo no encontrado. Ruta: (' . $archivo . ')';
                break;
            }
        }
        return $flag;
    }

    private function validarCarpeta(): bool
    {
        $flag = true;
        foreach ($this->carpetas as $carpeta) {
            if (!is_dir($carpeta)) {
                $flag = false;
                $this->estado = false;
                $this->error = 'Carpeta no encontrada. Ruta: (' . $carpeta . ')';
                break;
            } else {
                $files = array_diff(scandir($carpeta), array('.', '..'));
                if (sizeof($files) === 0) {
                    $flag = false;
                    $this->estado = false;
                    $this->error = 'Carpeta vacía. Ruta: (' . $carpeta . ')';
                    break;
                }
            }
        }
        return $flag;
    }

    private function executionCode($code, $comando): void
    {
        switch ($code) {
            case 0:
            {
                $this->estado = true;
                break;
            }
            case 1:
            {
                $this->error = 'Existen advertencias.';
                $this->estado = false;
                break;
            }
            case 2:
            {
                $this->error = 'Existen errores.';
                $this->estado = false;
                break;
            }
            case 7:
            {
                $this->error = 'Parámetros incorrectos.';
                $this->estado = false;
                break;
            }
            case 8:
            {
                $this->error = 'Memoria insuficiente para la operación.';
                $this->estado = false;
                break;
            }
            default:
            {
                $this->error = 'Error desconocido. (' . $comando . ')';
                $this->estado = false;
            }
        }
    }

    /**
     * Descomprime un fichero comprimido en la ruta ingresada
     * @param string $nombre Nombre del comprimido (incluir extension)
     * @param string $rutaArchivo Ruta en la cual se encuentra el comprimido
     * @param string $rutaDescomprimido Ruta en la cual se descomprime (Defecto: storage/app/temp/Nombre del archivo_marca de tiempo)
     * @return void
     */
    public function descomprimirArchivo(string $nombre, string $rutaArchivo, string $rutaDescomprimido = ''): void
    {
        if ($this->isLinux) {
            $exec = '7z';
        } else {
            $exec = resource_path('misc') . '/7zr.exe';
        }

        if (file_exists($rutaArchivo . '/' . $nombre)) {
            $nombreSExt = explode('.', $nombre, -1);
            $rutaDescomprimido = trim($rutaDescomprimido) === '' ? storage_path('app/temp/') . $nombreSExt[0] . '_' . time() : $rutaDescomprimido;
            $comando = $exec;
            // Agregando ruta donde se descomprime
            $comando = $comando . ' -o' . $rutaDescomprimido;
            // Agregando
            $comando = $comando . ' -y x ' . $rutaArchivo . '/' . $nombre;
            exec($comando, $output, $code);
            $this->executionCode($code, $comando);
        } else {
            $this->estado = false;
            $this->error = 'Archivo no encontrado.';
        }
    }

}
