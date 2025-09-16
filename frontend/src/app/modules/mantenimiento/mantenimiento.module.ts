import {NgModule} from '@angular/core';
import {MantenimientoRoutingModule} from './mantenimiento-routing.module';
import {SharedModule} from "@shared/shared.module";
import {BreadcrumbComponent} from "@shared/components/breadcrumb/breadcrumb.component";
import {LoadingComponent} from "@shared/components/loading/loading.component";
import {PaginacionComponent} from "@shared/components/paginacion/paginacion.component";
import {ReactiveFormsModule} from "@angular/forms";
import {UsuarioComponent} from "@modules/mantenimiento/pages/usuarios/usuario.component";
import {ModalUsuarioComponent} from "@modules/mantenimiento/components/modal-usuario/modal-usuario.component";
import {ModalPerfilComponent} from "@modules/mantenimiento/components/modal-perfil/modal-perfil.component";

import { NgSelectModule } from '@ng-select/ng-select';




@NgModule({
    declarations: [
        UsuarioComponent,
        ModalUsuarioComponent,
        ModalPerfilComponent,
 
    ],
    exports: [],
    imports: [
        SharedModule,
        BreadcrumbComponent,
        LoadingComponent,
        PaginacionComponent,
        MantenimientoRoutingModule,
        ReactiveFormsModule,
        NgSelectModule
    ],
    providers: []
})
export class MantenimientoModule {

}
