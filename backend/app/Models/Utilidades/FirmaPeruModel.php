<?php

namespace App\Models\Utilidades;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use PDO;

class FirmaPeruModel
{
    use HasFactory;

    private Connection $conexion;
    private string $token = '';
    private string $filtro = '.*FIR.*|.*FAU.*';
    private string $razonFirma = 'Soy el autor del documento';
    private string $cargo = '';
    private bool $isVisible = false;
    private int $posX = 390;
    private int $posY = 20;
    private int $fuente = 20;
    private int $pagina = 1;
    private string $nombrePOST = 'signed_file';
    private bool $unicoFirmante = false;
    private bool $estado = true;
    private array $errores = [];

    public function __construct()
    {
        $this->conexion = DB::connection('sighos');

        $this->obtenerToken();
    }

    private function obtenerToken(): void
    {
        $envNumero = env('APP_ENV') === 'production' ? 10 : 8;

        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.gral_sp_get_gral_credenciales ?,?,?');
        $smtp->bindParam(1, $envNumero, PDO::PARAM_INT);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();

        if ($estado === 1) {
            $this->token = $resultados['token'];
        }

        if ($estado === 0) {
            //Renovar token
            [$estadoCredenciales, $url, $usuario, $token] = $this->obtenerCredenciales();
            if ($estadoCredenciales) {
                $token = $this->generarToken($url, $usuario, $token);

                $envNumero = env('APP_ENV') === 'production' ? 10 : 8;
                $expiracion = time() + (82800); // 23 horas
                $expiracion = date("Y-m-d H:i:s", $expiracion);

                $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.gral_sp_upd_gral_credenciales ?,?,?');
                $smtp->bindParam(1, $envNumero, PDO::PARAM_INT);
                $smtp->bindParam(2, $token);
                $smtp->bindParam(3, $expiracion);
                $smtp->execute();
                $smtp->closeCursor();

                $this->token = $token;
            } else {
                $this->estado = false;
                $this->errores[] = 'No se pudo obtener credenciales para generar token.';
            }
        }
    }

    private function obtenerCredenciales(): array
    {
        $envNumero = env('APP_ENV') === 'production' ? 9 : 7;

        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.gral_sp_get_gral_credenciales ?,?,?');
        $smtp->bindParam(1, $envNumero, PDO::PARAM_INT);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();

        if ($estado === 1) {
            return [$estado, $resultados['url'], $resultados['usuario'], $resultados['token']];
        } else {
            $this->estado = false;
            $this->errores[] = $mensaje;
            return [$estado, '', '', ''];
        }
    }

    private function generarToken($url, $usuario, $password): bool|string
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'client_id=' . $usuario . '&client_secret=' . $password,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * Filtro para mostrar certificados (Defecto: Todos los certificados DNI)
     * Ejemplo:".FIR.*72342522.*" Solo muestra el certificado con tal número
     * @param string $filtro
     * @return $this
     */
    public function setFiltro(string $filtro): FirmaPeruModel
    {
        $this->filtro = $filtro;
        return $this;
    }

    /**
     * Razón de la firma (Defecto: Soy autor del documento)
     * @param string $razonFirma
     * @return $this
     */
    public function setRazonFirma(string $razonFirma): FirmaPeruModel
    {
        $this->razonFirma = $razonFirma;
        return $this;
    }

    /**
     * Apertura el visor para que puedan modificar la posición de la firma (Defecto: false)
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible(bool $isVisible): FirmaPeruModel
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    /**
     * Ubicación en horizontal de la firma (Defecto: 390)
     * @param int $posX
     * @return $this
     */
    public function setPosX(int $posX): FirmaPeruModel
    {
        $this->posX = $posX;
        return $this;
    }

    /**
     * Ubicación en vertical de la firma (Defecto: 20)
     * @param int $posY
     * @return $this
     */
    public function setPosY(int $posY): FirmaPeruModel
    {
        $this->posY = $posY;
        return $this;
    }

    /**
     * Tamaño de fuente de la firma (Defecto: 20)
     * @param int $fuente
     * @return $this
     */
    public function setFuente(int $fuente): FirmaPeruModel
    {
        $this->fuente = $fuente;
        return $this;
    }

    /**
     * Número de página en la cual estará la firma, empieza por 0 (Defecto: 0)
     * @param int $pagina
     * @return $this
     */
    public function setPagina(int $pagina): FirmaPeruModel
    {
        $this->pagina = $pagina;
        return $this;
    }

    /**
     * Cargo que aparece en la firma (Defecto:'')
     * @param string $cargo
     * @return $this
     */
    public function setCargo(string $cargo): FirmaPeruModel
    {
        $this->cargo = $cargo;
        return $this;
    }

    /**
     * Nombre del contenedor POST, que usara FirmaPeru para subir el archivo al servidor (Defecto: signed_file)
     * @param string $nombrePOST
     * @return $this
     */
    public function setNombrePOST(string $nombrePOST): FirmaPeruModel
    {
        $this->nombrePOST = $nombrePOST;
        return $this;
    }

    /**
     * Si es true, el documento no se puede volver a firmar
     * @param bool $unicoFirmante
     * @return $this
     */
    public function setUnicoFirmante(bool $unicoFirmante): FirmaPeruModel
    {
        $this->unicoFirmante = $unicoFirmante;
        return $this;
    }

    /**
     * Retorna el estado de la ejecución
     * @return bool
     */
    public function getEstado(): bool
    {
        return $this->estado;
    }

    /**
     * Retorna un arreglo de errores
     * @return array
     */
    public function getErrores(): array
    {
        return $this->errores;
    }

    /**
     * Genera el argumento para la firma digital con Firma Peru a partir de los parámetros ingresados
     *
     * @param string $nomDocumento Nombre del documento SIN FIRMAR
     * @param string $nomDocumentoFirmado Nombre del documento FIRMADO
     * @param string $rutaDocumento Ruta WEB de la ubicación del documento SIN FIRMAR (NO incluir "/" al final)
     * @param string $rutaDocumentoFirmado Ruta WEB a donde se cargara el documento FIRMADO
     * @return string Retorna el argumento en BASE64
     */
    public function obtenerArgumentosPDF(string $nomDocumento, string $nomDocumentoFirmado, string $rutaDocumento, string $rutaDocumentoFirmado): string
    {
        $url = env('APP_URL');

        $argumentos = '{
					"signatureFormat": "PAdES",
					"signatureLevel": "B",
					"signaturePackaging": "enveloped",
					"documentToSign": "' . $url . $rutaDocumento . '/' . $nomDocumento . '",
					"certificateFilter": "' . $this->filtro . '",
					"webTsa": "",
					"userTsa": "",
					"passwordTsa": "",
					"theme": "oscuro 2",
					"visiblePosition": ' . ($this->isVisible ? 'true' : 'false') . ',
					"contactInfo": "",
					"signatureReason": "' . $this->razonFirma . '",
					"bachtOperation": false,
					"oneByOne": false,
					"signatureStyle": 1,
					"imageToStamp": "' . $url . '/assets/img/logo_honadomani.png' . '",
					"stampTextSize": ' . $this->fuente . ',
					"stampWordWrap": 37,
					"role": "' . $this->cargo . '",
					"stampPage": ' . $this->pagina . ',
					"positionx": ' . $this->posX . ',
					"positiony": ' . $this->posY . ',
					"uploadDocumentSigned": "' .$url. $rutaDocumentoFirmado . '",
					"token": "' . $this->token . '",
					"certificationSignature": "' . ($this->unicoFirmante ? 'true' : 'false') . '"
				  }';
        return base64_encode($argumentos);
//        return $argumentos;
    }

    /**
     * Genera el argumento para Firma Peru y apertura la firma por lote 7-ZIP para PDF
     *
     * @param string $nomComprimido Nombre del comprimido a SIN FIRMAR
     * @param string $nomComprimidoFirmado Nombre del comprimido FIRMADO
     * @param string $rutaComprimido Ruta WEB de la ubicación del documento SIN FIRMAR (Incluir "/" al final)
     * @param string $rutaComprimidoFirmado Ruta WEB a donde se cargara el documento FIRMADO
     * @return string Retorna el argumento en BASE64
     */
    public function obtenerArgumentosZIP(string $nomComprimido, string $nomComprimidoFirmado, string $rutaComprimido, string $rutaComprimidoFirmado): string
    {
        $url = env('APP_URL');

        $argumentos = '{
					"signatureFormat": "PAdES",
					"signatureLevel": "B",
					"signaturePackaging": "enveloped",
					"documentToSign": "' . $url . $rutaComprimido . $nomComprimido . '",
					"certificateFilter": "' . $this->filtro . '",
					"webTsa": "",
					"userTsa": "",
					"passwordTsa": "",
					"theme": "oscuro 2",
					"visiblePosition": ' . ($this->isVisible ? 'true' : 'false') . ',
					"contactInfo": "",
					"signatureReason": "' . $this->razonFirma . '",
					"bachtOperation": true,
					"oneByOne": false,
					"signatureStyle": 1,
					"imageToStamp": "' . $url . '/assets/img/logo_honadomani.png' . '",
					"stampTextSize": ' . $this->fuente . ',
					"stampWordWrap": 37,
					"role": "' . $this->cargo . '",
					"stampPage": ' . $this->pagina . ',
					"positionx": ' . $this->posX . ',
					"positiony": ' . $this->posY . ',
					"uploadDocumentSigned": "' .$url. $rutaComprimidoFirmado . '",
					"token": "' . $this->token . '",
					"certificationSignature": "' . ($this->unicoFirmante ? 'true' : 'false') . '"
				  }';
        return base64_encode($argumentos);
//          return $argumentos;
    }

    /**
     * Genera el argumento inicial para FirmaPeru, solo contiene la ruta del cual obtendrá el argumento
     *
     * @param string $rutaArgumento Ruta web de la cual se obtendrá el argumento
     * @param string $extension Extension del documento que se busca firmar (pdf o 7z) Defecto: pdf
     * @return string
     */
    public function obtenerDatosArgumento(string $rutaArgumento, string $extension = 'pdf'): string
    {
        $url = env('APP_URL');

        $argumentos = '{
                     "param_url": "' . $url . $rutaArgumento . '",
                     "param_token": "' . str_pad(rand(0, 999999999), 11, "0", STR_PAD_LEFT) . '",
                     "document_extension": "' . $extension . '"
                    }';
        return base64_encode($argumentos);
    }
}
