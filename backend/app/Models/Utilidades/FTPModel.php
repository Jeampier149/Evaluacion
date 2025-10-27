<?php

namespace App\Models\Utilidades;

use Error;
use ErrorException;
use FTP\Connection;

class FTPModel
{
    private false|Connection $ftp;
    private string $servidor;
    private string $usuario;
    private string $password;

    public function __construct()
    {
        if (!env('FTP_HOST')) {
            throw new Error('Agregue la variable FTP_HOST en el archivo de env.');
        }
        if (!env('FTP_USERNAME')) {
            throw new Error('Agregue la variable FTP_USERNAME en el archivo de env.');
        }
        if (!env('FTP_PASSWORD')) {
            throw new Error('Agregue la variable FTP_PASSWORD en el archivo de env.');
        }
        $this->servidor = env('FTP_HOST');
        $this->usuario = env('FTP_USERNAME');
        $this->password = env('FTP_PASSWORD');

        $this->ftp = ftp_connect($this->servidor);
        if (!$this->ftp) {
            throw new Error('No se pudo conectar al servidor FTP.');
        }

        $flag = ftp_login($this->ftp, $this->usuario, $this->password);
        if (!$flag) {
            throw new Error('No se pudo conectar al servidor FTP con los credenciales.');
        }
    }

    /**
     * Dirección IP de servidor FTP (Defecto: env(FTP_HOST))
     * @param string $servidor
     * @return $this
     */
    public function setServidor(string $servidor): FTPModel
    {
        $this->servidor = $servidor;
        return $this;
    }

    /**
     * Usuario para el servidor FTP (Defecto: env(FTP_USERNAME))
     * @param string $usuario
     * @return $this
     */
    public function setUsuario(string $usuario): FTPModel
    {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * Contraseña para el servidor FTP (Defecto: env(FTP_PASSWORD))
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): FTPModel
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $rutaDestino Ubicación del documento en FTP (NO INCLUIR slash final)
     * @param string $nomArchivo Nombre de archivo a subir con extensión
     * @param string $rutaOrigen Ruta de archivo en local (NO INCLUIR slash final)
     * @return bool
     */
    public function subirArchivo(string $rutaDestino, string $nomArchivo, string $rutaOrigen): bool
    {
        // Crear directorio en caso no exista
        if (ftp_nlist($this->ftp, $rutaDestino) === false) ftp_mkdir($this->ftp, $rutaDestino);
        // Subir archivo al servidor FTP
        return ftp_put($this->ftp, $rutaDestino . "/" . $nomArchivo, $rutaOrigen . "/" . $nomArchivo, FTP_BINARY);
    }

    /**
     * @param string $rutaOrigen Ubicación del documento en FTP (NO INCLUIR slash al final)
     * @param string $nomArchivo Nombre del archivo a obtener (INCLUIR extensión)
     * @param string $rutaDestino Ruta local de descarga del archivo (NO INCLUIR slash al final) (Defecto: storage/app/temp)
     * @return bool
     */
    public function obtenerArchivo(string $rutaOrigen, string $nomArchivo, string $rutaDestino = ""): bool
    {
        $rutaDestino = trim($rutaDestino) === "" ? storage_path('app/temp') : $rutaDestino;
        try {
            return ftp_get($this->ftp, $rutaDestino . "/" . $nomArchivo, $rutaOrigen . '/' . $nomArchivo, FTP_ASCII);
        } catch (ErrorException) {
            throw new Error("No se pudo obtener el archivo o no existe (" . $nomArchivo . ")");
        }
    }

    /**
     * Lista de archivos que se encuentren en la ruta FTP
     * @param string $rutaFTP
     * @return false|string[]
     */
    public function listarArchivos(string $rutaFTP): array|bool
    {
        return ftp_nlist($this->ftp, $rutaFTP);
    }

    public function cerrarFTP(): void
    {
        if ($this->ftp) {
            ftp_close($this->ftp);
        }
    }

    public function __destruct()
    {
        $this->cerrarFTP();
    }
}
