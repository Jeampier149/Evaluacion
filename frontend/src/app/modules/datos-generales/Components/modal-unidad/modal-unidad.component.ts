import {Component, ViewChild} from '@angular/core';
import {Modal} from 'bootstrap';
import {finalize} from "rxjs";
import {errorAlerta, successAlerta} from "@shared/utils";
import { UnidadService } from '@services/datos-generales/unidad.service';


@Component({
  selector: 'app-modal-unidad',
  templateUrl: './modal-unidad.component.html',
  styleUrl: './modal-unidad.component.scss'
})
export class ModalUnidadComponent {
@ViewChild('modalUnidad') modalEl!: any;
    modal: any;
    resolve: any;
    reject: any;
    empleados:any=[]
    empleado:any
    idUnidad: string = '';
    tipo: number = 1; // 1 Nuevo, 2 Editar
    loading: boolean = false;
    formulario = {
        unidad: '',
        empleado: '',
        codigo:''

    }

    constructor(private UnidadService$: UnidadService) {
        this.listarEmpleado()
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
            this.idUnidad = idPerfil!;
            this.obtenerUnidad();
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

    obtenerUnidad() {
        this.loading = true;
        this.UnidadService$.obtenerUnidad(this.idUnidad)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.formulario.unidad = datos!.unidad;
                    this.formulario.empleado = datos!.empleado;
                    this.formulario.codigo= datos!.codigo;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }
listarEmpleado() {
      
        this.loading = true;
        this.UnidadService$.listarEmpleados()
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.empleados = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }
    guardarUnidad() {
        this.loading = true;
        let params: any = {
            unidad: this.formulario.unidad,
            empleado: this.formulario.empleado,
        }

        if (this.tipo === 1) {
            this.UnidadService$.guardarUnidad(params)
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
            this.UnidadService$.editarUnidad(params)
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
        this.idUnidad = '';
        this.formulario = {
            unidad: '',
            empleado: '',
            codigo:''

        }
    }

    limpiarCampos() {
    }

    ngOnDestroy() {
        this.modal.dispose();
    }
}
