import {Injectable} from '@angular/core';
import {HttpErrorResponse, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from "@angular/common/http";
import {catchError, map, Observable, retry, throwError} from "rxjs";
import {errorAlerta} from "@shared/utils";
import {keyframes} from "@angular/animations";

@Injectable({
    providedIn: 'root'
})
export class InterceptorService implements HttpInterceptor {

    intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        const token: string = localStorage.getItem('token') ?? '';
        if (token) {
            req = req.clone({
                setHeaders: {
                    authorization: 'Bearer ' + token
                }
            })
        }

        return next.handle(req).pipe(
            catchError((error: HttpErrorResponse) => {
                let mensaje = '';
                switch (error.status) {
                    case 401: {
                        mensaje = 'No tiene autorización';
                        break;
                    }
                    case 500: {
                        if (error.error.datos) {
                            mensaje = 'Error del servidor<br><p>Id. de correlación: ' + error.error.datos.identificador + '</p>';
                        }else{
                            mensaje = error.statusText;
                        }
                        break;
                    }
                    default: {
                        mensaje = error.statusText;
                    }
                }
                errorAlerta('Error ' + error.status, '', mensaje).then();
                return throwError(() => error.message);
            })
        );
    }
}
