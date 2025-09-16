export interface IListaProfesional {
    cargo: string;
    codMedico: string;
    desCargo: string;
    desEstado: string;
    desProfesion: string;
    documento: string;
    estado: string;
    nombres: string;
    profesion: string;
}

export interface IListaProfesionalParams {
    codigo: string;
    nombres: string;
    estado: string;
    longitud: number;
    pagina: number;
}
