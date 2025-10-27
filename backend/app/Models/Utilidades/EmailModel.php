<?php

namespace App\Models\Utilidades;

use Illuminate\Support\Facades\DB;
use PDO;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailModel
{
    private array|string $correos = [];
    private array|string $adjuntos = [];
    private array|string $copias = [];
    private array|string $copiasOcultas = [];
    private string $asunto = '';
    private bool $html = false;
    private string $cuerpo = '';
    private string $error = '';
    private string $contexto = '';
    private int $numeroCorreo = 5;

    /**
     * @param string $contexto Es la referencia para el log de errores.
     * @param int $numeroCorreo Es el número del ID de la tabla GRAL_CREDENCIALES para obtener el correo electrónico.
     * Con ENV APP_ENV en local, siempre será ID 5
     */
    public function __construct(string $contexto = '', int $numeroCorreo = 5)
    {
        $this->contexto = $contexto;
        $this->numeroCorreo = $numeroCorreo;
    }

    /**
     * Correos a la cual se desea enviar puede ser un string o un array de string
     * @param array|string $correos
     * @return $this
     */
    public function setCorreos(array|string $correos): EmailModel
    {
        $this->correos = $correos;
        return $this;
    }

    /**
     * Ruta de archivos adjuntos pueden ser un string o un array de string
     * Se valida que el documento se encuentre en la carpeta storage/app/public
     * @param array|string $adjuntos
     * @return $this
     */
    public function setAdjuntos(array|string $adjuntos): EmailModel
    {
        $this->adjuntos = $adjuntos;
        return $this;
    }

    /**
     * Texto de asunto del correo
     * @param string $asunto
     * @return $this
     */
    public function setAsunto(string $asunto): EmailModel
    {
        $this->asunto = $asunto;
        return $this;
    }

    /**
     * Flag que identifica si el cuerpo del correo es HTML
     * @param bool $html
     * @return $this
     */
    public function setHtml(bool $html): EmailModel
    {
        $this->html = $html;
        return $this;
    }

    /**
     * Cuerpo del correo, si es HTML activar con función setHTML(true)
     * @param string $cuerpo
     * @return $this
     */
    public function setCuerpo(string $cuerpo): EmailModel
    {
        $this->cuerpo = $cuerpo;
        return $this;
    }

    /**
     * Correos a la cual se le envía la copia puede ser un string o un array de string
     * @param array|string $copias
     * @return $this
     */
    public function setCopias(array|string $copias): EmailModel
    {
        $this->copias = $copias;
        return $this;
    }

    /**
     * Correos a la cual se le envía la copia oculta puede ser un string o un array de string
     * @param array|string $copiasOcultas
     * @return $this
     */
    public function setCopiasOcultas(array|string $copiasOcultas): EmailModel
    {
        $this->copiasOcultas = $copiasOcultas;
        return $this;
    }

    /**
     * Retorna el mensaje de error en caso la función enviar correo, falle.
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    public function enviarCorreo(): bool
    {
        if (!$this->validarEmail()) {
            return false;
        }

        if (!$this->validarAdjunto()) {
            return false;
        }

        $debug = env('APP_ENV', 'local');

        [$estado, $mensaje, $resultado] = $this->obtenerCredencialesCorreo($debug === 'local' ? 5 : $this->numeroCorreo);
        if (!$estado) {
            $this->error = 'Ocurrió un error en la base de datos. Comuníquese con el area de sistemas.';
            return false;
        }

        try {
            $email = new PHPMailer(true);
            $email->SMTPDebug = SMTP::DEBUG_OFF;                       //Enable verbose debug output
            $email->isSMTP();                                          //Send using SMTP
            $email->Host = $resultado['url'];                          //Set the SMTP server to send through
            $email->SMTPAuth = true;                                   //Enable SMTP authentication
            $email->Username = $resultado['usuario'];                  //SMTP username
            $email->Password = $resultado['token'];                    //SMTP password
            $email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          //Enable implicit TLS encryption
            $email->Port = 465;                                        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $email->setFrom($resultado['usuario'], 'HONADOMANI SAN BARTOLOME');

//            $email->addAddress('joe@example.net', 'Joe User');
            if (is_array($this->correos)) {
                foreach ($this->correos as $correo) {
                    $email->addAddress($correo);
                }
            } else {
                if (trim($this->correos)) {
                    $email->addAddress($this->correos);
                }
            }

//            $email->addCC('cc@example.com');
            if (is_array($this->copias)) {
                foreach ($this->copias as $copia) {
                    $email->addCC($copia);
                }
            } else {
                if (trim($this->copias) == '') {
                    $email->addCC($this->copias);
                }
            }

//            $email->addBCC('bcc@example.com');
            if (is_array($this->copiasOcultas)) {
                foreach ($this->copiasOcultas as $copiasOculta) {
                    $email->addBCC($copiasOculta);
                }
            } else {
                if (trim($this->copiasOcultas) == '') {
                    $email->addBCC($this->copiasOcultas);
                }
            }

//            $email->addReplyTo('info@example.com', 'Information');
//            Adjuntos
//            $email->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            if (is_array($this->adjuntos)) {
                foreach ($this->adjuntos as $adjunto) {
                    $email->addAttachment(storage_path('app/public/') . $adjunto);
                }
            } else {
                if (trim($this->adjuntos) !== '') {
                    $email->addAttachment(storage_path('app/public/') . $this->adjuntos);
                }
            }

            //Cuerpo
            $email->isHTML($this->html);
            $email->Subject = $this->asunto;
            $email->Body = $this->cuerpo;
//            $email->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $email->send();
            return true;
        } catch (Exception $e) {
            if (env('APP_DEBUG', true)) {
                $this->error = $e->getMessage();
            } else {
                $idLog = $this->guardarLogErrores($e);
                $this->error = 'Error enviando el correo - ID LOG: ' . $idLog;
            }
            return false;
        }
    }

    private function validarEmail(): bool
    {
        $estadoCorreo = true;
        $correoError = '';

        if (is_array($this->correos)) {
            foreach ($this->correos as $correo) {
                if (!filter_var($correo, FILTER_VALIDATE_EMAIL) or trim($correo) === '') {
                    $estadoCorreo = false;
                    $correoError = $correo;
                    break;
                }
            }
        } else {
            if (!filter_var($this->correos, FILTER_VALIDATE_EMAIL) or trim($this->correos) === '') {
                $estadoCorreo = false;
                $correoError = $this->correos;
            }
        }


        if (is_array($this->copias)) {
            foreach ($this->copias as $copia) {
                if (!filter_var($copia, FILTER_VALIDATE_EMAIL)) {
                    $estadoCorreo = false;
                    $correoError = $copia;
                    break;
                }
            }
        } else {
            if (!filter_var($this->copias, FILTER_VALIDATE_EMAIL) or trim($this->copias) !== '') {
                $estadoCorreo = false;
                $correoError = $this->copias;
            }
        }


        if (is_array($this->copiasOcultas)) {
            foreach ($this->copiasOcultas as $copiasOculta) {
                if (!filter_var($copiasOculta, FILTER_VALIDATE_EMAIL)) {
                    $estadoCorreo = false;
                    $correoError = $copiasOculta;
                    break;
                }
            }
        } else {
            if (!filter_var($this->copiasOcultas, FILTER_VALIDATE_EMAIL) or trim($this->copiasOcultas) !== '') {
                $estadoCorreo = false;
                $correoError = $this->copiasOcultas;
            }
        }

        if (!$estadoCorreo) {
            $this->error = 'El correo "' . $correoError . '" no es valido.';
        }

        if (trim($this->cuerpo) === '') {
            $this->error = 'El cuerpo del correo no puede estar vacío.';
            $estadoCorreo = false;
        }
        return $estadoCorreo;
    }

    private function validarAdjunto(): bool
    {
        $estadoAdjunto = true;
        $errorAdjunto = '';
        if (is_array($this->adjuntos)) {
            foreach ($this->adjuntos as $adjunto) {
                if (!file_exists(storage_path('app/public/') . $adjunto)) {
                    $estadoAdjunto = false;
                    $errorAdjunto = 'No existe el archivo adjunto: ' . $adjunto;
                    break;
                }
            }
        } else {
            if (!file_exists(storage_path('app/public/') . $this->adjuntos) && trim($this->adjuntos) <> '') {
                $estadoAdjunto = false;
                $errorAdjunto = 'No existe el archivo adjunto: ' . $this->adjuntos;
            }
        }

        if (!$estadoAdjunto) {
            $this->error = $errorAdjunto;
        }

        return $estadoAdjunto;
    }

    private function obtenerCredencialesCorreo(int $credencial): array
    {
        $conexion = DB::connection('sighos');
        $smtp = $conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.gral_sp_get_gral_credenciales ?,?,?');
        $smtp->bindParam(1, $credencial, PDO::PARAM_INT);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return [$estado, $mensaje, $resultados];
    }

    private function guardarLogErrores($errorLog): mixed
    {
        $conexion = DB::connection('sighos');
        $request = \request()->user();
        $usuario = 'SIN USUARIO';
        $perfil = 'SIN PERFIL';
        if ($request) {
            $user = $request->user();
            $usuario = $user->xg_Cod_Usuario;
            $perfil = $user->xg_Cod_Perfil;
        }
        $equipo = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $equipo = strtoupper(preg_replace('/(.sbdomain.local)/', "", gethostbyaddr($equipo)));

        $smtp = $conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.log_sp_ins_correo ?,?,?,?,?,?');
        $smtp->bindParam(1, $this->contexto);
        $smtp->bindParam(2, $errorLog);
        $smtp->bindParam(3, $usuario);
        $smtp->bindParam(4, $perfil);
        $smtp->bindParam(5, $equipo);
        $smtp->bindParam(6, $idLog, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 5);
        $smtp->execute();
        $smtp->closeCursor();
        return $idLog;
    }
}
