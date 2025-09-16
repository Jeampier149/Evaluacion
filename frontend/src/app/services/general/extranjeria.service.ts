import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {HttpResponseApi} from "@interfaces/http.interface";
import {map, Observable} from "rxjs";
import {MigracionesClass} from "@classes/servicios/migraciones.class";
@Injectable({
  providedIn: 'root'
})
export class ExtranjeriaService {

  constructor(private http: HttpClient) { }

  buscarMigraciones(documento: string): Observable<HttpResponseApi<MigracionesClass>> {
    return this.http.get<HttpResponseApi>('/api/extranjeria/buscarExtranjeria', {
        params: {documento: documento},
        responseType: "json",
    }).pipe(
        map(response => {
                return {...response, datos: new MigracionesClass(response.datos)}
            }
        )
    )
}
}
