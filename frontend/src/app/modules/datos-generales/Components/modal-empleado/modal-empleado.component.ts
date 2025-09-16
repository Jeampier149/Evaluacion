import {Component, ViewChild} from '@angular/core';
import {Modal} from 'bootstrap';
import {finalize} from "rxjs";
import {errorAlerta, successAlerta} from "@shared/utils";
import { CargoService } from '@services/datos-generales/cargo.service';


@Component({
  selector: 'app-modal-empleado',
  templateUrl: './modal-empleado.component.html',
  styleUrl: './modal-empleado.component.scss'
})
export class ModalEmpleadoComponent {
@ViewChild('modalCargo') modalEl!: any;
    modal: any;
    resolve: any;
    reject: any;
    idCargo: string = '';
    tipo: number = 1; // 1 Nuevo, 2 Editar
    loading: boolean = false;
    formatos :any=[]
    unidades :any=[]
    servicios:any=[]
    condiciones:any=[]
    cargos :any=[]
    niveles :any=[]
    formulario = {
        id: '',
        formato: '',
        unidad: '',
        servicio: '',
        cargo: '',
        condicion: '',
        nivel: '',
        tipo_doc: '',

    }

    constructor(private CargoService$: CargoService) {
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
            this.idCargo = idPerfil!;
            this.obtenerCargo();
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

    obtenerCargo() {
        this.loading = true;
        this.CargoService$.obtenerCargo(this.idCargo)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.formulario.id = datos!.codigo;
                //    this.formulario.descripcion = datos!.descripcion;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    guardarCargo() {
        this.loading = true;
        let params: any = {
            id: this.idCargo,
          //  descripcion: this.formulario.descripcion,
        }

        if (this.tipo === 1) {
            params.id = this.formulario.id;
            this.CargoService$.guardarCargo(params)
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
            this.CargoService$.editarCargo(params)
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
        this.idCargo = '';
        // this.formulario = {
        //     id: '',
        //    // descripcion: '',

        // }
    }

    limpiarCampos() {
    }

    ngOnDestroy() {
        this.modal.dispose();
    }
}
