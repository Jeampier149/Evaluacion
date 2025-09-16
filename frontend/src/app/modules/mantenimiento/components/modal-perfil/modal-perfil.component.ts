import {Component, ElementRef, ViewChild} from '@angular/core';
import {Modal} from "bootstrap";
import {finalize} from "rxjs";
import {PerfilService} from "@services/mantenimiento/perfil.service";
import {IListaPerfil, IListaPerfilParams} from "@interfaces/mantenimiento/perfil.interface";

@Component({
    selector: 'app-modal-perfil',
    templateUrl: './modal-perfil.component.html'
})
export class ModalPerfilComponent {
    @ViewChild('modalPerfil') modalEl?: any;
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    modal: any;
    resolve: any;
    reject: any;

    loading: boolean = false;
    pagina: number = 1;
    longitud: number = 10;

    filtro = {
        codigo: '',
        descripcion: '',
        estado: '1'
    }

    perfiles: IListaPerfil[] = [];

    constructor(private PerfilService$: PerfilService) {
    }

    listarPerfil() {
        this.loading = true;
        let params: IListaPerfilParams = {
            ...this.filtro,
            longitud: this.longitud,
            pagina: this.pagina
        }

        this.PerfilService$.listarPerfil(params)
            .pipe(
                finalize(() => {
                    this.loading = false;
                })
            ).subscribe(e => {
            this.perfiles = e.datos!;
        });
    }

    buscarPerfil() {
        this.pagina = 1;
        this.listarPerfil();
    }

    cambiarPagina(pagina: number) {
        this.pagina = pagina;
        this.listarPerfil();
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        });
        this.modalEl.nativeElement.addEventListener('shown.bs.modal', () => {
            this.inpFocus.nativeElement.focus();
        })
    }

    openModal(): Promise<IListaPerfil | undefined> {
        this.modal.show();
        this.listarPerfil();
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
        })
    }

    closeModal() {
        this.modal.hide();
        this.resolve();
        this.resetModal();
    }

    seleccionarPerfil(perfil: IListaPerfil) {
        this.modal.hide();
        this.resolve(perfil);
        this.resetModal();
    }

    limpiarCampos() {
        this.filtro = {
            codigo: '',
            descripcion: '',
            estado: '1'
        };
        this.buscarPerfil();
    }

    ngOnDestroy() {
        this.modal.dispose();
    }

    private resetModal() {
        this.filtro = {
            codigo: '',
            descripcion: '',
            estado: '1'
        };
        this.pagina = 1;
    }

}
