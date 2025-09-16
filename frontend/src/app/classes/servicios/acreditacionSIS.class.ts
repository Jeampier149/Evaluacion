export class acreditacionSISClass {
    tipoDocumento: string = '';
    documento: string = '';
    apellidoPaterno: string = '';
    apellidoMaterno: string = '';
    nombres: string = '';
    fechaAfiliacion: string = '';
    EESS: string = '';
    descripcionEESS: string = '';
    ubigeoEESS: string = '';
    descripcionUbigeoEESS: string = '';
    regimen: string = '';
    tipoSeguro: string = '';
    descripcionSeguro: string = '';
    contrato: string = '';
    fechaCaducidad: string = '';
    estado: string = '';
    tabla: string = '';
    numeroRegistro: string = '';
    genero: string = '';
    fechaNacimiento: string = '';
    ubigeo: string = '';
    disa: string = '';
    tipoFormato: string = '';
    numeroContrato: string = '';
    correlativo: string = '';
    idPlan: string = '';
    idGrupoPoblacional: string = '';


    constructor(data: {}) {
        Object.assign(this, data);
    }

    obtenerNombresCompletos() {
        return this.apellidoPaterno + ' ' + this.apellidoMaterno + ' ' + this.nombres;
    }

    obtenerPrimerNombre() {
        return this.nombres.split(' ')[0];
    }

    obtenerSegundoNombre() {
        let nombresArray = this.nombres.split(' ');
        nombresArray.shift();
        return nombresArray ? nombresArray.join(' ') : '';
    }

    obtenerFechaAfiliacion() {
        return this.fechaAfiliacion.slice(0, 4) + '-' + this.fechaAfiliacion.slice(4, 6) + '-' + this.fechaAfiliacion.slice(6, 8)
    }

    obtenerSexo() {
        return parseInt(this.genero) === 1 ? 'M' : 'F';
    }


}
