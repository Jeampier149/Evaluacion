import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";



@Injectable({
    providedIn: 'root'
})
export class EmpleadoService {
    constructor(private http: HttpClient) {

    }

    listarEmpleado(params:any): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/datos-generales/empleado/lista-empleado', {
            params: {...params},
            responseType: "json"
        });
    }
    obtenerEmpleado(idEmpleado: string): Observable<HttpResponseApi> {
            return this.http.get<HttpResponseApi>('/api/datos-generales/empleado/obtener-empleado', {
                params: {idEmpleado},
                responseType: "json"
            });
        }

   listarEmpleados(): Observable<HttpResponseApi> {
        return this.http.get<HttpResponseApi>('/api/datos-generales/empleado/listar-empleados', {
            params: {},
            responseType: "json"
        });
    }

    
     editarEmpleado(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/datos-generales/empleado/editar-empleado', {...params},
                {responseType: "json"}
            );
        }
    
     guardarEmpleado(params: any): Observable<HttpResponseApi> {
            return this.http.post<HttpResponseApi>('/api/datos-generales/empleado/guardar-empleado', {...params},
                {responseType: "json"}
            );
        }

        
    anularEmpleado(idEmpleado: string, motivo: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/datos-generales/empleado/anular-empleado', {idEmpleado, motivo},
            {responseType: "json"}
        );
    }

    activarEmpleado(idEmpleado: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/datos-generales/empleado/activar-empleado', {idEmpleado},
            {responseType: "json"}
        );
    }
}
