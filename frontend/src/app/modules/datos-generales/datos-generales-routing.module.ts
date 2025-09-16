import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {MenuComponent} from "@modules/configuracion/pages/menu/menu.component";
import { CargoComponent } from './Pages/cargo/cargo.component';
import { UnidadOrganicaComponent } from './Pages/unidad-organica/unidad-organica.component';
import { ServiciosComponent } from './Pages/servicios/servicios.component';
import { EmpleadoComponent } from './Pages/empleado/empleado.component';

const routes: Routes = [
    {path: 'menu', component: MenuComponent, title: 'Menu | SIEVAL'},
    {path: 'cargo', component: CargoComponent, title: 'Cargo | SIEVAL'},
    {path: 'unidad_organica', component: UnidadOrganicaComponent, title: 'Unidad Organica | SIEVAL'},
    {path: 'servicios', component: ServiciosComponent, title: 'Servicio| SIEVAL'},
    {path: 'empleados', component: EmpleadoComponent, title: 'Empleado | SIEVAL'}

];
@NgModule({
    imports: [RouterModule.forChild(routes)]
})
export class DatosGeneralRoutingModule {
}
