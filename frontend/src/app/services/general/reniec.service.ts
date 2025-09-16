import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {HttpResponseApi} from "@interfaces/http.interface";
import {reniecClass} from "@classes/servicios/reniec.class";
import {map, Observable} from "rxjs";
@Injectable({
  providedIn: 'root'
})
export class ReniecService {

  constructor(private http: HttpClient) {

  }

  buscarDni(dni: string): Observable<HttpResponseApi<reniecClass>> {
      return this.http.get<HttpResponseApi>('/api/reniec/buscarReniec', {
          params: {dni},
          responseType: "json",
      }).pipe(
          map(response => {
                  return {...response, datos: new reniecClass(response.datos)}
              }
          )
      )
  }
}
