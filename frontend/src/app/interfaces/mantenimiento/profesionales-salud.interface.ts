export interface IListaProfesionales {
    apellidoMaterno: string;
    apellidoPaterno: string;
    cargo: string;
    codMedico: string;
    colegiatura: string;
    desCargo: string;
    desEstado: string;
    documento: string;
    estado: string;
    nombres: string;
}

export interface IListaProfesionalesParams {
    codigo: string,
    documento: string,
    apellidoPaterno: string,
    apellidoMaterno: string,
    nombres: string,
    cmp: string,
    estado: string,
    pagina: number,
    longitud: number
}

export interface IProfesional {
    apellidoMaterno: string;
    apellidoPaterno: string;
    cargo: string;
    codigo: string;
    colegio: string;
    colegiatura: string;
    estado: string;
    fechaIngreso: string;
    fechaNacimiento: string;
    nombres: string;
    numeroPlaza: string;
    profesion: string;
    rne: string;
    sexo: string;
    tipoDocumento: string;
    documento: string;
}
