import {Component, ViewChild} from '@angular/core';
import {LoginService} from "@services/login.service";
import {Modal} from 'bootstrap';
import {errorAlerta, successAlerta} from "@shared/utils";

@Component({
    selector: 'app-modal-pass',
    templateUrl: './modal-pass.component.html'
})
export class ModalPassComponent {
    @ViewChild('modalPass') modalEl?: any;
    modal: any;
    usuario: string = '';
    antPass: string = '';
    newPass: string = '';
    confPass: string = '';

    resolve: any;
    reject: any;

    constructor(private LoginService: LoginService) {
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        })
    }

    openModal(usuario: string, antPass: string) {
        this.usuario = usuario;
        this.antPass = antPass;
        this.modal.show();
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
        })
    }

    closeModal() {
        this.modal.hide();
        this.resetModal();
    }

    private resetModal() {
        this.newPass = '';
        this.antPass = '';
        this.confPass = '';
        this.usuario = '';
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

        const $loginService = this.LoginService.cambiarPassword(this.usuario, this.antPass, this.newPass);
        $loginService.subscribe(({datos, mensaje, estado}) => {
            if (estado === 1) {
                successAlerta('Éxito', mensaje).then(()=>{
                    this.resolve(true);
                });
                this.closeModal();
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

    ngOnDestroy() {
        this.closeModal();
    }

}
