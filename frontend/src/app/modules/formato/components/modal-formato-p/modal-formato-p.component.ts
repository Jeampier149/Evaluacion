import {Component, ViewChild} from '@angular/core';
import {Modal} from 'bootstrap';
import {finalize} from "rxjs";
import {errorAlerta, successAlerta} from "@shared/utils";
import { FormatoPService } from '@services/formato/formato_p.service';

@Component({
  selector: 'app-modal-formato-p',
  templateUrl: './modal-formato-p.component.html',
  styleUrl: './modal-formato-p.component.scss'
})
export class ModalFormatoPComponent {
@ViewChild('modalFormatoP') modalEl!: any;
    modal: any;
    resolve: any;
    reject: any;
    idFormatoP: string = '';
    tipo: number = 1; // 1 Nuevo, 2 Editar
    loading: boolean = false;
    formulario = {
        id: '',
        descripcion: ''

    }

    constructor(private FormatoPService$: FormatoPService) {
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        });
    }


    openModal(tipo: number, idFormato?: string): Promise<boolean> {
        this.modal.show();
        this.tipo = tipo;
        if (tipo === 2) {
            this.idFormatoP = idFormato!;
            this.obtenerFormatoP();
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

    obtenerFormatoP() {
        this.loading = true;
        this.FormatoPService$.obtenerFormatoP(this.idFormatoP)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.formulario.id = datos!.codigo;
                    this.formulario.descripcion = datos!.descripcion;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    guardarFormatoP() {
        this.loading = true;
        let params: any = {
            id: this.idFormatoP,
            descripcion: this.formulario.descripcion,
        }

        if (this.tipo === 1) {
            params.id = this.formulario.id;
            this.FormatoPService$.guardarFormatoP(params)
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
            this.FormatoPService$.editarFormatoP(params)
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
        // this.obtenerFormato();
    }

    resetModal() {
        this.tipo = 1;
        this.idFormatoP = '';
        this.formulario = {
            id: '',
            descripcion: '',

        }
    }

    limpiarCampos() {
    }

    ngOnDestroy() {
        this.modal.dispose();
    }
}
