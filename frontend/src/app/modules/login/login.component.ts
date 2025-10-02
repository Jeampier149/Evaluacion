import {Component, ViewChild} from '@angular/core';
import {LoginService} from "@services/login.service";
import {errorAlerta, successAlerta, warningAlerta} from "@shared/utils";
import {ModalPassComponent} from "@modules/login/components/modal-pass/modal-pass.component";
import {Router} from "@angular/router";
import {guardarSession, Session} from "@store/session.actions";
import {Store} from "@ngrx/store";
import {finalize} from "rxjs";

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.css'],
})
export class LoginComponent {
    @ViewChild(ModalPassComponent) modalPass?: ModalPassComponent
    usuario: string = '';
    password: string = '';
    tipoInputPassword: string = 'password';
    loading: boolean = false;
    anio: string = new Date().getFullYear().toString();

    constructor(private LoginService: LoginService, public router: Router, private store: Store<Session>) {
        this.obtenerCSRF();
    }

    ngOnInit() {
        document.getElementsByName('username')[0].focus();
    }

    obtenerCSRF() {
        this.LoginService.obtenerCsrf().subscribe();
    }

    submitForm() {
        if (this.password.length < 3) {
            errorAlerta('Error', 'La contraseña debe mayor a 3 caracteres.').then();
            return;
        }

        if (this.usuario.length <= 0) {
            errorAlerta('Error', 'Ingrese un usuario valido.').then();
            return;
        }

        this.loading = true;
        const $loginService = this.LoginService.login(this.usuario, this.password);
        $loginService.pipe(
            finalize(() => {
                this.loading = false;
            })
        ).subscribe(({datos, mensaje, estado}) => {
            if (estado === 1) {
                let mensaje: string = 'Bienvenido al Sistema de Evaluacion de Personal (SIEVAL)' ;
                successAlerta('Éxito!', mensaje).then(() => {
                    let session: Session = {
                        logIn: '1',
                        usuario: datos.username,
                      //accesos: datos.accesos,
                        menu: datos.menu,
                        informacion:datos.informacion,
                        categoria:datos.categoria,
                        cc_empleado:datos.cc_empleado,
                        token: datos.token,                    
                        nombres: datos.nombre,
                        perfil: datos.perfil,
                        servicio:datos.servicio,
                        departamento:datos.departamento,
                        fechaExpiracion: datos.fecha_expiracion
                    }
                    this.store.dispatch(guardarSession({session}));
                    this.router.navigate(['inicio']).then();
                });
            } else if (estado === 2) {
                warningAlerta('Atención!', datos).then(() => {
                    this.modalPass?.openModal(this.usuario, this.password).then((e) => {
                        if (e) {
                            this.password = '';
                        }
                    });
                });
            } else if (estado === 0) {
                errorAlerta('Error', mensaje).then(() => {
                });
            } else {
                let errores = JSON.parse(datos);
                let mensaje = '';
                Object.entries(errores).forEach(([, value]: any) => {
                    mensaje += value.map((error: any) => {
                        return error + '<br>';
                    })
                })
                errorAlerta('Error', '', mensaje).then();
            }
        })
    }

    onSubmitForm(e: SubmitEvent): void {
        e.preventDefault();
    }

    saltoPassword(e: Event): void {
        document.getElementsByName('password')[0].focus();
        e.preventDefault();
    }

    validarUsuario(e: Event): void {
        e.preventDefault();
        this.loading = true;
        const loginService$ = this.LoginService.validarUsuario(this.usuario, this.password);
        loginService$.pipe(
            finalize(() => {
                this.loading = false;
            })
        ).subscribe(({datos, estado, mensaje}) => {
            if (estado === 1) {
                this.modalPass?.openModal(this.usuario, this.password).then((e)=>{
                    if (e){
                        this.password = '';
                    }
                });
            } else if (estado === 0) {
                errorAlerta('Error', mensaje).then();
            } else {
                let errores = JSON.parse(datos);
                let mensaje = '';
                Object.entries(errores).forEach(([, value]: any) => {
                    mensaje += value.map((error: any) => {
                        return error + '<br>';
                    })
                })
                errorAlerta('Error', '', mensaje).then();
            }
        })
    }

}
