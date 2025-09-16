import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {finalize} from "rxjs";
import {Session} from "@store/session.actions";
import {SessionSelectors} from "@store/index.session";
import {Store} from "@ngrx/store";
import Swal from "sweetalert2";
import {errorAlerta, successAlerta} from "@shared/utils";
import {IListaUsuario, IListaUsuarioParams} from "@interfaces/mantenimiento/usuario.interface";
import {UsuarioService} from "@services/mantenimiento/usuario.service";
import {ModalUsuarioComponent} from "@modules/mantenimiento/components/modal-usuario/modal-usuario.component";
import { ProcesarEjecucionService } from '@services/mantenimiento/procesar-ejecucion.service';

@Component({
    selector: 'app-usuarios',
    templateUrl: './usuario.component.html'
})
export class UsuarioComponent {
    @ViewChild('inpFocus') inpFocus!: ElementRef<HTMLInputElement>;
    @ViewChild(ModalUsuarioComponent) modalUsuario!: ModalUsuarioComponent;
    rutas: rutaBreadCrumb[] = [{
        nombre: 'Mantenimiento',
        ruta: '/mantenimiento/usuarios'
    }, {nombre: 'Usuarios de Sistema'}];
    accesos: any[] | undefined = [];
    longitud: number = 15;
    pagina: number = 1;
    loading: boolean = false;
    usuarios: IListaUsuario[] = [];

    filtros = {
        codigo: '',
        nombres: '',
        departamento: '',
        perfil: '',
        descripcionPerfil: '',
        estado: '1'
    }

    longitudes: number[] = [15, 20, 50, 100];

    constructor(private UsuariosService$: UsuarioService,
                private store: Store<Session>) {
        this.listarUsuario();
        this.obtenerPermisos();
    }

    ngOnInit() {
    }

    ngAfterViewInit() {
        this.inpFocus.nativeElement.focus();
    }

    obtenerPermisos() {
        this.store.select(SessionSelectors.session).subscribe((e) => {
            this.accesos = e.accesos;
        });
    }

    listarUsuario() {
        this.loading = true;
        let params: IListaUsuarioParams = {
            ...this.filtros,
            pagina: this.pagina,
            longitud: this.longitud
        }
        this.UsuariosService$.listarUsuario(params)
            .pipe(finalize(() => {
                this.loading = false;
            }))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.usuarios = datos!;
                }
            })
    }

    filtrarUsuarios() {
        this.pagina = 1;
        this.listarUsuario();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarUsuario();
    }

    limpiarCampos() {
        this.filtros = {
            nombres: '',
            departamento: '',
            estado: '1',
            codigo: '',
            descripcionPerfil: '',
            perfil: ''
        }
        this.pagina = 1;
        this.listarUsuario();
        this.inpFocus.nativeElement.focus();
    }

    async abrirModalUsuario(tipo: number, codigo?: string) {
        await this.modalUsuario.openModal(tipo, codigo);
    }

    async anularUsuario(codigo: string) {
        const {isConfirmed, value} = await Swal.fire({
            title: 'Anular Usuario',
            text: '¿Esta seguro que desea anular el usuario seleccionado?',
            input: 'text',
            inputLabel: 'Motivo',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Anular'
        })

        if (isConfirmed) {
            this.UsuariosService$.anularUsuario(codigo, value)
                .subscribe(({datos, mensaje, estado}) => {
                    if (estado || !datos) {
                        successAlerta('Éxito', mensaje).then(() =>
                            this.filtrarUsuarios()
                        );
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
    }

    async activarUsuario(codigo: string) {
        const {isConfirmed} = await Swal.fire({
            title: 'Activar Usuario',
            text: '¿Esta seguro que desea activar el usuario seleccionado?',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Activar'
        })

        if (isConfirmed) {
            this.UsuariosService$.activarUsuario(codigo)
                .subscribe(({estado, mensaje, datos}) => {
                    if (estado) {
                        successAlerta('Éxito', mensaje).then(() =>
                            this.filtrarUsuarios()
                        );
                    } else {
                        errorAlerta('Error', mensaje).then();
                    }
                });
        }
    }

    reporteUsuarios(){
        this.UsuariosService$.reporteUsuarios()
            .pipe(
                   finalize(() => {
                    this.loading = false;
                })
            )
            .subscribe((response: Blob) => {
                const url = window.URL.createObjectURL(response);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Reporte Usuarios.xlsx`;
                a.click();
                window.URL.revokeObjectURL(url);
            });
      }


}
