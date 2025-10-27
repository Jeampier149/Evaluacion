<?php

namespace App\Models\Utilidades;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;

class RefirmaModel
{
    use HasFactory;

    private Connection $conexion;
    private string $idCliente = '';
    private string $password = '';
    private string $filtro = '.*FIR.*|.*FAU.*';
    private string $razonFirma = 'Soy el autor del documento';
    private bool $isVisible = true;
    private int $posX = 390;
    private int $posY = 20;
    private int $fuente = 7;
    private int $pagina = 0;
    private string $nombrePOST = 'signed_file';
    private bool $isHttps = false;
    private bool $estado = true;
    private array $errores = [];

    public function __construct()
    {
        $this->conexion = DB::connection('sighos');
        $this->obtenerCredenciales();
    }

    private function obtenerCredenciales(): void
    {
        // $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.gral_sp_get_gral_credenciales ?,?,?');
        // $smtp->bindValue(1, 6, PDO::PARAM_INT);
        // $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        // $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        // $smtp->execute();
        // $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        // $smtp->closeCursor();

        // if ($estado === 1) {
            $this->idCliente = '6TkMl7K13a21YAc_tpyst4WB5sI';
            $this->password = 'BLtf30ayY1V4OkHhuTRn';
        // }

        // if ($estado === 0) {
        //     $this->estado = false;
        //     $this->errores[] = $mensaje;
        // }
    }

    /**
     * Filtro para mostrar certificados (Defecto: Todos los certificados DNI)
     * Ejemplo:".FIR.*72342522.*" Solo muestra el certificado con tal número
     * @param string $filtro
     * @return $this
     */
    public function setFiltro(string $filtro): RefirmaModel
    {
        $this->filtro = $filtro;
        return $this;
    }

    /**
     * Razón de la firma (Defecto: Soy autor del documento)
     * @param string $razonFirma
     * @return $this
     */
    public function setRazonFirma(string $razonFirma): RefirmaModel
    {
        $this->razonFirma = $razonFirma;
        return $this;
    }

    /**
     * Visibilidad de la firma (Defecto: true)
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible(bool $isVisible): RefirmaModel
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    /**
     * Ubicación en horizontal de la firma (Defecto: 390)
     * @param int $posX
     * @return $this
     */
    public function setPosX(int $posX): RefirmaModel
    {
        $this->posX = $posX;
        return $this;
    }

    /**
     * Ubicación en vertical de la firma (Defecto: 20)
     * @param int $posY
     * @return $this
     */
    public function setPosY(int $posY): RefirmaModel
    {
        $this->posY = $posY;
        return $this;
    }

    /**
     * Tamaño de fuente de la firma (Defecto: 7)
     * @param int $fuente
     * @return $this
     */
    public function setFuente(int $fuente): RefirmaModel
    {
        $this->fuente = $fuente;
        return $this;
    }

    /**
     * Número de página en la cual estará la firma, empieza por 0 (Defecto: 0)
     * @param int $pagina
     * @return $this
     */
    public function setPagina(int $pagina): RefirmaModel
    {
        $this->pagina = $pagina;
        return $this;
    }

    /**
     * Nombre del contenedor POST, que usara Refirma para subir el archivo al servidor (Defecto: signed_file)
     * @param string $nombrePOST
     * @return $this
     */
    public function setNombrePOST(string $nombrePOST): RefirmaModel
    {
        $this->nombrePOST = $nombrePOST;
        return $this;
    }

    /**
     * Protocolo de seguridad de la WEB de la cual se descargara y cargara el documento (Defecto: false)
     * @param bool $isHttps
     * @return $this
     */
    public function setIsHttps(bool $isHttps): RefirmaModel
    {
        $this->isHttps = $isHttps;
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
     * Genera el argumento para la firma digital con Refirma PDF a partir de los parámetros ingresados
     *
     * @param string $nomDocumento Nombre del documento SIN FIRMAR
     * @param string $nomDocumentoFirmado Nombre del documento FIRMADO
     * @param string $rutaDocumento Ruta WEB de la ubicación del documento SIN FIRMAR (Incluir "/" al final)
     * @param string $rutaDocumentoFirmado Ruta WEB a donde se cargara el documento FIRMADO
     * @return string Retorna el argumento en BASE64
     */
    public function obtenerArgumentosPDFInvoker(string $nomDocumento, string $nomDocumentoFirmado, string $rutaDocumento, string $rutaDocumentoFirmado): string
    {
        $url = env('APP_URL');

        $argumentos = '{
					"app":"pdf",
					"fileUploadUrl": "' . $url . $rutaDocumentoFirmado . '",
					"reason":"' . $this->razonFirma . '",
					"dcfilter":"' . $this->filtro . '",
					"type":"W",
					"clientId":"' . $this->idCliente . '",
					"clientSecret":"' . $this->password . '",
					"fileDownloadUrl":"' . $url . $rutaDocumento . $nomDocumento . '",
					"posx":"' . $this->posX . '",
					"posy":"' . $this->posY . '",
					"outputFile":"' . $nomDocumentoFirmado . '",
					"protocol":"' . ($this->isHttps ? 'S' : 'T') . '",
					"contentFile":"' . $nomDocumento . '",
					"stampAppearanceId":"0",
					"isSignatureVisible":"' . ($this->isVisible ? 'true' : 'false') . '",
					"idFile":"' . $this->nombrePOST . '",
					"fileDownloadLogoUrl":"' . $url . '/storage/img/iLogo1.png' . '",
					"fileDownloadStampUrl":"' . $url . '/storage/img/iFirma1.png' . '",
					"pageNumber":"' . $this->pagina . '",
					"maxFileSize":"52428800",
					"fontSize":"' . $this->fuente . '",
					"timestamp":"false"
				}';
        return base64_encode($argumentos);
//        return $argumentos;
    }

    /**
     * Genera el argumento para Refirma PCX y apertura la firma por lote 7-ZIP para PDF
     *
     * @param string $nomComprimido Nombre del comprimido SIN FIRMAR
     * @param string $nomComprimidoFirmado Nombre del comprimido FIRMADO (No puede ser el mismo que el de descarga)
     * @param string $rutaComprimido Ruta WEB de la ubicación del documento SIN FIRMAR (Incluir "/" al final)
     * @param string $rutaComprimidoFirmado Ruta WEB a donde se cargara el documento FIRMADO
     * @return string Retorna el argumento en BASE64
     */
    public function obtenerArgumentosZIPInvoker(string $nomComprimido, string $nomComprimidoFirmado, string $rutaComprimido, string $rutaComprimidoFirmado): string
    {
        $url = env('APP_URL');

        $argumentos = '{
					"app":"pcx",
					"mode":"lot-p",
					"fileUploadUrl": "' . $url . $rutaComprimidoFirmado .'",
					"reason":"' . $this->razonFirma .'",
					"dcfilter":"' . $this->filtro .'",
					"type":"W",
					"clientId":"' . $this->idCliente .'",
					"clientSecret":"' . $this->password .'",
					"fileDownloadUrl":"' . $url . $rutaComprimido . $nomComprimido .'",
					"posx":"' . $this->posX .'",
					"posy":"' . $this->posY .'",
					"outputFile":"' . $nomComprimidoFirmado .'",
					"protocol":"' . ($this->isHttps ? 'S' : 'T') .'",
					"contentFile":"' . $nomComprimido .'",
					"stampAppearanceId":"0",
					"isSignatureVisible":"' . ($this->isVisible ? 'true' : 'false') .'",
					"idFile":"' . $this->nombrePOST .'",
					"fileDownloadLogoUrl":"' . $url .'/storage/img/iLogo1.png' .'",
					"fileDownloadStampUrl":"' . $url .'/storage/img/iFirma1.png' .'",
					"pageNumber":"' . $this->pagina .'",
					"maxFileSize":"52428800",
					"fontSize":"' . $this->fuente .'",
					"timestamp":"false",
					"signatureLevel":"0"
				}';
        return base64_encode($argumentos);
    }

}
