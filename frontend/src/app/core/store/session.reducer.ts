import {createReducer, on} from '@ngrx/store';
import {eliminarSession, guardarSession, Session} from "./session.actions";

export const initialState: Session = {
    logIn: localStorage.getItem('logIn') ?? undefined,
    nombres: localStorage.getItem('nombres') ?? undefined,
    cc_empleado: localStorage.getItem('cc_empleado') ?? undefined,
    perfil: localStorage.getItem('perfil') ?? undefined,
    servicio: localStorage.getItem('servicio') ?? undefined,
    categoria: localStorage.getItem('categoria') ?? undefined,
    departamento: localStorage.getItem('departamento') ?? undefined,
    token: localStorage.getItem('token') ?? undefined,
    accesos: JSON.parse(localStorage.getItem('accesos') ?? '{}'),
    menu: JSON.parse(localStorage.getItem('menu') ?? '{}'),
    informacion: JSON.parse(localStorage.getItem('informacion') ?? '{}'),
    usuario: localStorage.getItem('usuario') ?? undefined,
    fechaExpiracion: localStorage.getItem('fechaExpiracion') ?? undefined
};


export const sessionReducer = createReducer(
    initialState,
    on(guardarSession, (state, action) => {
        localStorage.setItem('logIn', action.session.logIn ?? '');
        localStorage.setItem('nombres', action.session.nombres ?? '');
        localStorage.setItem('perfil', action.session.perfil ?? '');
        localStorage.setItem('servicio', action.session.servicio ?? '');
        localStorage.setItem('cc_empleado', action.session.cc_empleado?? '');
        localStorage.setItem('departamento', action.session.departamento ?? '');
        localStorage.setItem('token', action.session.token ?? '');
        localStorage.setItem('accesos', JSON.stringify(action.session.accesos ?? ''));
        localStorage.setItem('menu', JSON.stringify(action.session.menu ?? ''));
        localStorage.setItem('informacion', JSON.stringify(action.session.informacion ?? ''));
        localStorage.setItem('usuario', action.session.usuario ?? '');
        localStorage.setItem('categoria', action.session.categoria ?? '');
        localStorage.setItem('fechaExpiracion', action.session.fechaExpiracion ?? '');
        return {
            ...state, ...action.session
        }
    }),
    on(eliminarSession, (state) => {
        let resetSession: Session = {}
        localStorage.removeItem('logIn');
        localStorage.removeItem('nombres');
        localStorage.removeItem('perfil');
        localStorage.removeItem('cc_empleado');
        localStorage.removeItem('token');
        localStorage.removeItem('accesos');
        localStorage.removeItem('menu');
        localStorage.removeItem('informacion');
        localStorage.removeItem('categoria');
        localStorage.removeItem('usuario');
        localStorage.removeItem('servicio');
        localStorage.removeItem('departamento');
        localStorage.removeItem('fechaExpiracion');
        return {
            ...state, resetSession
        }
    })
);
