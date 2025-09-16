export interface IListaPerfil {
    descripcion_estado: string;
    estado: string;
    id: string;
    descripcion: string;
    observacion: string;
}

export interface IListaPerfilParams {
    id: string,
    descripcion: string,
    pagina: number,
    longitud: number
}

export interface IListaUsuarioPerfil {
    usuario: string;
    nombres: string;
    perfil_web: string;
    descripcion_web: string;
    perfil_fox: string;
    descripcion_fox: string;
    estado: string;
    descripcion_estado: string;
}

export interface IListaPerfilUsuarioParams {
    idPerfil: string,
    codigo_usuario: string,
    nombres: string,
    pagina: number,
    longitud: number
}

export interface IPerfil {
    id: string;
    descripcion: string;
    observacion: string;
}

export interface IListaPerfilCombo {
    id: string,
    nombre: string
}
