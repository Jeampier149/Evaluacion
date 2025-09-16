import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import { ModalUnidadComponent } from '@modules/datos-generales/Components/modal-unidad/modal-unidad.component';
import { UnidadService } from '@services/datos-generales/unidad.service';


@Component({
  selector: 'app-unidad-organica',
  templateUrl: './unidad-organica.component.html',
  styleUrl: './unidad-organica.component.scss'
})
export class UnidadOrganicaComponent {
 @ViewChild(ModalUnidadComponent) modalUnidad!: ModalUnidadComponent
   
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Unidad'}];
    longitud: number = 15;
    pagina: number = 1;
    unidades: any = [];

    filtros = {
        descripcion: '',
        coordinador: ''
    };

    constructor(private UnidadService$: UnidadService) {
    }

    ngAfterViewInit() {
        this.listarUnidad();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this. listarUnidad();
    }

    filtrarUnidad() {
        this.pagina = 1;
        this.listarUnidad();
    }

    listarUnidad() {
        let params: any= {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.loading = true;
        this.UnidadService$.listarUnidad(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.unidades = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }

    limpiarUnidad() {
        this.filtros = {
            descripcion: '',
            coordinador: '',
        };
        this.listarUnidad();
    }

    async abrirNuevoUnidad() {
        let respuesta = await this.modalUnidad.openModal(1);
        if (respuesta) {
            this.listarUnidad();
        }
    }

    async abrirEditarUnidad(idMenu: string) {
        let respuesta = await this.modalUnidad.openModal(2, idMenu);
        if (respuesta) {
            this.listarUnidad();
        }
    }

     async abrirAnularUnidad(idUnidad: string) {
         const {isConfirmed, value} = await Swal.fire({
             title: 'Anular Unidad',
             text: '¿Esta seguro que desea anular la unidad seleccionada?',
             input: 'text',
             inputLabel: 'Motivo',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Anular'
        })

         if (isConfirmed) {
             this.UnidadService$.anularUnidad(idUnidad, value)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarUnidad()
                         );
                    } else {
                         errorAlerta('Error', mensaje).then();
                    }
                });
         }
     }

     async abrirActivarUnidad(idPerfil: string) {
         const {isConfirmed} = await Swal.fire({
             title: 'Activar Menú',
             text: '¿Esta seguro que desea activar la unidad seleccionado?',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Activar'
         })

         if (isConfirmed) {
             this.UnidadService$.activarUnidad(idPerfil)
                 .subscribe(({mensaje, estado}) => {
                    if (estado) {
                         successAlerta('Éxito', mensaje).then(() =>
                             this.listarUnidad()
                         );
                     } else {
                         errorAlerta('Error', mensaje).then();
                     }
                 });
         }
     }


}
