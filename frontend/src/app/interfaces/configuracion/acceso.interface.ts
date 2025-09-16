export interface IListaAcceso {
    descripcion_estado: string;
    estado: string;
    id: string;
    perfil: string;
    menu: string;
    nombre_menu: string;
    menu_padre: string;
}

export interface IListaAccesoParams {
    idPerfil: string,
    idMenu: string,
    pagina: number,
    longitud: number
}

export interface IAcceso {
    idPerfil: string;
    idMenu: string;
}
