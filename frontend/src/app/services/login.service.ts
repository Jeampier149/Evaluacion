import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {HttpResponseApi} from "../interfaces/http.interface";

@Injectable({
    providedIn: 'root'
})
export class LoginService {

    loginRequired: any;

    constructor(private http: HttpClient) {

    }

    obtenerCsrf() {
        return this.http.get('/sanctum/csrf-cookie', {responseType: "json"});
    }

    login(user: string, password: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/login', {
            username: user,
            password: password
        }, {
            responseType: "json"
        });
    }

    validarUsuario(user: string, password: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/validar-usuario', {
            nombreUsuario: user,
            contrasena: password
        }, {
            responseType: "json"
        });
    }


    cambiarPassword(user: string, password: string, newPassword: string): Observable<HttpResponseApi> {
        return this.http.post<HttpResponseApi>('/api/cambiar-contrasena', {
            nombreUsuario: user,
            contrasena: password,
            nuevaContrasena: newPassword
        }, {
            responseType: "json"
        });
    }


    logOut(): Observable<any> {
        return this.http.post('/api/logout', {}, {
            responseType: "json"
        });
    }

    validarSesion(): Observable<any> {
        return new Observable((subscriber) => {
            this.loginRequired = subscriber;
            return subscriber.next(0);
        });
    }

    requireLogin() {
        // Validación de sesión
        let fechaExp: any = localStorage.getItem('fechaExpiracion');
        if (fechaExp !== null) {
            fechaExp = new Date(fechaExp);
            let fechaComparativa = new Date(fechaExp.getTime() - (15 * 60000)); // Tiempo limite  - 15 min
            if (new Date() > fechaComparativa) { // Lanza modal login si esta dentro de los 15 min
                this.loginRequired.next(1);
            } else {
                this.loginRequired.next(0);
            }
        } else {
            this.loginRequired.next(1);
        }
    }


}
