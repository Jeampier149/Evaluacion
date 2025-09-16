import {createAction, props} from '@ngrx/store';

export interface Session {
    logIn? : string,
    usuario?: string,
    nombres?: string,
    accesos?: any[],
    servicio?:string,
    departamento?:string,
    menu?:[],
    token?: string,
    perfil?: string,
    fechaExpiracion?: string
}

export const guardarSession = createAction('[Login] Guardar Session',
    props<{ session: Session }>());
export const eliminarSession = createAction('[Login] Eliminar Session');
