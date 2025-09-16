import { Component, ViewChild } from '@angular/core';
import { Modal } from 'bootstrap';
import { finalize } from 'rxjs';
import { errorAlerta, successAlerta } from '@shared/utils';
import { PeriodoService } from '@services/evaluacion/periodo.service';
import { AnimationStyleMetadata } from '@angular/animations';

@Component({
    selector: 'app-modal-periodo',
    templateUrl: './modal-periodo.component.html',
    styleUrl: './modal-periodo.component.scss',
})
export class ModalPeriodoComponent {
    @ViewChild('modalPeriodo') modalEl!: any;
    modal: any;
    resolve: any;
    reject: any;
    idPeriodo: string = '';
    tipo: number = 1; // 1 Nuevo, 2 Editar
    loading: boolean = false;
    formulario = {
        id: '',
        ano: '',
        semestre: '',
        nombre: '',
        desde: '',
        hasta: '',
        multiplicar: '',
        dividir: '',
        factor_asistencia: '',
        estado: '',
    };
    nombre = '';
    constructor(private PeriodoService$: PeriodoService) {}

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false,
        });
    }
    generarNombre() {
        this.formulario.nombre = `${this.formulario.semestre} Periodo ${this.formulario.ano}`;
    }

    openModal(tipo: number, idPerfil?: string): Promise<boolean> {
        this.modal.show();
        this.tipo = tipo;
        if (tipo === 2) {
            this.idPeriodo = idPerfil!;
            this.obtenerPeriodo();
        }
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
        });
    }

    closeModal() {
        this.modal.hide();
        this.resolve(false);
        this.resetModal();
    }

    obtenerPeriodo() {
        this.loading = true;
        this.PeriodoService$.obtenerPeriodo(this.idPeriodo)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.formulario.id = datos!.codigo;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }

    guardarPeriodo() {
        this.loading = true;
        if (this.tipo === 1){
            this.generatePeriodId()
        }

        let params: any = {
            ...this.formulario,
        };

        if (this.tipo === 1) {
            this.PeriodoService$.guardarPeriodo(params)
                .pipe(finalize(() => (this.loading = false)))
                .subscribe(({ estado, mensaje }) => {
                    if (estado) {
                        successAlerta('Éxito!', mensaje).then(() => {
                            this.modal.hide();
                            this.resolve(true);
                            this.resetModal();
                        });
                    } else {
                        errorAlerta('Error!', mensaje).then();
                    }
                });
        }

        if (this.tipo === 2) {
            this.PeriodoService$.editarPeriodo(params)
                .pipe(finalize(() => (this.loading = false)))
                .subscribe(({ estado, mensaje }) => {
                    if (estado) {
                        successAlerta('Éxito!', mensaje).then(() => {
                            this.modal.hide();
                            this.resolve(true);
                            this.resetModal();
                        });
                    } else {
                        errorAlerta('Error!', mensaje).then();
                    }
                });
        }
        // this.obtenerPerfil();
    }
    generatePeriodId(): string {
        const year = this.formulario.ano;
        if (!year) return '';

        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(1, '0');
 
        return  this.formulario.id=(`${year}-${hours}${minutes}${seconds}`).substring(0, 10);
    }

    resetModal() {
        this.tipo = 1;
        this.idPeriodo = '';
        this.formulario = {
            id: '',
            ano: '',
            semestre: '',
            nombre: '',
            desde: '',
            hasta: '',
            multiplicar: '',
            dividir:'',
            factor_asistencia: '',
            estado: '',
        };
    }

    limpiarCampos() {}

    ngOnDestroy() {
        this.modal.dispose();
    }
}
