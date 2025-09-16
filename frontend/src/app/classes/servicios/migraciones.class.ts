export class MigracionesClass {
    modo: string = '';
    codigo: string = '';
    mensaje: string = '';
    documento: string = '';
    apellidoPaterno: string = '';
    apellidoMaterno: string = '';
    nombres: string = '';
    calidadMigratoria: string = '';

    constructor(data: {}) {
        Object.assign(this, data);
    }

    obtenerPrimerNombre() {
        return this.nombres.split(' ')[0];
    }

    obtenerSegundoNombre() {
        let nombresArray = this.nombres.split(' ');
        nombresArray.shift();
        return nombresArray ? nombresArray.join(' ') : '';
    }

}
