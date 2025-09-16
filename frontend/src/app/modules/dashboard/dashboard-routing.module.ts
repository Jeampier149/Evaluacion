import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {DashboardComponent} from "@modules/dashboard/dashboard.component";
import {PruebasComponent} from "@modules/dashboard/componentes/pruebas.component";

const routes: Routes = [{path: '', component: DashboardComponent, title: 'Dashboard | SISP'},{path:'componentes', component:PruebasComponent}];

@NgModule({
    imports: [RouterModule.forChild(routes)]
})
export class DashboardRoutingModule {
}
