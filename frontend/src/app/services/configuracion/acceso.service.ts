import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";
import {IAcceso, IListaAcceso, IListaAccesoParams} from "@interfaces/configuracion/acceso.interface";

@Injectable({
    providedIn: 'root'
})
export class AccesoService {
    constructor(private http: HttpClient) {

    }

    listarAccesos(params: IListaAccesoParams): Observable<HttpResponseApi<IListaAcceso[]>> {
        return this.http.get<HttpResponseApi<IListaAcceso[]>>('/api/configuracion/accesos/lista-acceso', {
            params: {...params},
            responseType: "json"
        });
    }

    agregarAcceso(params: IAcceso): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/accesos/agregar-acceso', {...params},
            {responseType: "json"}
        );
    }

    anularAcceso(params: IAcceso, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/accesos/anular-acceso', {...params, motivo},
            {responseType: "json"}
        );
    }

}
