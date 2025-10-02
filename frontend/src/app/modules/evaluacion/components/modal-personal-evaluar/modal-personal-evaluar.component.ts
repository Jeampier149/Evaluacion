import { Component, ViewChild } from '@angular/core';
import { Modal } from 'bootstrap';
import { finalize } from 'rxjs';
import { errorAlerta, successAlerta } from '@shared/utils';
import { PeriodoService } from '@services/evaluacion/periodo.service';
import { AnimationStyleMetadata } from '@angular/animations';
import { PersonalEvaluarService } from '@services/evaluacion/personal-evaluar.service';
@Component({
    selector: 'app-modal-personal-evaluar',
    templateUrl: './modal-personal-evaluar.component.html',
    styleUrl: './modal-personal-evaluar.component.scss',
})
export class ModalPersonalEvaluarComponent {
    @ViewChild('modalEmpleadoEvaluarNuevo') modalEl!: any;
    modal: any;
    resolve: any;
    reject: any;
    idPeriodo: string = '';
    longitud: number = 15;
    pagina: number = 1;
    tipo: number = 1; //
    loading: boolean = false;
    filtros = {
        periodo: '',
        apellidos: '',
        nombre: '',
        categoria: '',
        servicio: '',
        cargo: '',
    };
    data: any = [];
    selectedIds: number[] = [];
    allSelected: boolean = false;
    constructor(private EmpleadoEvaluar$: PersonalEvaluarService) {}

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false,
        });
    }
    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarEmpleadosNuevos();
    }

    openModal(tipo: number, periodo: any): Promise<boolean> {
        this.modal.show();
        this.tipo = tipo;
        this.filtros.periodo = periodo;
        this.listarEmpleadosNuevos();
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

    listarEmpleadosNuevos() {
        let params: any = {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina,
        };
        this.loading = true;
        this.EmpleadoEvaluar$.listarEmpleadosNuevos(params)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.data = datos;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }

    resetModal() {
        this.tipo = 1;
        this.filtros = {
            periodo: '',
            apellidos: '',
            nombre: '',
            categoria: '',
            servicio: '',
            cargo: '',
        };
    }
    filtrarEmpleado() {
        this.pagina = 1;
        this.listarEmpleadosNuevos();
    }
    limpiarCampos() {
        this.filtros = {
            periodo: '',
            apellidos: '',
            nombre: '',
            categoria: '',
            servicio: '',
            cargo: '',
        };
        this.listarEmpleadosNuevos();
    }

    ngOnDestroy() {
        this.modal.dispose();
    }

    // Toggle para seleccionar/deseleccionar un item individual
    toggleSelection(id: number): void {
        const index = this.selectedIds.indexOf(id);

        if (index === -1) {
            // Agregar al array si no existe
            this.selectedIds.push(id);
        } else {
            // Remover del array si existe
            this.selectedIds.splice(index, 1);
        }

        // Actualizar estado del checkbox general
        this.updateAllSelectedState();
    }

    // Verificar si un ID está seleccionado
    isSelected(id: number): boolean {
        return this.selectedIds.includes(id);
    }

    // Actualizar estado del checkbox general
    private updateAllSelectedState(): void {
        this.allSelected =
            this.selectedIds.length === this.data.length &&
            this.data.length > 0;
    }

    agregarEmpleadoEval(tipo: any) {
        this.loading = true;
        this.EmpleadoEvaluar$.agregarEmpleadoEval(
            tipo,
            this.filtros.periodo,
            this.selectedIds
        )
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
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
}
