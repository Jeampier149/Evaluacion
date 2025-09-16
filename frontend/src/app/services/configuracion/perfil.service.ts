import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";
import {
    IListaPerfil, IListaPerfilCombo,
    IListaPerfilParams,
    IListaPerfilUsuarioParams, IListaUsuarioPerfil,
    IPerfil
} from "@interfaces/configuracion/perfil.interface";

@Injectable({
    providedIn: 'root'
})
export class PerfilService {
    constructor(private http: HttpClient) {

    }

    listarComboPerfiles(): Observable<HttpResponseApi<IListaPerfilCombo[]>> {
        return this.http.get<HttpResponseApi<IListaPerfilCombo[]>>('/api/configuracion/perfil/lista-perfil-combo', {
            responseType: "json"
        });
    }

    listarPerfiles(params: IListaPerfilParams): Observable<HttpResponseApi<IListaPerfil[]>> {
        return this.http.get<HttpResponseApi<IListaPerfil[]>>('/api/configuracion/perfil/lista-perfil', {
            params: {...params},
            responseType: "json"
        });
    }

    listarPerfilesPorUsuario(params: IListaPerfilUsuarioParams): Observable<HttpResponseApi<IListaUsuarioPerfil[]>> {
        return this.http.get<HttpResponseApi<IListaUsuarioPerfil[]>>('/api/configuracion/perfil/lista-perfil-usuario', {
            params: {...params},
            responseType: "json"
        });
    }

    obtenerPerfil(idPerfil: string): Observable<HttpResponseApi<IPerfil>> {
        return this.http.get<HttpResponseApi<IPerfil>>('/api/configuracion/perfil/obtener-perfil', {
            params: {idPerfil},
            responseType: "json"
        });
    }

    editarPerfil(params: IPerfil): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/perfil/editar-perfil', {...params},
            {responseType: "json"}
        );
    }

    guardarPerfil(params: IPerfil): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/perfil/guardar-perfil', {...params},
            {responseType: "json"}
        );
    }

    anularPerfil(idPerfil: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/perfil/anular-perfil', {idPerfil, motivo},
            {responseType: "json"}
        );
    }

    activarPerfil(idPerfil: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/perfil/activar-perfil', {idPerfil},
            {responseType: "json"}
        );
    }

}
