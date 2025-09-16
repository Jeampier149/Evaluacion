import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class PeriodoService {
    constructor(private http: HttpClient) {

    }

    listarPeriodo(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/periodo/lista-periodo', {
            params: {...params},
            responseType: "json"
        });
    }
    obtenerPeriodo(idPeriodo: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/evaluacion/periodo/obtener-periodo', {
                params: {idPeriodo},
                responseType: "json"
            });
        }

   listarEmpleados(): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/periodo/listar-empleados', {
            params: {},
            responseType: "json"
        });
    }

    
     editarPeriodo(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/periodo/editar-periodo', {...params},
                {responseType: "json"}
            );
        }
    
     guardarPeriodo(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/periodo/guardar-periodo', {...params},
                {responseType: "json"}
            );
        }
    generarFormatos(idPeriodo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/evaluacion/periodo/generar-formatos', {idPeriodo},
            {responseType: "json"}
        );
    }
        
    anularPeriodo(idPeriodo: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/evaluacion/periodo/anular-periodo', {idPeriodo, motivo},
            {responseType: "json"}
        );
    }

    activarPeriodo(idPeriodo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/evaluacion/periodo/activar-periodo', {idPeriodo},
            {responseType: "json"}
        );
    }
}
