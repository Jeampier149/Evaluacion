import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import { ServicioService } from '@services/datos-generales/servicio.service';
import { ModalServicioComponent } from '@modules/datos-generales/Components/modal-servicio/modal-servicio.component';


@Component({
  selector: 'app-servicios-organica',
  templateUrl: './servicios.component.html',

})
export class ServiciosComponent {
 @ViewChild(ModalServicioComponent) modalServicio!: ModalServicioComponent
   
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'servicio'}];
    longitud: number = 15;
    pagina: number = 1;
    servicios: any = [];

    filtros = {
        servicio:'',
        unidad: '',
        coordinador: ''
    };

    constructor(private ServicioService$: ServicioService) {
    }

    ngAfterViewInit() {
        this.listarServicio();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this. listarServicio();
    }

    filtrarServicio() {
        this.pagina = 1;
        this.listarServicio();
    }

    listarServicio() {
        let params: any= {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.loading = true;
        this.ServicioService$.listarServicio(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.servicios = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }

    limpiarServicio() {
        this.filtros = {
            servicio: '',
            unidad:'',
            coordinador: '',
        };
        this.listarServicio();
    }

    async abrirNuevoServicio() {
        let respuesta = await this.modalServicio.openModal(1);
        if (respuesta) {
            this.listarServicio();
        }
    }

    async abrirEditarServicio(idMenu: string) {
        let respuesta = await this.modalServicio.openModal(2, idMenu);
        if (respuesta) {
            this.listarServicio();
        }
    }

     async abrirAnularServicio(idServicio: string) {
         const {isConfirmed, value} = await Swal.fire({
             title: 'Anular Servicio',
             text: '¿Esta seguro que desea anular la servicio seleccionada?',
             input: 'text',
             inputLabel: 'Motivo',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Anular'
        })

         if (isConfirmed) {
             this.ServicioService$.anularServicio(idServicio, value)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarServicio()
                         );
                    } else {
                         errorAlerta('Error', mensaje).then();
                    }
                });
         }
     }

     async abrirActivarServicio(idPerfil: string) {
         const {isConfirmed} = await Swal.fire({
             title: 'Activar Menú',
             text: '¿Esta seguro que desea activar la servicio seleccionado?',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Activar'
         })

         if (isConfirmed) {
             this.ServicioService$.activarServicio(idPerfil)
                 .subscribe(({mensaje, estado}) => {
                    if (estado) {
                         successAlerta('Éxito', mensaje).then(() =>
                             this.listarServicio()
                         );
                     } else {
                         errorAlerta('Error', mensaje).then();
                     }
                 });
         }
     }


}
