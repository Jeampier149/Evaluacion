import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class ServicioService {
    constructor(private http: HttpClient) {

    }

    listarServicio(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/datos-generales/servicio/lista-servicio', {
            params: {...params},
            responseType: "json"
        });
    }
    obtenerServicio(idServicio: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/datos-generales/servicio/obtener-servicio', {
                params: {idServicio},
                responseType: "json"
            });
        }

   listarEmpleados(): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/datos-generales/servicio/listar-empleados', {
            params: {},
            responseType: "json"
        });
    }

    
     editarServicio(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/datos-generales/servicio/editar-servicio', {...params},
                {responseType: "json"}
            );
        }
    
     guardarServicio(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/datos-generales/servicio/guardar-servicio', {...params},
                {responseType: "json"}
            );
        }

        
    anularServicio(idServicio: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/datos-generales/servicio/anular-servicio', {idServicio, motivo},
            {responseType: "json"}
        );
    }

    activarServicio(idServicio: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/datos-generales/servicio/activar-servicio', {idServicio},
            {responseType: "json"}
        );
    }
}
