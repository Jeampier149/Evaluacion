import {NgModule} from '@angular/core';
import {DashboardRoutingModule} from './dashboard-routing.module';
import {DashboardComponent} from '@modules/dashboard/dashboard.component';
import {SharedModule} from "@shared/shared.module";
import {BreadcrumbComponent} from "@shared/components/breadcrumb/breadcrumb.component";
import {PruebasComponent} from "@modules/dashboard/componentes/pruebas.component";
import {LoadingComponent} from "@shared/components/loading/loading.component";
import {TablaComponent} from "@shared/components/tabla/tabla.component";
import {Select2Module} from "ng-select2-component";
import {PaginacionComponent} from "@shared/components/paginacion/paginacion.component";

@NgModule({
    declarations: [
        DashboardComponent,
        PruebasComponent,
    ],
    exports: [],
    imports: [
        SharedModule,
        BreadcrumbComponent,
        DashboardRoutingModule,
        LoadingComponent,
        TablaComponent,
        Select2Module,
        PaginacionComponent,
    ],
    providers: []
})
export class DashboardModule {

}
