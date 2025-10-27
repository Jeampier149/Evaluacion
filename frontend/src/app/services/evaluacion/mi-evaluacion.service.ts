import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class MiEvaluacionService {
    constructor(private http: HttpClient) {

    }


    listarEvalEmpleado(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/lista-evaluar', {
            params: {...params},
            responseType: "json"
        });
    }
       listarPeriodosMievaluacion(): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/lista-periodos-mi-evaluacion', {
            params: {},
            responseType: "json"
        });
    }

    listarEvalForm(idCategoria: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/lista-eval-form', {
                params: {idCategoria},
                responseType: "json"
            });
        }

    listarEvalFormF(idEmpleado:any,periodo:any,categoria:any): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/lista-eval-form-f', {
                params: {idEmpleado,periodo,categoria},
                responseType: "json"
            });
        }

      generarPdf(idEmpleado:any,periodo:any,categoria:any) {
            return this.http.get('/api/evaluacion/evaluar/generarPdf-Evaluacion', {
                params: {idEmpleado,periodo,categoria},
                responseType: "blob"
            });
        }


     guardarConformidadEvaluado(data: any,periodo:any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/miEvaluacion/guardar-conformidad-evaluado', {data,periodo},
                {responseType: "json"}
            );
        }

      obtenerArgumentosFirma(empleado: any,periodo:any,archivo:any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/miEvaluacion/obtener-argumentos-firma', {empleado,periodo,archivo},
                {responseType: "json"}
            );
        }
       imprimirFichaEvaluacion( archivo: string, periodo:string): Observable<Blob> {
        return this.http.get('/api/evaluar/imprimir-ficha-evaluacion', {
            params: {
                archivo,
                periodo
            },
            responseType: "blob"
        });
    }      






}
