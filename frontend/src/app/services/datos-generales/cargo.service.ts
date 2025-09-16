import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class CargoService {
    constructor(private http: HttpClient) {

    }

    listarCargos(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/datos-generales/cargos/lista-cargo', {
            params: {...params},
            responseType: "json"
        });
    }
    obtenerCargo(idCargo: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/datos-generales/cargos/obtener-cargo', {
                params: {idCargo},
                responseType: "json"
            });
        }

    buscarCargos(codigo: string) {
        return this.http.get<HttpResponseApi>('/api/datos-generales/cargos/obtener-cargo', {
            params: {codigo},
            responseType: "json"
        })
    }
    
     editarCargo(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/datos-generales/cargos/editar-cargo', {...params},
                {responseType: "json"}
            );
        }
    
     guardarCargo(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/datos-generales/cargos/guardar-cargo', {...params},
                {responseType: "json"}
            );
        }
    anularCargo(idCargo: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/datos-generales/cargos/anular-cargo', {idCargo, motivo},
            {responseType: "json"}
        );
    }

    activarCargo(idCargo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/datos-generales/cargos/activar-cargo', {idCargo},
            {responseType: "json"}
        );
    }
}
