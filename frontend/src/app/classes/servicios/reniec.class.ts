export class reniecClass {
    apellidoMaterno: string = '';
    apellidoPaterno: string = '';
    centroPoblado: string = '';
    codMensaje: string = '';
    continente: string = '';
    departamento: string = '';
    direccion: string = '';
    distrito: string = '';
    dni: string = '';
    fechaEmision: string = '';
    fechaNacimiento: string = '';
    modo: string = '';
    nombres: string = '';
    pais: string = '';
    provincia: string = '';
    sexo: string = '';
    ubigeoCentroPoblado: string = '';
    ubigeoContinente: string = '';
    ubigeoDepartamento: string = '';
    ubigeoDistrito: string = '';
    ubigeoPais: string = '';
    ubigeoProvincia: string = '';

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

    obtenerFechaNacimiento() {
        return this.fechaNacimiento.slice(0, 4) + '-' + this.fechaNacimiento.slice(4, 6) + '-' + this.fechaNacimiento.slice(6, 8)
    }

    obtenerUbigeo() {
        return this.ubigeoDepartamento + '' + this.ubigeoProvincia + '' + this.ubigeoDistrito;
    }

    obtenerUbigeoDescripcion() {
        return this.departamento + ' - ' + this.provincia + ' - ' + this.distrito;
    }

    obtenerSexo() {
        return parseInt(this.sexo) === 1 ? 'MASCULINO' : 'FEMENINO';
    }


}
