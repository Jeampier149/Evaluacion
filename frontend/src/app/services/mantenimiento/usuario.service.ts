import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";
import {IListaUsuario, IListaUsuarioParams, IUsuario} from "@interfaces/mantenimiento/usuario.interface";


@Injectable({
    providedIn: 'root'
})
export class UsuarioService {
    constructor(private http: HttpClient) {

    }

    listarUsuario(params: IListaUsuarioParams): Observable<HttpResponseApi<IListaUsuario[]>> {
        return this.http.get<HttpResponseApi<IListaUsuario[]>>('/api/mantenimiento/usuarios/lista-usuario', {
            params: {...params},
            responseType: "json"
        });
    }

    obtenerUsuario(codigo: string): Observable<HttpResponseApi<IUsuario>> {
        return this.http.get<HttpResponseApi<IUsuario>>('/api/mantenimiento/usuarios/obtener-usuario', {
            params: {codigo},
            responseType: "json"
        });
    }

    guardarUsuario(usuario: IUsuario): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/mantenimiento/usuarios/guardar-usuario', {
            ...usuario
        }, {
            responseType: "json"
        });
    }

    editarUsuario(usuario: IUsuario): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/mantenimiento/usuarios/editar-usuario', {
            ...usuario
        }, {
            responseType: "json"
        });
    }

    anularUsuario(codigo: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/mantenimiento/usuarios/anular-usuario', {
            codigo, motivo
        }, {
            responseType: "json"
        });
    }

    activarUsuario(codigo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/mantenimiento/usuarios/activar-usuario', {
            codigo
        }, {
            responseType: "json"
        });
    }

    reestrablecerUsuario(codigo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/mantenimiento/usuarios/reestablecer-usuario', {
            codigo
        }, {
            responseType: "json"
        });
    }
    reporteUsuarios(){
        return this.http.get('/api/mantenimiento/usuarios/reporte-usuarios', { 
          params:{
        }, responseType: 'blob' });
       }
}
