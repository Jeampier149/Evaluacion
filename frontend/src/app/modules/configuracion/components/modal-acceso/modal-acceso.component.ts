import {Component, ElementRef, ViewChild} from '@angular/core';
import {Modal} from 'bootstrap';
import {finalize} from "rxjs";
import {errorAlerta, successAlerta} from "@shared/utils";
import {IAcceso, IListaAcceso, IListaAccesoParams} from "@interfaces/configuracion/acceso.interface";
import {AccesoService} from "@services/configuracion/acceso.service";
import Swal from "sweetalert2";
import {PerfilService} from "@services/configuracion/perfil.service";
import {IListaPerfilCombo} from "@interfaces/configuracion/perfil.interface";

@Component({
    selector: 'app-modal-acceso',
    templateUrl: './modal-acceso.component.html'
})
export class ModalAccesoComponent {
    @ViewChild('modalAcceso') modalEl!: any;
    @ViewChild('selFocus') selFocus!: ElementRef;
    modal: any;
    longitud: number = 15;
    pagina: number = 1;
    nombreMenu: string = '';
    loading: boolean = false;
    accesos: IListaAcceso[] = [];

    perfil: string = '';
    perfiles: IListaPerfilCombo[] = [];

    filtros = {
        idMenu: '',
        idPerfil: ''
    };

    constructor(private AccesoService$: AccesoService, private PerfilService$: PerfilService) {
        this.obtenerPerfiles();
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

    obtenerPerfiles() {
        this.loading = true;
        this.PerfilService$.listarComboPerfiles()
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.perfiles = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    openModal(idMenu?: string, nombreMenu?: string): void {
        if (idMenu && nombreMenu) {
            this.filtros.idMenu = idMenu;
            this.nombreMenu = nombreMenu;
        }
        this.listarAccesos();
        this.modal.show();
    }

    closeModal() {
        this.modal.hide();
        this.resetModal();
    }

    listarAccesos() {
        this.loading = true;
        let params: IListaAccesoParams = {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }
        this.AccesoService$.listarAccesos(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.accesos = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarAccesos();
    }

    filtrarUsuarios() {
        this.pagina = 1;
        this.listarAccesos();
    }

    limpiarFiltros() {
        this.filtros = {
            idPerfil: '',
            idMenu: this.filtros.idMenu,
        };
        this.listarAccesos();
    }


    resetModal() {
        this.pagina = 1;
        this.nombreMenu = '';
        this.perfil = '';
        this.filtros = {
            idPerfil: '',
            idMenu: '',
        }
    }

    async abrirAnularAcceso(idMenu: string, idPerfil: string) {
        let modal = document.getElementsByClassName('modal show')[0];
        let target = (modal as HTMLElement) ?? 'body';
        const {isConfirmed, value} = await Swal.fire({
            title: 'Anular Acceso',
            text: '¿Esta seguro que desea quitar el acceso?',
            input: 'text',
            inputLabel: 'Motivo',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Anular',
            target: target
        })

        if (isConfirmed) {
            let params: IAcceso = {
                idMenu: idMenu,
                idPerfil: idPerfil
            }
            this.AccesoService$.anularAcceso(params, value)
                .subscribe(({datos, mensaje, estado}) => {
                    if (estado || !datos) {
                        successAlerta('Éxito', mensaje).then(() =>
                            this.listarAccesos()
                        );
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
    }

    async agregarAcceso() {
        if (this.perfil === '') {
            errorAlerta('Error', 'Seleccione un perfil.').then();
            return
        }

        let params: IAcceso = {
            idMenu: this.filtros.idMenu,
            idPerfil: this.perfil
        }
        this.AccesoService$.agregarAcceso(params)
            .subscribe(({datos, mensaje, estado}) => {
                if (estado || !datos) {
                    successAlerta('Éxito', mensaje).then(() =>
                        this.listarAccesos()
                    );
                } else {
                    errorAlerta('Error', mensaje).then();
                }
            });
    }

    ngOnDestroy() {
        this.modal.dispose();
    }
}
