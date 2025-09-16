import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";
import {IListaPerfil, IListaPerfilParams} from "@interfaces/mantenimiento/perfil.interface";


@Injectable({
    providedIn: 'root'
})
export class PerfilService {
    constructor(private http: HttpClient) {

    }

    listarPerfil(params: IListaPerfilParams): Observable<HttpResponseApi<IListaPerfil[]>> {
        return this.http.get<HttpResponseApi<IListaPerfil[]>>('/api/mantenimiento/lista-perfil', {
            params: {...params},
            responseType: "json"
        });
    }

    buscarPerfil(codigo: string) {
        return this.http.get<HttpResponseApi<IListaPerfil>>('/api/mantenimiento/usuarios/obtener-perfil', {
            params: {codigo},
            responseType: "json"
        })
    }
}
