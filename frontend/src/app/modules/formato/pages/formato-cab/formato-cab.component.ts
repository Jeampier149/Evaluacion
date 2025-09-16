import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import { FormatosCabService } from '@services/formato/formato_cab.service';
import { ModalFormatoCabComponent } from '@modules/formato/components/modal-formato-cab/modal-formato-cab.component';


@Component({
  selector: 'app-formato-cab',
  templateUrl: './formato-cab.component.html',
  styleUrl: './formato-cab.component.scss'
})
export class FormatoCabComponent {
@ViewChild(ModalFormatoCabComponent) modalCargo!: ModalFormatoCabComponent
    //@ViewChild(ModalPerfilUsuarioComponent) modalPerfilUsuario!: ModalPerfilUsuarioComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Formatos Cab'}];
    longitud: number = 15;
    pagina: number = 1;
    formatos: any = [];
    categoria:any='01'
  

    constructor(private FormatoService$: FormatosCabService) {
    }

    ngAfterViewInit() {
        this.listarFormatoCab();
        this.inpFocus.nativeElement.focus();
    }


    listarFormatoCab() {
        let categoria=this.categoria
        this.loading = true;
        this.FormatoService$.listarFormatosCabs(categoria)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.formatos = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }

    

    async abrirNuevoCargo() {
        let respuesta = await this.modalCargo.openModal(1);
        if (respuesta) {
            this.listarFormatoCab();
        }
    }

    async abrirEditarPerfil(idMenu: string) {
        let respuesta = await this.modalCargo.openModal(2, idMenu);
        if (respuesta) {
            this.listarFormatoCab();
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
             this.FormatoService$.anularFormatosCab(idCargo, value)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarFormatoCab()
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
             this.FormatoService$.activarFormatosCab(idPerfil)
                 .subscribe(({mensaje, estado}) => {
                    if (estado) {
                         successAlerta('Éxito', mensaje).then(() =>
                             this.listarFormatoCab()
                         );
                     } else {
                         errorAlerta('Error', mensaje).then();
                     }
                 });
         }
     }

}
