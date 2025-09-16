import {Component, ViewChild} from '@angular/core';
import {Modal} from "bootstrap";
import {finalize} from "rxjs";
import {FormControl, FormGroup, Validators} from "@angular/forms";
import {errorAlerta, successAlerta} from "@shared/utils";
import {UsuarioService} from "@services/mantenimiento/usuario.service";
import {ModalPerfilComponent} from "@modules/mantenimiento/components/modal-perfil/modal-perfil.component";
import {PerfilService} from "@services/mantenimiento/perfil.service";
import {ProfesionalService} from "@services/mantenimiento/profesional.service";

import Swal from "sweetalert2";
import {IUsuario} from "@interfaces/mantenimiento/usuario.interface";

@Component({
    selector: 'app-modal-usuario',
    templateUrl: './modal-usuario.component.html'
})
export class ModalUsuarioComponent {
    @ViewChild('modalUsuario') modalEl?: any;
    @ViewChild(ModalPerfilComponent) modalPerfil?: ModalPerfilComponent;

    modal: any;
    resolve: any;
    reject: any;

    loading: boolean = false;

    tipo: number = 1;
    codigo?: string;

    descripcionPerfil: string = '';
    nombresProfesional: string = '';

    usuarioForm!: FormGroup;

    buscador = {
        profesional: false,
    }

    constructor(
        private PerfilService$: PerfilService,
        private ProfesionalService$: ProfesionalService,
        private UsuarioService$: UsuarioService) {
        this.inicializarFormulario();
    }

    obtenerUsuario() {
        this.loading = true;
        this.UsuarioService$.obtenerUsuario(this.codigo!)
            .pipe(finalize(() => {
                this.loading = false;
            }))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.usuarioForm.get('codigo')?.setValue(datos!.codigo);
                    this.usuarioForm.get('nombres')?.setValue(datos!.nombres);
                    this.usuarioForm.get('perfil')?.setValue(datos!.perfil);
                    this.buscarPerfil();
                   
                }
            })
    }

    inicializarFormulario() {
        this.usuarioForm = new FormGroup({
            codigo: new FormControl('', {nonNullable: true, validators: [Validators.required]}),
            nombres: new FormControl('', {nonNullable: true, validators: [Validators.required]}),
            perfil: new FormControl('', {nonNullable: true, validators: [Validators.required]}),
            profesional: new FormControl('', {nonNullable: true}),
            busqueda: new FormControl(false, {nonNullable: true}),
            reporte: new FormControl(false, {nonNullable: true}),
            archivero: new FormControl(false, {nonNullable: true}),
        });
    }

    guardarUsuario() {
        let usuario: IUsuario = {
            nombres: this.usuarioForm.get('nombres')?.value,
            perfil: this.usuarioForm.get('perfil')?.value,
            codigo: ''
        };

        this.loading = true;
        if (this.tipo === 1) { // Nuevo
            usuario.codigo = this.usuarioForm.get('codigo')?.value;
            this.UsuarioService$.guardarUsuario(usuario)
                .pipe(finalize(() => {
                    this.loading = false;
                })).subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    successAlerta('Éxito!', mensaje).then(() => {
                        this.closeModal();
                    });
                } else {
                    let html = '<ul>';
                    datos.forEach((error: string) => {
                        html += '<li>' + error + '</li>'
                    })
                    html += '</ul>';
                    errorAlerta('Error', '', html, {customClass: {htmlContainer: 'text-start'}}).then();
                }
            })
        } else { // Editar
            usuario.codigo = this.codigo!;
            this.UsuarioService$.editarUsuario(usuario)
                .pipe(finalize(() => {
                    this.loading = false;
                })).subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    successAlerta('Éxito!', mensaje).then(() => {
                        this.closeModal();
                    });
                } else {
                    let html = '<ul>';
                    datos.forEach((error: string) => {
                        html += '<li>' + error + '</li>'
                    })
                    html += '</ul>';
                    errorAlerta('Error', '', html, {customClass: {htmlContainer: 'text-start'}}).then();
                }
            })
        }
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false
        })
    }

    ngOnInit() {

    }

    openModal(tipo: number, codigo?: string): Promise<boolean> {
        this.tipo = tipo;
        console.log(this.tipo)
        if (tipo === 2) {
            this.codigo = codigo;
            this.obtenerUsuario();
            this.usuarioForm.get('codigo')?.disable();
        }
        this.modal.show();
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
        })
    }

    closeModal() {
        this.modal.hide();
        this.resolve(true);
        this.resetModal();
    }

    ngOnDestroy() {
        this.modal.dispose();
    }

    async abrirModalPerfil() {
        this.modal.hide();
        let perfil = await this.modalPerfil?.openModal();
        if (perfil) {
            this.usuarioForm.get('perfil')?.setValue(perfil.codigo);
            this.descripcionPerfil = perfil.descripcion;
        }
        this.modal.show();
    }

    buscarPerfil() {
        let perfil = this.usuarioForm.get('perfil')?.value;
        this.loading = true;
        this.PerfilService$.buscarPerfil(perfil)
            .pipe(finalize(() => {
                this.loading = false;
            }))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.usuarioForm.get('perfil')?.setValue(datos!.codigo);
                    this.descripcionPerfil = datos!.descripcion;
                } else {
                    errorAlerta('Error!', mensaje).then();
                    this.descripcionPerfil = '';
                }
            })
    }

    async resetContrasenia() {
        const {isConfirmed, value} = await Swal.fire({
            title: 'Re-establecer Contraseña',
            text: '¿Esta seguro que desea limpiar la contraseña del usuario?',
            showCancelButton: true,
            focusCancel: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Restablecer',
            target: this.modalEl.nativeElement
        })
        if (isConfirmed) {
            this.UsuarioService$.reestrablecerUsuario(this.codigo!)
                .subscribe(({mensaje, estado}) => {
                    if (estado) {
                        successAlerta('Éxito', mensaje).then();
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
    }

    private resetModal() {
        this.tipo = 1;
        this.codigo = undefined;
        this.usuarioForm.reset();
        this.descripcionPerfil = '';
        this.nombresProfesional = '';
        this.buscador.profesional = false;
        this.usuarioForm.get('codigo')?.enable();
    }

}
