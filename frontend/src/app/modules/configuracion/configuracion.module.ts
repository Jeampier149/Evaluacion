import {NgModule} from '@angular/core';
import {ConfiguracionRoutingModule} from './configuracion-routing.module';
import {SharedModule} from "@shared/shared.module";
import {BreadcrumbComponent} from "@shared/components/breadcrumb/breadcrumb.component";
import {TablaComponent} from "@shared/components/tabla/tabla.component";
import {LoadingComponent} from "@shared/components/loading/loading.component";
import {PaginacionComponent} from "@shared/components/paginacion/paginacion.component";
import {MenuComponent} from "@modules/configuracion/pages/menu/menu.component";
import {ModalMenuComponent} from "@modules/configuracion/components/modal-menu/modal-menu.component";
import {ModalAccionComponent} from "@modules/configuracion/components/modal-accion/modal-accion.component";
import {PerfilComponent} from "@modules/configuracion/pages/perfil/perfil.component";
import {ModalPerfilComponent} from "@modules/configuracion/components/modal-perfil/modal-perfil.component";
import {
    ModalPerfilUsuarioComponent
} from "@modules/configuracion/components/modal-perfil-usuario/modal-perfil-usuario.component";
import {ModalAccesoComponent} from "@modules/configuracion/components/modal-acceso/modal-acceso.component";

@NgModule({
    declarations: [
        MenuComponent,
        PerfilComponent,
        ModalMenuComponent,
        ModalAccionComponent,
        ModalPerfilComponent,
        ModalPerfilUsuarioComponent,
        ModalAccesoComponent
    ],
    exports: [],
    imports: [
        ConfiguracionRoutingModule,
        SharedModule,
        BreadcrumbComponent,
        TablaComponent,
        LoadingComponent,
        PaginacionComponent
    ]
})
export class ConfiguracionModule {

}
