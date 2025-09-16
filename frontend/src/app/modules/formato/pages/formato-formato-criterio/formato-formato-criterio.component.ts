import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import { ModalFormatoCabComponent } from '@modules/formato/components/modal-formato-cab/modal-formato-cab.component';
import { FormatoCriterioService } from '@services/formato/formato_criterio.service';

@Component({
  selector: 'app-formato-formato-criterio',
  templateUrl: './formato-formato-criterio.component.html',
  styleUrl: './formato-formato-criterio.component.scss'
})
export class FormatoFormatoCriterioComponent {
@ViewChild(ModalFormatoCabComponent) modalCargo!: ModalFormatoCabComponent
    //@ViewChild(ModalPerfilUsuarioComponent) modalPerfilUsuario!: ModalPerfilUsuarioComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Formatos Cab'}];
    longitud: number = 15;
    pagina: number = 1;
    factores:any=[]
    formatos: any = [];
    categoria:any='01'
    factor:any='001'

    constructor(private FormatoCriterio$: FormatoCriterioService) {
    }

    ngAfterViewInit() {
        this.listarFactor()
        this.listarFormatoCriterio();
        this.inpFocus.nativeElement.focus();
    }


    listarFormatoCriterio() {
        let factor=this.factor
        this.loading = true;
        this.FormatoCriterio$.listarFormatoCriterio(factor)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.formatos = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }
    listarFactor() {
        let categoria=this.categoria
        this.loading = true;
        this.FormatoCriterio$.listarFactor(categoria)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.factores = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }
    

    async abrirNuevoCargo() {
        let respuesta = await this.modalCargo.openModal(1);
        if (respuesta) {
            this.listarFormatoCriterio();
        }
    }

    async abrirEditarPerfil(idMenu: string) {
        let respuesta = await this.modalCargo.openModal(2, idMenu);
        if (respuesta) {
            this.listarFormatoCriterio();
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
             this.FormatoCriterio$.anularFormatoCriterio(idCargo, value)
                 .subscribe(({datos, mensaje, estado}) => {
                     if (estado || !datos) { successAlerta('Éxito', mensaje).then(() =>
                             this.listarFormatoCriterio()
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
             this.FormatoCriterio$.activarFormatoCriterio(idPerfil)
                 .subscribe(({mensaje, estado}) => {
                    if (estado) {
                         successAlerta('Éxito', mensaje).then(() =>
                             this.listarFormatoCriterio()
                         );
                     } else {
                         errorAlerta('Error', mensaje).then();
                     }
                 });
         }
     }

}
