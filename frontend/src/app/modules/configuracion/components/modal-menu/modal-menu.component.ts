import {Component, ElementRef, ViewChild} from '@angular/core';
import {Modal} from 'bootstrap';
import {MenuService} from "@services/configuracion/menu.service";
import {finalize} from "rxjs";
import {errorAlerta, successAlerta} from "@shared/utils";
import {IListaMenuCombo, IMenu} from "@interfaces/configuracion/menu.interface";

@Component({
    selector: 'app-modal-menu',
    templateUrl: './modal-menu.component.html'
})
export class ModalMenuComponent {
    @ViewChild('modalMenu') modalEl!: any;
    @ViewChild('selFocus') selFocus!: ElementRef;
    modal: any;
    resolve: any;
    reject: any;
    idMenu: string = '';
    tipo: number = 1; // 1 Nuevo, 2 Editar
    loading: boolean = false;
    menus: IListaMenuCombo[] = [];
    formulario = {
        padre: '',
        nombre: '',
        orden: 0,
        icono: '',
        ruta: ''
    }

    constructor(private MenuService$: MenuService) {
        this.obtenerMenus();
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        });
        this.modalEl.nativeElement.addEventListener('shown.bs.modal', () => {
            this.selFocus.nativeElement.focus();
        })
    }


    obtenerMenus() {
        this.MenuService$.listaMenuCombo()
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.menus = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    openModal(tipo: number, idMenu?: string): Promise<boolean> {
        this.modal.show();
        this.tipo = tipo;
        if (tipo === 2) {
            this.idMenu = idMenu!;
            this.obtenerMenu();
        }
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
        })
    }

    closeModal() {
        this.modal.hide();
        this.resolve(false);
        this.resetModal();
    }

    obtenerMenu() {
        this.loading = true;
        this.MenuService$.obtenerMenu(this.idMenu)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.formulario.nombre = datos!.nombre;
                    this.formulario.orden = datos!.orden;
                    this.formulario.padre = datos!.padre;
                    this.formulario.icono = datos!.icono;
                    this.formulario.ruta = datos!.ruta;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    guardarMenu() {
        this.loading = true;
        let params: IMenu = {
            icono: this.formulario.icono,
            padre: this.formulario.padre,
            ruta: this.formulario.ruta,
            id: this.idMenu,
            orden: this.formulario.orden,
            nombre: this.formulario.nombre
        }

        if (this.tipo === 1) {
            this.MenuService$.guardarMenu(params)
                .pipe(finalize(() => this.loading = false))
                .subscribe(({estado, mensaje}) => {
                    if (estado) {
                        successAlerta('Éxito!', mensaje).then(() => {
                            this.modal.hide();
                            this.resolve(true);
                            this.resetModal();
                        });
                    } else {
                        errorAlerta('Error!', mensaje).then();
                    }
                })
        }

        if (this.tipo === 2) {
            this.MenuService$.editarMenu(params)
                .pipe(finalize(() => this.loading = false))
                .subscribe(({estado, mensaje}) => {
                    if (estado) {
                        successAlerta('Éxito!', mensaje).then(() => {
                            this.modal.hide();
                            this.resolve(true);
                            this.resetModal();
                        });
                    } else {
                        errorAlerta('Error!', mensaje).then();
                    }
                })
        }
        this.obtenerMenus();

    }

    resetModal() {
        this.tipo = 1;
        this.idMenu = '';
        this.formulario = {
            padre: '',
            ruta: '',
            icono: '',
            orden: 0,
            nombre: ''
        }
    }

    limpiarCampos() {
    }

    ngOnDestroy() {
        this.modal.dispose();
    }
}
