import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class PersonalEvaluarService {
    constructor(private http: HttpClient) {

    }

    listarEmpleados(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/listar-personal-evaluar', {
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


    listarEmpleadosNuevos(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/evaluacion/evaluar/listar-personal-nuevo-evaluar', {
            params: {...params},
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
