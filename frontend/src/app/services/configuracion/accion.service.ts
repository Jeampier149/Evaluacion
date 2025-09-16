import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";
import {IListaAccion} from "@interfaces/configuracion/accion.interface";

@Injectable({
    providedIn: 'root'
})
export class AccionService {
    constructor(private http: HttpClient) {

    }

    listarAcciones(idMenu: string): Observable<HttpResponseApi<IListaAccion[]>> {
        return this.http.get<HttpResponseApi<IListaAccion[]>>('/api/configuracion/lista-accion', {
            params: {idMenu},
            responseType: "json"
        });
    }

    guardarAccion(idMenu: string, descripcionAccion: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/guardar-accion', {idMenu, descripcionAccion},
            {responseType: "json"}
        );
    }

    anularAccion(idAccion: string, descripcion: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/anular-accion', {id: idAccion, descripcion},
            {responseType: "json"}
        );
    }
}
