import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class UnidadService {
    constructor(private http: HttpClient) {

    }

    listarUnidad(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/datos-generales/unidad/lista-unidad', {
            params: {...params},
            responseType: "json"
        });
    }
    obtenerUnidad(idUnidad: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/datos-generales/unidad/obtener-unidad', {
                params: {idUnidad},
                responseType: "json"
            });
        }

   listarEmpleados(): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/datos-generales/unidad/listar-empleados', {
            params: {},
            responseType: "json"
        });
    }

    
     editarUnidad(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/datos-generales/unidad/editar-unidad', {...params},
                {responseType: "json"}
            );
        }
    
     guardarUnidad(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/datos-generales/unidad/guardar-unidad', {...params},
                {responseType: "json"}
            );
        }

        
    anularUnidad(idUnidad: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/datos-generales/unidad/anular-unidad', {idUnidad, motivo},
            {responseType: "json"}
        );
    }

    activarUnidad(idUnidad: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/datos-generales/unidad/activar-unidad', {idUnidad},
            {responseType: "json"}
        );
    }
}
