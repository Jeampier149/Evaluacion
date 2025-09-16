import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import { ModalCargoComponent } from '@modules/datos-generales/Components/modal-cargo/modal-cargo.component';
import { CargoService } from '@services/datos-generales/cargo.service';


@Component({
  selector: 'app-cargo',
  templateUrl: './cargo.component.html',
  styleUrl: './cargo.component.scss'
})
export class CargoComponent {
    @ViewChild(ModalCargoComponent) modalCargo!: ModalCargoComponent
    //@ViewChild(ModalPerfilUsuarioComponent) modalPerfilUsuario!: ModalPerfilUsuarioComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Cargos'}];
    longitud: number = 15;
    pagina: number = 1;
    cargos: any = [];

    filtros = {
        id: '',
        descripcion: ''
    };

    constructor(private CargoService$: CargoService) {
    }

    ngAfterViewInit() {
        this.listarCargos();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this. listarCargos();
    }

    filtrarCargo() {
        this.pagina = 1;
        this.listarCargos();
    }

    listarCargos() {
        let params: any= {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.loading = true;
        this.CargoService$.listarCargos(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.cargos = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }

    limpiarCargos() {
        this.filtros = {
            id: '',
            descripcion: '',
        };
        this.listarCargos();
    }

    async abrirNuevoCargo() {
        let respuesta = await this.modalCargo.openModal(1);
        if (respuesta) {
            this.listarCargos();
        }
    }

    async abrirEditarPerfil(idMenu: string) {
        let respuesta = await this.modalCargo.openModal(2, idMenu);
        if (respuesta) {
            this.listarCargos();
        }
    }

     async abrirAnularCargo(idCargo: string) {
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
             this.CargoService$.anularCargo(idCargo, value)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarCargos()
                         );
                    } else {
                         errorAlerta('Error', mensaje).then();
                    }
                });
         }
     }

     async abrirActivarCargo(idPerfil: string) {
         const {isConfirmed} = await Swal.fire({
             title: 'Activar Menú',
             text: '¿Esta seguro que desea activar el perfil seleccionado?',
             showCancelButton: true,
             cancelButtonText: 'Cancelar',
             confirmButtonText: 'Activar'
         })

         if (isConfirmed) {
             this.CargoService$.activarCargo(idPerfil)
                 .subscribe(({mensaje, estado}) => {
                    if (estado) {
                         successAlerta('Éxito', mensaje).then(() =>
                             this.listarCargos()
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
