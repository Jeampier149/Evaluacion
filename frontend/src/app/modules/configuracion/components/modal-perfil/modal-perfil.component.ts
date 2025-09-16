import {Component, ViewChild} from '@angular/core';
import {Modal} from 'bootstrap';
import {finalize} from "rxjs";
import {errorAlerta, successAlerta} from "@shared/utils";
import {PerfilService} from "@services/configuracion/perfil.service";
import {IPerfil} from "@interfaces/configuracion/perfil.interface";

@Component({
    selector: 'app-modal-perfil',
    templateUrl: './modal-perfil.component.html'
})
export class ModalPerfilComponent {
    @ViewChild('modalPerfil') modalEl!: any;
    modal: any;
    resolve: any;
    reject: any;
    idPerfil: string = '';
    tipo: number = 1; // 1 Nuevo, 2 Editar
    loading: boolean = false;
    formulario = {
        id: '',
        descripcion: '',
        observacion: '',
    }

    constructor(private PerfilService$: PerfilService) {
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        });
    }


    openModal(tipo: number, idPerfil?: string): Promise<boolean> {
        this.modal.show();
        this.tipo = tipo;
        if (tipo === 2) {
            this.idPerfil = idPerfil!;
            this.obtenerPerfil();
        }
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
        })
    }

    closeModal() {
        this.modal.hide();
        this.resolve(false);
        this.resetModal();
    }

    obtenerPerfil() {
        this.loading = true;
        this.PerfilService$.obtenerPerfil(this.idPerfil)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.formulario.id = datos!.id;
                    this.formulario.descripcion = datos!.descripcion;
                    this.formulario.observacion = datos!.observacion;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    guardarPerfil() {
        this.loading = true;
        let params: IPerfil = {
            id: this.idPerfil,
            descripcion: this.formulario.descripcion,
            observacion: this.formulario.observacion
        }

        if (this.tipo === 1) {
            params.id = this.formulario.id;
            this.PerfilService$.guardarPerfil(params)
                .pipe(finalize(() => this.loading = false))
                .subscribe(({estado, mensaje}) => {
                    if (estado) {
                        successAlerta('Éxito!', mensaje).then(() => {
                            this.modal.hide();
                            this.resolve(true);
                            this.resetModal();
                        });
                    } else {
                        errorAlerta('Error!', mensaje).then();
                    }
                })
        }

        if (this.tipo === 2) {
            this.PerfilService$.editarPerfil(params)
                .pipe(finalize(() => this.loading = false))
                .subscribe(({estado, mensaje}) => {
                    if (estado) {
                        successAlerta('Éxito!', mensaje).then(() => {
                            this.modal.hide();
                            this.resolve(true);
                            this.resetModal();
                        });
                    } else {
                        errorAlerta('Error!', mensaje).then();
                    }
                })
        }
        // this.obtenerPerfil();
    }

    resetModal() {
        this.tipo = 1;
        this.idPerfil = '';
        this.formulario = {
            id: '',
            descripcion: '',
            observacion: ''
        }
    }

    limpiarCampos() {
    }

    ngOnDestroy() {
        this.modal.dispose();
    }
}
