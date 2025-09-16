import {Component, ElementRef, ViewChild} from '@angular/core';
import { ModalPeriodoComponent } from '@modules/evaluacion/components/modal-periodo/modal-periodo.component';
import { PeriodoService } from '@services/evaluacion/periodo.service';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";



@Component({
  selector: 'app-periodo',
  templateUrl: './periodo.component.html',
  styleUrl: './periodo.component.scss'
})
export class PeriodoComponent {
@ViewChild(ModalPeriodoComponent) modalPeriodo!: ModalPeriodoComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Periodos'}];
    longitud: number = 15;
    pagina: number = 1;
    periodos: any = [];

    filtros = {
        id: '',
        descripcion: '',
        desde:'',
        hasta:'',
        div:'',
        mult:'',
        asis:'',
        estado:''

    };

    constructor(private PeriodoService$: PeriodoService  ) {
    }

    ngAfterViewInit() {
        this.listarPeriodos();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this. listarPeriodos();
    }

    filtrarPeriodo() {
        this.pagina = 1;
        this.listarPeriodos();
    }

    listarPeriodos() {
        let params: any= {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.loading = true;
        this.PeriodoService$.listarPeriodo(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.periodos = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }

    limpiarPeriodos() {
        this.filtros = {
           id: '',
        descripcion: '',
        desde:'',
        hasta:'',
        div:'',
        mult:'',
        asis:'',
        estado:''
        };
        this.listarPeriodos();
    }

    async abrirNuevoPeriodo() {
        let respuesta = await this.modalPeriodo.openModal(1);
        if (respuesta) {
            this.listarPeriodos();
        }
    }



    async abrirEditarPerfil(idMenu: string) {
        let respuesta = await this.modalPeriodo.openModal(2, idMenu);
        if (respuesta) {
            this.listarPeriodos();
        }
    }
 

 async generarFormatos(idPeriodo: string) {
         const {isConfirmed, value} = await Swal.fire({
             title: 'Generar Formatos',
             text: '¿Esta seguro que desea generar los formatos para el periodo  seleccionado?',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Generar'
        })

         if (isConfirmed) {
             this.PeriodoService$.generarFormatos(idPeriodo)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarPeriodos()
                         );
                    } else {
                         errorAlerta('Error', mensaje).then();
                    }
                });
         }
     }














    

     async abrirAnularPeriodo(idPeriodo: string) {
         const {isConfirmed, value} = await Swal.fire({
             title: 'Anular Perfil',
             text: '¿Esta seguro que desea anular el cargo seleccionado?',
             input: 'text',
             inputLabel: 'Motivo',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Anular'
        })

         if (isConfirmed) {
             this.PeriodoService$.anularPeriodo(idPeriodo, value)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarPeriodos()
                         );
                    } else {
                         errorAlerta('Error', mensaje).then();
                    }
                });
         }
     }

     async abrirActivarPeriodo(idPerfil: string) {
         const {isConfirmed} = await Swal.fire({
             title: 'Activar Menú',
             text: '¿Esta seguro que desea activar el perfil seleccionado?',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Activar'
         })

         if (isConfirmed) {
             this.PeriodoService$.activarPeriodo(idPerfil)
                 .subscribe(({mensaje, estado}) => {
                    if (estado) {
                         successAlerta('Éxito', mensaje).then(() =>
                             this.listarPeriodos()
                         );
                     } else {
                         errorAlerta('Error', mensaje).then();
                     }
                 });
         }
     }

    // abrirModalPerfilUsuario(idPerfil: string) {
    //     this.modalPerfilUsuario.openModal(idPerfil);
    // }

}
