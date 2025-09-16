import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";
import {IListaMenu, IListaMenuCombo, IListaMenuParams, IMenu} from "@interfaces/configuracion/menu.interface";

@Injectable({
    providedIn: 'root'
})
export class MenuService {
    constructor(private http: HttpClient) {

    }

    listaMenuCombo(): Observable<HttpResponseApi<IListaMenuCombo[]>> {
        return this.http.get<HttpResponseApi<IListaMenuCombo[]>>('/api/configuracion/combo-menu', {
            responseType: "json"
        });
    }

    listarMenus(params: IListaMenuParams): Observable<HttpResponseApi<IListaMenu[]>> {
        return this.http.get<HttpResponseApi<IListaMenu[]>>('/api/configuracion/lista-menu', {
            params: {...params},
            responseType: "json"
        });
    }

    obtenerMenu(idMenu: string): Observable<HttpResponseApi<IMenu>> {
        return this.http.post<HttpResponseApi<IMenu>>('/api/configuracion/obtener-menu', {idMenu},
            {responseType: "json"});
    }

    editarMenu(params: IMenu): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/editar-menu', {...params},
            {responseType: "json"}
        );
    }

    guardarMenu(params: IMenu): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/guardar-menu', {...params},
            {responseType: "json"}
        );
    }

    anularMenu(idMenu: string, descripcion: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/anular-menu', {id: idMenu, descripcion},
            {responseType: "json"}
        );
    }

    activarMenu(idMenu: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/configuracion/activar-menu', {id: idMenu},
            {responseType: "json"}
        );
    }

}
