import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class FormatosCabService {
    constructor(private http: HttpClient) {

    }

    listarFormatosCabs(categoria:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/formato/formato_cab/lista-formato_cab', {
            params: {categoria},
            responseType: "json"
        });
    }
    obtenerFormatosCab(idFormatosCab: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/formatos/formato_cab/obtener-formato_cab', {
                params: {idFormatosCab},
                responseType: "json"
            });
        }

    buscarFormatosCabs(codigo: string) {
        return this.http.get<HttpResponseApi>('/api/formatos/formato_cab/obtener-formato_cab', {
            params: {codigo},
            responseType: "json"
        })
    }
    
     editarFormatosCab(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/formatos/formato_cab/editar-formato_cab', {...params},
                {responseType: "json"}
            );
        }
    
     guardarFormatosCab(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/formatos/formato_cab/guardar-formato_cab', {...params},
                {responseType: "json"}
            );
        }
    anularFormatosCab(idFormatosCab: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/formatos/formato_cab/anular-formato_cab', {idFormatosCab, motivo},
            {responseType: "json"}
        );
    }

    activarFormatosCab(idFormatosCab: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/formatos/formato_cab/activar-formato_cab', {idFormatosCab},
            {responseType: "json"}
        );
    }
}
