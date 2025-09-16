import {Component, ElementRef, ViewChild} from '@angular/core';
import { ModalEmpleadoComponent } from '@modules/datos-generales/Components/modal-empleado/modal-empleado.component';
import { EmpleadoService } from '@services/datos-generales/empleado.service';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";

@Component({
  selector: 'app-empleado',
  templateUrl: './empleado.component.html',
  styleUrl: './empleado.component.scss'
})
export class EmpleadoComponent {
@ViewChild(ModalEmpleadoComponent) modalEmpleado!: ModalEmpleadoComponent
   @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Empleado'}];
    longitud: number = 15;
    pagina: number = 1;
    empleados: any = [];

    filtros = {
        nombre: '',
        tipo_doc: '',
        num_doc: '',
        unidad:'',
        servicio:'',
        condicion:'',
    };

   constructor(private EmpleadoService$: EmpleadoService) {}
 ngAfterViewInit() {
        this.listarEmpleado();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this. listarEmpleado();
    }

    filtrarEmpleado() {
        this.pagina = 1;
        this.listarEmpleado();
    }

    listarEmpleado() {
        let params: any= {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.loading = true;
        this.EmpleadoService$.listarEmpleado(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.empleados = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }
 limpiarEmpleado() {
        this.filtros = {
        nombre: '',
        tipo_doc: '',
        num_doc: '',
        unidad:'',
        servicio:'',
        condicion:'',
        };
        this.listarEmpleado();
    }

    async abrirNuevoEmpleado() {
        let respuesta = await this.modalEmpleado.openModal(1);
        if (respuesta) {
            this.listarEmpleado();
        }
    }

    async abrirEditarEmpleado(idMenu: string) {
        let respuesta = await this.modalEmpleado.openModal(2, idMenu);
        if (respuesta) {
            this.listarEmpleado();
        }
    }

     async abrirAnularEmpleado(idEmpleado: string) {
         const {isConfirmed, value} = await Swal.fire({
             title: 'Anular Empleado',
             text: '¿Esta seguro que desea anular el empleado seleccionada?',
             input: 'text',
             inputLabel: 'Motivo',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Anular'
        })

         if (isConfirmed) {
             this.EmpleadoService$.anularEmpleado(idEmpleado, value)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarEmpleado()
                         );
                    } else {
                         errorAlerta('Error', mensaje).then();
                    }
                });
         }
     }
     async abrirActivarEmpleado(idPerfil: string) {
         const {isConfirmed} = await Swal.fire({
             title: 'Activar Menú',
             text: '¿Esta seguro que desea activar la unidad seleccionado?',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Activar'
         })

         if (isConfirmed) {
             this.EmpleadoService$.activarEmpleado(idPerfil)
                 .subscribe(({mensaje, estado}) => {
                    if (estado) {
                         successAlerta('Éxito', mensaje).then(() =>
                             this.listarEmpleado()
                         );
                     } else {
                         errorAlerta('Error', mensaje).then();
                     }
                 });
         }
     }
}
