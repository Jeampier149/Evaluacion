import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class FormatoPService {
    constructor(private http: HttpClient) {

    }

    listarFormatoP(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/formato/formato_p/lista-formato_p', {
            params: {...params},
            responseType: "json"
        });
    }
    obtenerFormatoP(idFormatoP: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/formato/formato_p/obtener-formato_p', {
                params: {idFormatoP},
                responseType: "json"
            });
        }

    buscarFormatoPs(codigo: string) {
        return this.http.get<HttpResponseApi>('/api/formato/formato_p/obtener-formato_p', {
            params: {codigo},
            responseType: "json"
        })
    }
    
     editarFormatoP(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/formato/formato_p/editar-formato_p', {...params},
                {responseType: "json"}
            );
        }
    
     guardarFormatoP(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/formato/formato_p/guardar-formato_p', {...params},
                {responseType: "json"}
            );
        }
    anularFormatoP(idFormatoP: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/formato/formato_p/anular-formato_p', {idFormatoP, motivo},
            {responseType: "json"}
        );
    }

    activarFormatoP(idFormatoP: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/formato/formato_p/activar-formato_p', {idFormatoP},
            {responseType: "json"}
        );
    }
}
