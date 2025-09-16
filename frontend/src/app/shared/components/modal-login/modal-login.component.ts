import {Component, ElementRef, ViewChild} from '@angular/core';
import {Carousel, Modal} from "bootstrap";
import {finalize} from "rxjs";
import {SharedModule} from "@shared/shared.module";
import {FormControl, FormGroup, ReactiveFormsModule, Validators} from "@angular/forms";
import {errorAlerta, successAlerta, warningAlerta} from "@shared/utils";
import {eliminarSession, guardarSession, Session} from "@store/session.actions";
import {LoginService} from "@services/login.service";
import {Router} from "@angular/router";
import {Store} from "@ngrx/store";
import {LoadingComponent} from "@shared/components/loading/loading.component";

@Component({
    standalone: true,
    selector: 'app-modal-login',
    imports: [
        SharedModule,
        ReactiveFormsModule,
        LoadingComponent
    ],
    templateUrl: './modal-login.component.html'
})
export class ModalLoginComponent {
    @ViewChild('modalLogin') modalEl?: any;
    @ViewChild('carouselLogin') carousel?: any;
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    modal: undefined | Modal;
    show: boolean = false;
    modales: any[] = [];
    accion: number = 1; // 1: Iniciar Sesión // 2: Actualizar contraseña

    loading: boolean = false;
    loginForm: FormGroup | undefined;

    resolve: any;
    reject: any;

    newPass: string = '';
    confPass: string = '';

    constructor(private LoginService: LoginService, public router: Router, private store: Store<Session>) {
    }

    ngOnInit() {
        this.loginForm = new FormGroup({
            usuario: new FormControl('', {
                nonNullable: true,
                validators: [Validators.required, Validators.minLength(3)]
            }),
            password: new FormControl('', {
                nonNullable: true,
                validators: [Validators.required, Validators.minLength(2)]
            }),
        })
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        });

        this.modalEl.nativeElement.addEventListener('shown.bs.modal', () => {
            this.inpFocus.nativeElement.focus();
        })

        this.carousel = new Carousel(this.carousel.nativeElement, {
            touch: false,
            keyboard: false,
            wrap: false
        })
    }

    openModal() {
        if (!this.show) {
            // Ocultar todos lo modales
            let modales = document.getElementsByClassName('modal fade show');
            for (let modal of Array.from(modales)) {
                let modalInstance = Modal.getInstance(modal);
                this.modales.push(modalInstance);
                modalInstance?.hide();
            }
            this.modal?.show();
            this.show = true;
        }
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
        })
    }

    closeModal() {
        this.show = false;
        this.resetModal();
        this.store.dispatch(eliminarSession());
        this.resolve(false);
        this.router.navigate(['login']).then();
    }

    iniciarSesion() {
        if (this.accion === 1) {
            this.loading = true;
            this.LoginService.login(this.loginForm?.controls['usuario'].value, this.loginForm?.controls['password'].value).pipe(
                finalize(() => {
                    this.loading = false;
                })
            ).subscribe(({datos, mensaje, estado}) => {
                if (estado === 1) {
                    let mensaje: string = 'Inicio de sesión correcto';
                    successAlerta('Éxito!', mensaje).then(() => {
                        let session: Session = {
                            logIn: '1',
                            usuario: datos.usuario,
                            accesos: datos.accesos,
                            menu: datos.menu,
                            token: datos.token,
                            nombres: datos.nombres,
                            perfil: datos.perfil,
                            fechaExpiracion: datos.fecha_expiracion
                        }
                        this.store.dispatch(guardarSession({session}));
                        this.modal?.hide();
                        this.show = false;
                        this.resetModal();

                        // Mostar modales ocultos y limpiar lista
                        for (let modal of this.modales) {
                            modal.show();
                        }
                        this.modales = [];

                        this.resolve(true);
                    });
                } else if (estado === 2) {
                    warningAlerta('Atención!', datos).then(() => {
                        this.accion = 2;
                        this.carousel.to(1);
                        // this.openModalPass();
                    });
                } else if (estado === 0) {
                    errorAlerta('Error', datos, '', {
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => {
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
        } else {
            this.actualizarPass();
        }
    }

    actualizarPass() {
        if (this.newPass !== this.confPass) {
            errorAlerta('Error', 'Las contraseñas ingresadas no son iguales').then();
            return
        }
        if (this.newPass === '' || this.newPass.length < 3) {
            errorAlerta('Error', 'La contraseña debe ser de 3 caracteres a más').then();
            return
        }

        this.loading = true;

        let usuario = this.loginForm?.controls['usuario'].value;
        let antPass = this.loginForm?.controls['password'].value;
        const $loginService = this.LoginService.cambiarPassword(usuario, antPass, this.newPass);
        $loginService.pipe(finalize(() => this.loading = false))
            .subscribe(({datos, mensaje, estado}) => {
                if (estado === 1) {
                    successAlerta('Éxito', mensaje).then(() => {
                        this.carousel.to(0);
                        this.accion = 1;
                        this.loginForm?.controls['password'].setValue('');
                    });
                } else if (estado === 2) {
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

    private resetModal() {
        this.loginForm?.reset();
    }

    ngOnDestroy() {
        this.modal?.dispose();
    }

}
