<?php

namespace App\Models\Utilidades;

use Error;
use Exception;

class SMBModel
{
    private bool $isLinux;
    private string $error = '';
    private bool $isDebug = false;

    public function getError(): string
    {
        return $this->error;
    }

    public function setIsDebug(bool $isDebug): SMBModel
    {
        $this->isDebug = $isDebug;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (PHP_OS === 'Linux') {
            $this->isLinux = true;
            // Comprobar instalación de paquete necesario para linux
            exec('which smbclient', $output);
            if (sizeof($output) === 0) {
                throw new Exception('No se encuentra instalado smbclient.');
            }
            // Comprobar si se tiene los credenciales WINDOWS DOMAIN
            if (!env('SMB_USERNAME')) {
                throw new Error('Agregue la variable SMB_USERNAME en el archivo de env.');
            }
            if (!env('SMB_PASSWORD')) {
                throw new Error('Agregue la variable SMB_PASSWORD en el archivo de env.');
            }
            if (!env('SMB_DOMAIN')) {
                throw new Error('Agregue la variable SMB_DOMAIN en el archivo de env.');
            }
        }
    }

    /**
     * @param string $rutaCompartida Dirección de red la cual comparte una carpeta o recurso. Ejem: //192.168.10.11/carpeta1
     * * @param string $archivoRemoto Ruta remota y nombre del archivo que se cargara. Ejem: /carpeta2/ejem.txt
     * * @param string $archivoLocal Ruta local y nombre del archivo para ser cargado. Ejem: /var/www/html/bbb.txt
     * @return bool
     */
    public function subirArchivo(string $rutaCompartida, string $archivoLocal, string $archivoRemoto): bool
    {
        $rutaCompartida = str_replace("\\", "/", $rutaCompartida);
        $archivoRemoto = str_replace("\\", "/", $archivoRemoto);
        $archivoLocal = str_replace("\\", "/", $archivoLocal);
        if ($this->isLinux) {
            $conexion = 'smbclient //' . $rutaCompartida . ' -U ' . env('SMB_USERNAME') . ' -W ' . env('SMB_DOMAIN') . ' --password=' . env('SMB_PASSWORD');
            $comando = $conexion . ' -c \'put ' . $archivoLocal . ' ' . $archivoRemoto . '\'; exit;';
            exec($comando, $output, $return_var);
            if ($return_var === 1) {
                if ($this->isDebug) {
                    $this->error = $comando . '<br>';
                    $this->error = $this->error . $output[0];
                } else {
                    $this->error = 'No se pudo cargar el archivo.';
                }
                return false;
            }
            return true;
        }
        $flag = copy($archivoLocal, $rutaCompartida . '/' . $archivoRemoto);
        if (!$flag) {
            $this->error = 'No se pudo cargar el archivo.';
            return false;
        }
        return true;
    }

    /**
     * @param string $rutaCompartida Dirección de red la cual comparte una carpeta o recurso. Ejem: //192.168.10.11/carpeta1
     * @param string $archivoRemoto Ruta remota y nombre de archivo que se desea descargar. Ejem: /carpeta2/ejem.txt
     * @param string $archivoLocal Ruta local y nombre del archivo. Ejem: /var/www/html/bbb.txt
     * @return bool
     */
    public function descargarArchivo(string $rutaCompartida, string $archivoRemoto, string $archivoLocal): bool
    {

        $rutaCompartida = str_replace("\\", "/", $rutaCompartida);
        $archivoRemoto = str_replace("\\", "/", $archivoRemoto);
        $archivoLocal = str_replace("\\", "/", $archivoLocal);
        if ($this->isLinux) {
            $conexion = 'smbclient //' . $rutaCompartida . ' -U ' . env('SMB_USERNAME') . ' -W ' . env('SMB_DOMAIN') . ' --password=' . env('SMB_PASSWORD');
            $comando = $conexion . ' -c \'get ' . $archivoRemoto . ' ' . $archivoLocal . '; exit;\'';
            exec($comando, $output, $return_var);
            if ($return_var === 1) {
                if ($this->isDebug) {
                    $this->error = $comando . '<br>';
                    $this->error = $this->error . $output[0];
                } else {
                    $this->error = 'No se pudo descargar el archivo.';
                }
                return false;
            }
            return true;
        }
        $flag = copy($rutaCompartida . '/' . $archivoRemoto, $archivoLocal);
        if (!$flag) {
            $this->error = 'No se pudo descargar el archivo.';
            return false;
        }
        return true;
    }

    /**
     * @param string $rutaCompartida Dirección de red la cual comparte una carpeta o recurso. Ejem: //192.168.10.11/carpeta1
     * @param string $archivoRemoto Ruta remota y nombre de archivo que se desea eliminar. Ejem: /carpeta2/ejem.txt
     * @return bool
     */
    public function eliminarArchivo(string $rutaCompartida, string $archivoRemoto): bool
    {
        $rutaCompartida = str_replace("\\", "/", $rutaCompartida);
        $archivoRemoto = str_replace("\\", "/", $archivoRemoto);
        if ($this->isLinux) {
            $conexion = 'smbclient //' . $rutaCompartida . ' -U ' . env('SMB_USERNAME') . ' -W ' . env('SMB_DOMAIN') . ' --password=' . env('SMB_PASSWORD');
            $comando = $conexion . ' -c \'del ' . $archivoRemoto . ' ; exit;\'';
            exec($comando, $output, $return_var);
            if ($return_var === 1) {
                if ($this->isDebug) {
                    $this->error = $comando . '<br>';
                    $this->error = $this->error . $output[0];
                } else {
                    $this->error = 'No se pudo eliminar el archivo.';
                }
                return false;
            }
            return true;
        }
        $flag = unlink($rutaCompartida . '/' . $archivoRemoto);
        if (!$flag) {
            $this->error = 'No se pudo eliminar el archivo.';
            return false;
        }
        return true;
    }
}
