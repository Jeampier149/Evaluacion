import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import { ModalCargoComponent } from '@modules/datos-generales/Components/modal-cargo/modal-cargo.component';
import { CargoService } from '@services/datos-generales/cargo.service';
import { ModalFormatoPComponent } from '@modules/formato/components/modal-formato-p/modal-formato-p.component';
import { FormatoPService } from '@services/formato/formato_p.service';


@Component({
  selector: 'app-formato-p',
  templateUrl: './formato-p.component.html',
  styleUrl: './formato-p.component.scss'
})
export class FormatoPComponent {
 @ViewChild(ModalFormatoPComponent) modalCargo!: ModalFormatoPComponent
    //@ViewChild(ModalPerfilUsuarioComponent) modalPerfilUsuario!: ModalPerfilUsuarioComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Formatos P'}];
    longitud: number = 15;
    pagina: number = 1;
    formatos: any = [];

    filtros = {
        id: '',
        descripcion: ''
    };

    constructor(private FormatoService$: FormatoPService) {
    }

    ngAfterViewInit() {
        this.listarFormatoP();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this. listarFormatoP();
    }

    filtrarCargo() {
        this.pagina = 1;
        this.listarFormatoP();
    }

    listarFormatoP() {
        let params: any= {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.loading = true;
        this.FormatoService$.listarFormatoP(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.formatos = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }

    limpiarFormatoP() {
        this.filtros = {
            id: '',
            descripcion: '',
        };
        this.listarFormatoP();
    }

    async abrirNuevoCargo() {
        let respuesta = await this.modalCargo.openModal(1);
        if (respuesta) {
            this.listarFormatoP();
        }
    }

    async abrirEditarPerfil(idMenu: string) {
        let respuesta = await this.modalCargo.openModal(2, idMenu);
        if (respuesta) {
            this.listarFormatoP();
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
             this.FormatoService$.anularFormatoP(idCargo, value)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarFormatoP()
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
             this.FormatoService$.activarFormatoP(idPerfil)
                 .subscribe(({mensaje, estado}) => {
                    if (estado) {
                         successAlerta('Éxito', mensaje).then(() =>
                             this.listarFormatoP()
                         );
                     } else {
                         errorAlerta('Error', mensaje).then();
                     }
                 });
         }
     }

}
