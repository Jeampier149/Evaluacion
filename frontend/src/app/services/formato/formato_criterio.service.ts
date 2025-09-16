import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class FormatoCriterioService {
    constructor(private http: HttpClient) {

    }

    listarFormatoCriterio(factor:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/formato/formato_criterio/lista-formato_criterio', {
            params: {factor},
            responseType: "json"
        });
    }
    
    listarFactor(categoria:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/formato/formato_criterio/lista-factor', {
            params: {categoria},
            responseType: "json"
        });
    }
    obtenerFormatoCriterio(idFormatoCriterio: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/formatos/formato_criterio/obtener-formato_criterio', {
                params: {idFormatoCriterio},
                responseType: "json"
            });
        }

    buscarFormatoCriterios(codigo: string) {
        return this.http.get<HttpResponseApi>('/api/formatos/formato_criterio/obtener-formato_criterio', {
            params: {codigo},
            responseType: "json"
        })
    }
    
     editarFormatoCriterio(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/formatos/formato_criterio/editar-formato_criterio', {...params},
                {responseType: "json"}
            );
        }
    
     guardarFormatoCriterio(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/formatos/formato_criterio/guardar-formato_criterio', {...params},
                {responseType: "json"}
            );
        }
    anularFormatoCriterio(idFormatoCriterio: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/formatos/formato_criterio/anular-formato_criterio', {idFormatoCriterio, motivo},
            {responseType: "json"}
        );
    }

    activarFormatoCriterio(idFormatoCriterio: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/formatos/formato_criterio/activar-formato_criterio', {idFormatoCriterio},
            {responseType: "json"}
        );
    }
}
