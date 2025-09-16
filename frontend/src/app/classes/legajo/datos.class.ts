import {errorAlerta, warningAlerta} from "@shared/utils";

export class DatosClass {
    codigo: string | undefined;
    cui: string = '';
    tipoDocumento: string = '0';
    documento: string = '';
    primerNombre: string = '';
    segundoNombre: string = '';
    apellidoMaterno: string = '';
    apellidoPaterno: string = '';
    telefono: string = '';
    email: string = '';
    sexo: string = '';
    estadoSituacional: string = 'A';
    estado: string = 'A';
    estadoCivil: string = 'S';
    nivelInstruccion: string = '0';
    ocupacion: string = '0';
    centroLaboral: string = '';
    observacion: string = '';

    conyuge = {
        tipoFinanciador: '',
        nombres: '',
    }
    acompaniante = {
        nombres: '',
        parentesco: ''
    }
    madre = {
        historia: '',
        nombres: '',
        estado: '',
        telefono: ''
    }
    padre = {
        nombres: '',
        estado: '',
        telefono: ''
    }
    direccion = {
        tipoDireccion: '',
        direccion: '',
        numero: '',
        interior: '',
        lote: '',
        manzana: '',
        tipoLocalidad: '',
        localidad: '',
        ubigeo: ''
    }
    nacimiento = {
        edadGestar: 0,
        fecha: "",
        hora: "",
        lugarNacimiento: "3",
        ubigeo: "",
        peso: 0,
        tipoParto: ""
    }

    financiador: FinanciadorClass = new FinanciadorClass();
    atencion: AtencionClass = new AtencionClass();

    creado: string | undefined;
    modificado: string | undefined;

    _errores: any[] = [];
    _erroresAtencion: any[] = [];
    _erroresFinanciador: any[] = [];
    _alertas: any[] = [];
    _alertasAtencion: any[] = [];
    _alertasFinanciador: any[] = [];

    obtenerHistoria(id: string): DatosClass {
        return this;
    }

    /**
     *
     * @param tipo 1: Nueva HC / 2: Editar HC
     */
    validarErrores(tipo: number) {
        this._errores = [];
        this._erroresFinanciador = [];
        this._erroresAtencion = [];

        if (this.tipoDocumento !== '0') {
            if (this.documento.trim() === '') {
                this._errores.push('Falta el documento de identidad');
            }
        }

        if (this.apellidoMaterno.trim() === '') {
            this._errores.push('Falta el apellido materno');
        }

        if (this.apellidoPaterno.trim() === '') {
            this._errores.push('Falta el apellido paterno');
        }
        if (this.primerNombre.trim() === '') {
            this._errores.push('Falta el nombre');
        }
        if (!this.nacimiento.fecha || this.nacimiento.fecha.trim() === '') {
            this._errores.push('Falta la fecha de nacimiento');
        }
        if (this.sexo.toString().trim() === '') {
            this._errores.push('Seleccione un sexo');
        }
        if (this.tipoDocumento.trim() === '') {
            this._errores.push('Falta el tipo de documento');
        } else {
            if (this.tipoDocumento.trim() === '1' && this.documento.trim().length !== 8) {
                this._errores.push('El numero de DNI debe ser de 8 dígitos.');
            }
        }
        if (this.direccion.direccion.trim() === '' && this.direccion.localidad?.trim() === '') {
            this._errores.push('Ingrese una Dirección o Localidad');
        } else {
            if (this.direccion.numero?.trim() === '' && (this.direccion.manzana?.trim() === ''
                || this.direccion.lote?.trim() === '')) {
                this._errores.push('Ingrese un numero de casa o manzana y lote del domicilio');
            }
        }
        if (this.direccion.ubigeo?.trim() === '') {
            this._errores.push('Falta el ubigeo de domicilio');
        }

        if (this.email.trim() !== '') {
            let regExpEmail = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,4})+$/.test(this.email.trim());
            if (!regExpEmail) {
                this._errores.push('Correo electrónico no valido.');
            }
        }

        if (this.telefono.toString().trim() !== '') {
            let regExpNumber = /^[0-9]*$/.test(this.telefono.trim());
            if (!regExpNumber) {
                this._errores.push('Numero de teléfono no valido.');
            }
        }

        if (this.madre.telefono.toString().trim() !== '') {
            let regExpNumber = /^[0-9]*$/.test(this.madre.telefono.trim());
            if (!regExpNumber) {
                this._errores.push('Numero de teléfono de la madre, no valido.');
            }
        }

        if (this.padre.telefono.toString().trim() !== '') {
            let regExpNumber = /^[0-9]*$/.test(this.padre.telefono.trim());
            if (!regExpNumber) {
                this._errores.push('Numero de teléfono de la padre, no valido.');
            }
        }

        /* Validación Datos Familiares */

        /*     if (this.padre.tipoDocumento?.trim() === '') {
                 this._errores.push('Ingrese Documento del Padre');
             } else {
                 if (this.tipoDocumento.trim() === '1' && this.documento.trim().length !== 8) {
                     this._errores.push('El numero de DNI del Padre debe ser de 8 dígitos.');
                 }
             }*/

        let edad = this.obtenerEdad();
        if (edad !== undefined && edad! < 18) {
            let auxError = 'El paciente es menor de edad,';
            if (this.padre.nombres === '' && this.madre.nombres === '') {
                this._errores.push(auxError + ' ingrese los datos de los padres');
            }
            if (this.padre.telefono === '' && this.madre.telefono === '') {
                this._errores.push('Ingrese el numero de teléfono del alguno de los padres.');
            }
        }

        /* Validación Nacimiento */

        if (this.nacimiento.ubigeo.trim() === '') {
            this._errores.push('Falta el lugar de nacimiento');
        }

        if (tipo === 1) {
            if (!this.financiador?.validarErrores()) {
                this._erroresFinanciador = this.financiador?._errores;
            }
        }

        if (tipo === 1 && this.financiador?.tipoFinanciador === '1') {
            if (!this.atencion?.validarErrores()) {
                this._erroresAtencion = this.atencion?._errores;
            }
        }

        if (this._errores.length > 0) {
            console.group('Errores de Historia');
            console.error(this._errores);
            console.groupEnd();
            return false
        }

        return !(this._erroresFinanciador.length > 0 || this._erroresAtencion.length > 0);
    }

    validarAlertas() {
        this._alertas = [];

        if (this.telefono.toString().trim() === '') {
            this._alertas.push('Paciente sin numero de teléfono');
        }

        if (this._alertas.length > 0) {
            console.group('Alertas de Historia');
            console.warn(this._alertas);
            console.groupEnd();
            return false;
        }

        return true;
    }

    /**
     * Lanzar un sweetalert con los errores recolectados.
     */
    verErrores() {
        let erroresTxt = '';
        if (this._errores.length > 0) {
            erroresTxt += '<ul class="text-start"><h5>Datos historia</h5>';
            this._errores.map((error) => {
                erroresTxt += '<li>' + error + '</li>';
            });
            erroresTxt += '</ul>'
        }

        if (this._erroresFinanciador.length > 0) {
            erroresTxt += '<ul class="text-start"><h5>Datos de Financiador</h5>';
            this._erroresFinanciador.map((error) => {
                erroresTxt += '<li>' + error + '</li>';
            });
            erroresTxt += '</ul>'
        }

        if (this._erroresAtencion.length > 0) {
            erroresTxt += '<ul class="text-start"><h5>Datos de Primera Atención</h5>';
            this._erroresAtencion.map((error) => {
                erroresTxt += '<li>' + error + '</li>';
            });
            erroresTxt += '</ul>'
        }

        if (this._errores.length > 0 || this._erroresFinanciador.length > 0 || this._erroresAtencion.length > 0) {
            errorAlerta('Atención!', '', erroresTxt).then();
        }
    }

    /**
     * Lanzar un sweetalert con alternativas con las advertencias recolectadas
     */
    async verAdvertencias() {
        let alertasTxt = '';
        this._alertas.map(alert => {
            alertasTxt += '<li>' + alert + '</li>';
        });

        if (this._alertas.length > 0) {
            return await warningAlerta('Atención', '', 0, {
                html: '<ul style="text-align: left">' + alertasTxt + '</ul>',
                timerProgressBar: false,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: "Continuar",
                cancelButtonText: "Cancelar"
            }).then(response => {
                return response.isConfirmed
            })
        }
        return true
    }

    obtenerEdad() {
        if (this.nacimiento.fecha !== undefined || this.nacimiento.fecha !== '') {
            let fecha = new Date();
            let fechaNacimiento = new Date(this.nacimiento.fecha + 'T00:00');
            return fecha.getFullYear() - fechaNacimiento.getFullYear();
        }
        return;
    }
}

export class FinanciadorClass {
    tipoFinanciador = "1";
    codigoIpress = "";
    fechaAfiliacion = "";
    fechaConsulta = "";
    nroAfiliacion = {
        disa: '',
        tipoFormato: '',
        nroContrato: '',
        correlativo: ''
    };
    observacion = "";
    procedencia = "";

    _errores: any[] = []

    validarErrores() {
        this._errores = [];

        if (this.tipoFinanciador.trim() === '6') { // SIS
            if (this.codigoIpress.trim() === '') {
                this._errores.push('Seleccione un establecimiento.');
            }

            if (this.procedencia.toString().trim() === '') {
                this._errores.push('Seleccione la procedencia.');
            }
            if (this.nroAfiliacion.disa.trim() === '') {
                this._errores.push('Ingrese una DISA.');
            }
            if (this.nroAfiliacion.tipoFormato.trim() === '') {
                this._errores.push('Ingrese el lote.');
            }
            if (this.nroAfiliacion.nroContrato.trim() === '') {
                this._errores.push('Ingrese el nro de Contrato.');
            }
            if (this.fechaAfiliacion.trim() === '') {
                this._errores.push('Ingrese la fecha de filiación.');
            }
        }

        if (this._errores.length > 0) {
            console.group('Errores de Financiador');
            console.error(this._errores);
            console.groupEnd()
            return false
        }
        return true;
    }

    verErrores() {
        let erroresTxt = '<ul class="text-start">';
        this._errores.map((error) => {
            erroresTxt += '<li>' + error + '</li>';
        });
        erroresTxt += '</ul>'

        if (this._errores.length > 0) {
            errorAlerta('Atención!', '', erroresTxt).then();
        }
    }
}

export class AtencionClass {
    fechaDocumento = '';
    tipoDocumento = '';
    documento = '';
    especialidad = '00';
    departamento = '0';
    consultorio = '00';
    upss= '';

    _errores: any[] = [];

    validarErrores() {
        this._errores = [];
        if (this.fechaDocumento.length === 0) {
            this._errores.push('Falta la fecha de documento.');
        }
        if (this.tipoDocumento.length === 0) {
            this._errores.push('Falta el tipo de documento.');
        }
        if (this.documento.length === 0) {
            this._errores.push('Falta el documento.');
        }
        if (this.especialidad.length === 0) {
            this._errores.push('Seleccione una especialidad.');
        }
        if (this.departamento.length === 0) {
            this._errores.push('Ingrese un departamento de primera atención');
        }
        if (this.consultorio.length === 0) {
            this._errores.push('Ingrese un consultorio de primera atención');
        }
        if (this._errores.length > 0) {
            console.group('Errores de Atención');
            console.error(this._errores);
            console.groupEnd()
            return false
        }
        return true;
    }

    verErrores() {
        let erroresTxt = '<ul class="text-start">';
        this._errores.map((error) => {
            erroresTxt += '<li>' + error + '</li>';
        });
        erroresTxt += '</ul>'

        if (this._errores.length > 0) {
            errorAlerta('Atención!', '', erroresTxt).then();
        }
    }

}
