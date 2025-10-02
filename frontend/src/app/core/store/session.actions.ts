import {createAction, props} from '@ngrx/store';

export interface Session {
    logIn? : string,
    usuario?: string,
    cc_empleado?: string,
    nombres?: string,
    accesos?: any[],
    servicio?:string,
    departamento?:string,
    menu?:[],
    informacion?:[],
    categoria?: string,
    token?: string,
    perfil?: string,
    fechaExpiracion?: string
}

export const guardarSession = createAction('[Login] Guardar Session',
    props<{ session: Session }>());
export const eliminarSession = createAction('[Login] Eliminar Session');
