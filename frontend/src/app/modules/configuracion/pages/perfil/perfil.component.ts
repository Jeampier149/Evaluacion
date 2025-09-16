import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import {PerfilService} from "@services/configuracion/perfil.service";
import {IListaPerfil, IListaPerfilParams} from "@interfaces/configuracion/perfil.interface";
import {ModalPerfilComponent} from "@modules/configuracion/components/modal-perfil/modal-perfil.component";
import {
    ModalPerfilUsuarioComponent
} from "@modules/configuracion/components/modal-perfil-usuario/modal-perfil-usuario.component";

@Component({
    selector: 'app-perfil',
    templateUrl: './perfil.component.html',
})
export class PerfilComponent {
    @ViewChild(ModalPerfilComponent) modalPerfil!: ModalPerfilComponent
    @ViewChild(ModalPerfilUsuarioComponent) modalPerfilUsuario!: ModalPerfilUsuarioComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Perfiles'}];
    longitud: number = 15;
    pagina: number = 1;
    perfiles: IListaPerfil[] = [];

    filtros = {
        id: '',
        descripcion: ''
    };

    constructor(private PerfilService$: PerfilService) {
    }

    ngAfterViewInit() {
        this.listarPerfil();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarPerfil();
    }

    filtrarPerfil() {
        this.pagina = 1;
        this.listarPerfil();
    }

    listarPerfil() {
        let params: IListaPerfilParams = {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.loading = true;
        this.PerfilService$.listarPerfiles(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.perfiles = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }

    limpiarFiltros() {
        this.filtros = {
            id: '',
            descripcion: '',
        };
        this.listarPerfil();
    }

    async abrirNuevoPerfil() {
        let respuesta = await this.modalPerfil.openModal(1);
        if (respuesta) {
            this.listarPerfil();
        }
    }

    async abrirEditarPerfil(idMenu: string) {
        let respuesta = await this.modalPerfil.openModal(2, idMenu);
        if (respuesta) {
            this.listarPerfil();
        }
    }

    async abrirAnularPerfil(idPerfil: string) {
        const {isConfirmed, value} = await Swal.fire({
            title: 'Anular Perfil',
            text: '¿Esta seguro que desea anular el perfil seleccionado?',
            input: 'text',
            inputLabel: 'Motivo',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Anular'
        })

        if (isConfirmed) {
            this.PerfilService$.anularPerfil(idPerfil, value)
                .subscribe(({datos, mensaje, estado}) => {
                    if (estado || !datos) {
                        successAlerta('Éxito', mensaje).then(() =>
                            this.listarPerfil()
                        );
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
    }

    async abrirActivarPerfil(idPerfil: string) {
        const {isConfirmed} = await Swal.fire({
            title: 'Activar Menú',
            text: '¿Esta seguro que desea activar el perfil seleccionado?',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Activar'
        })

        if (isConfirmed) {
            this.PerfilService$.activarPerfil(idPerfil)
                .subscribe(({mensaje, estado}) => {
                    if (estado) {
                        successAlerta('Éxito', mensaje).then(() =>
                            this.listarPerfil()
                        );
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
    }

    abrirModalPerfilUsuario(idPerfil: string) {
        this.modalPerfilUsuario.openModal(idPerfil);
    }

}
