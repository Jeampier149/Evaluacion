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
  
     agregarEmpleadoEval(tipo:any,periodo: any, data?:any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/agregar-empleado-eval', {tipo,periodo,data},
                {responseType: "json"}
            );
        }


     obtenerEmpleadoEval(id:any,periodo: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/obtener-data-empleado', {periodo,id},
                {responseType: "json"}
            );
        }    
          

     obtenerSelects(): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/obtener-data-selects', {},
                {responseType: "json"}
            );
        }    


     editarEmpleadoEval(params: any,periodo:any,empleado:any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/evaluacion/evaluar/editar-personal-evaluar', {...params,periodo,empleado},
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
