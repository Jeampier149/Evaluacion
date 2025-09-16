import {Component, ElementRef, ViewChild} from '@angular/core';
import {Modal} from 'bootstrap';
import {finalize} from "rxjs";
import {errorAlerta, successAlerta} from "@shared/utils";
import {AccionService} from "@services/configuracion/accion.service";
import {IListaAccion} from "@interfaces/configuracion/accion.interface";
import Swal from "sweetalert2";

@Component({
    selector: 'app-modal-accion',
    templateUrl: './modal-accion.component.html'
})
export class ModalAccionComponent {
    @ViewChild('modalAccion') modalEl!: any;
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    modal: any;
    resolve: any;
    reject: any;
    idMenu: string = '';
    nombreMenu: string = '';
    loading: boolean = false;
    acciones: IListaAccion[] = [];
    formulario = {
        descripcion: '',
    }

    constructor(private AccionService$: AccionService) {
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        });
        this.modalEl.nativeElement.addEventListener('shown.bs.modal', () => {
            this.inpFocus.nativeElement.focus();
        })
    }

    openModal(idMenu: string, nombreMenu: string): Promise<boolean> {
        this.modal.show();
        this.idMenu = idMenu!;
        this.nombreMenu = nombreMenu;
        this.listaAccion();
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

    listaAccion() {
        this.loading = true;
        this.AccionService$.listarAcciones(this.idMenu)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.acciones = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    guardarAccion() {
        this.loading = true;
        this.AccionService$.guardarAccion(this.idMenu, this.formulario.descripcion)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    successAlerta('Éxito', mensaje).then(() => {
                        this.formulario.descripcion = '';
                        this.listaAccion()
                    });
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    async abrirAnularAccion(idAccion: string) {
        this.modal.hide();
        const {isConfirmed, value} = await Swal.fire({
            title: 'Anular Acción',
            text: '¿Esta seguro que desea anular la acción seleccionado?',
            input: 'text',
            inputLabel: 'Motivo',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Anular'
        })

        if (isConfirmed) {
            this.AccionService$.anularAccion(idAccion, value)
                .subscribe(({datos, mensaje, estado}) => {
                    if (estado || !datos) {
                        successAlerta('Éxito', mensaje).then(() =>
                            this.listaAccion()
                        );
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
        this.modal.show();

    }

    resetModal() {
        this.idMenu = '';
        this.nombreMenu = '';
        this.acciones = [];
        this.formulario = {
            descripcion: ''
        }
    }

    limpiarCampos() {
    }

    ngOnDestroy() {
        this.modal.dispose();
    }
}
