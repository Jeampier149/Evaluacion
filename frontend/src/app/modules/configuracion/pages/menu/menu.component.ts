import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {MenuService} from "@services/configuracion/menu.service";
import {IListaMenu, IListaMenuParams} from "@interfaces/configuracion/menu.interface";
import {errorAlerta, successAlerta} from "@shared/utils";
import {ModalMenuComponent} from "@modules/configuracion/components/modal-menu/modal-menu.component";
import {ModalAccionComponent} from "@modules/configuracion/components/modal-accion/modal-accion.component";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import {ModalAccesoComponent} from "@modules/configuracion/components/modal-acceso/modal-acceso.component";

@Component({
    selector: 'app-menu',
    templateUrl: './menu.component.html',
})
export class MenuComponent {
    @ViewChild(ModalMenuComponent) modalMenu!: ModalMenuComponent
    @ViewChild(ModalAccionComponent) modalAccion!: ModalAccionComponent
    @ViewChild(ModalAccesoComponent) modalAcceso!: ModalAccesoComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Menus'}];
    longitud: number = 15;
    pagina: number = 1;
    menus: IListaMenu[] = [];

    filtros = {
        nombre: '',
        padre: '',
        orden: '',
        estado: 'A'
    };

    constructor(private MenuService$: MenuService) {
    }

    ngAfterViewInit() {
        this.listarMenu();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarMenu();
    }

    filtrarMenu() {
        this.pagina = 1;
        this.listarMenu();
    }

    listarMenu() {
        let params: IListaMenuParams = {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.loading = true;
        this.MenuService$.listarMenus(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.menus = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }

    limpiarFiltros() {
        this.filtros = {
            nombre: '',
            orden: '',
            padre: '',
            estado: 'A'
        };
        this.listarMenu();
    }

    async abrirNuevoMenu() {
        let respuesta = await this.modalMenu.openModal(1);
        if (respuesta) {
            this.listarMenu();
        }
    }

    async abrirEditarMenu(idMenu: string) {
        let respuesta = await this.modalMenu.openModal(2, idMenu);
        if (respuesta) {
            this.listarMenu();
        }
    }

    async abrirAnularMenu(idMenu: string) {
        const {isConfirmed, value} = await Swal.fire({
            title: 'Anular Menú',
            text: '¿Esta seguro que desea anular el menu seleccionado?',
            input: 'text',
            inputLabel: 'Motivo',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Anular'
        })

        if (isConfirmed) {
            this.MenuService$.anularMenu(idMenu, value)
                .subscribe(({datos, mensaje, estado}) => {
                    if (estado || !datos) {
                        successAlerta('Éxito', mensaje).then(() =>
                            this.listarMenu()
                        );
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
    }

    async abrirActivarMenu(idMenu: string) {
        const {isConfirmed} = await Swal.fire({
            title: 'Activar Menú',
            text: '¿Esta seguro que desea activar el menú seleccionado?',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Activar'
        })

        if (isConfirmed) {
            this.MenuService$.activarMenu(idMenu)
                .subscribe(({mensaje, estado}) => {
                    if (estado) {
                        successAlerta('Éxito', mensaje).then(() =>
                            this.listarMenu()
                        );
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
    }

    async abrirModalAccion(idMenu: string, nombreMenu: string) {
        let respuesta = await this.modalAccion.openModal(idMenu, nombreMenu);
        if (respuesta) {
            this.listarMenu();
        }
    }

    abrirModalAcceso(idMenu: string, nombreMenu: string) {
        this.modalAcceso.openModal(idMenu, nombreMenu);
    }

}
