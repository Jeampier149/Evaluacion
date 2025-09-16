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
       listarPeriodos(): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/lista-periodos', {
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

          










        
     editarEvaluar(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/editar-evaluar', {...params},
                {responseType: "json"}
            );
        }
    
    anularEvaluar(idEvaluar: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/anular-evaluar', {idEvaluar, motivo},
            {responseType: "json"}
        );
    }

    activarEvaluar(idEvaluar: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/activar-evaluar', {idEvaluar},
            {responseType: "json"}
        );
    }

}
