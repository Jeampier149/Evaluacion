import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class EvaluarService {
    constructor(private http: HttpClient) {

    }

    listarEvalEmpleado(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/lista-evaluar', {
            params: {...params},
            responseType: "json"
        });
    }
      listarEvalEmpleadoRevisor(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/lista-evaluar-revisor', {
            params: {...params},
            responseType: "json"
        });
    }
       listarPeriodos(): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/lista-periodos', {
            params: {},
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


   listarHistorial(params:any,idEmpleado:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/listar-historial', {
            params: {...params,idEmpleado},
            responseType: "json"
        });
    }

  
     guardarEvaluar(data: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/guardar-evaluar', {data},
                {responseType: "json"}
            );
        }

     guardarEvaluarRevisor(data: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/guardar-evaluar-revisor', {data},
                {responseType: "json"}
            );
        }
    obtenerArgumentosFirma(data: any,periodo:any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/obtener-argumentos-firma', {data,periodo},
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
