export interface IListaMenu {
    icono: string;
    padre: string;
    estado: string;
    ruta: string;
    id: string;
    orden: number;
    nombre: string
}

export interface IListaMenuParams {
    nombre: string,
    padre: string,
    orden: string,
    estado: string,
    pagina: number,
    longitud: number
}

export interface IListaMenuCombo {
    id: string,
    nombre: string,
    padre: string,
    estado: string,
}

export interface IMenu {
    icono: string;
    padre: string;
    ruta: string;
    id: string;
    orden: number;
    nombre: string
}
