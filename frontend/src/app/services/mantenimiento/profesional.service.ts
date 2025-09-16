import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";
import {IListaProfesional, IListaProfesionalParams} from "@interfaces/mantenimiento/profesional.interface";


@Injectable({
    providedIn: 'root'
})
export class ProfesionalService {
    constructor(private http: HttpClient) {

    }

    listarProfesional(params: IListaProfesionalParams): Observable<HttpResponseApi<IListaProfesional[]>> {
        return this.http.get<HttpResponseApi<IListaProfesional[]>>('/api/mantenimiento/lista-profesional', {
            params: {...params},
            responseType: "json"
        });
    }

    buscarProfesional(codigo: string) {
        return this.http.get<HttpResponseApi<IListaProfesional>>('/api/mantenimiento/usuarios/obtener-profesional', {
            params: {codigo},
            responseType: "json"
        })
    }
}
