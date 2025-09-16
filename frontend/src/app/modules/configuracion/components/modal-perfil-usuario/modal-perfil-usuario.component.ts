import {Component, ViewChild} from '@angular/core';
import {Modal} from 'bootstrap';
import {finalize} from "rxjs";
import {errorAlerta} from "@shared/utils";
import {PerfilService} from "@services/configuracion/perfil.service";
import {
    IListaPerfilUsuarioParams,
    IListaUsuarioPerfil
} from "@interfaces/configuracion/perfil.interface";

@Component({
    selector: 'app-modal-perfil-usuario',
    templateUrl: './modal-perfil-usuario.component.html'
})
export class ModalPerfilUsuarioComponent {
    @ViewChild('modalPerfilUsuario') modalEl!: any;
    modal: any;
    longitud: number = 15;
    pagina: number = 1;
    idPerfil: string = '';
    loading: boolean = false;
    usuarios: IListaUsuarioPerfil[] = [];

    filtros = {
        idPerfil: '',
        codigo_usuario: '',
        nombres: ''
    };


    constructor(private PerfilService$: PerfilService) {
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        });
    }


    openModal(idPerfil?: string): void {
        if (idPerfil) {
            this.filtros.idPerfil = idPerfil;
        }
        this.listarUsuario();
        this.modal.show();
    }

    closeModal() {
        this.modal.hide();
        this.resetModal();
    }

    listarUsuario() {
        this.loading = true;
        let params: IListaPerfilUsuarioParams = {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }
        this.PerfilService$.listarPerfilesPorUsuario(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.usuarios = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarUsuario();
    }

    filtrarUsuarios() {
        this.pagina = 1;
        this.listarUsuario();
    }

    limpiarFiltros() {
        this.filtros = {
            idPerfil: '',
            codigo_usuario: '',
            nombres: '',
        };
        this.listarUsuario();
    }


    resetModal() {
        this.idPerfil = '';
        this.pagina = 1;
        this.filtros = {
            idPerfil: '',
            codigo_usuario: '',
            nombres: ''
        }
    }

    ngOnDestroy() {
        this.modal.dispose();
    }
}
