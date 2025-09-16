import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '@shared/shared.module';
import { BreadcrumbComponent } from '@shared/components/breadcrumb/breadcrumb.component';
import { TablaComponent } from '@shared/components/tabla/tabla.component';
import { LoadingComponent } from '@shared/components/loading/loading.component';
import { PaginacionComponent } from '@shared/components/paginacion/paginacion.component';
import { CargoComponent } from './Pages/cargo/cargo.component';
import { ModalCargoComponent } from './Components/modal-cargo/modal-cargo.component';
import { DatosGeneralRoutingModule } from './datos-generales-routing.module';
import { UnidadOrganicaComponent } from './Pages/unidad-organica/unidad-organica.component';
import { ModalUnidadComponent } from './Components/modal-unidad/modal-unidad.component';
import { ModalServicioComponent } from './Components/modal-servicio/modal-servicio.component';
import { ModalEmpleadoComponent } from './Components/modal-empleado/modal-empleado.component';
import { NgSelectModule } from '@ng-select/ng-select';
import { ServiciosComponent } from './Pages/servicios/servicios.component';
import { EmpleadoComponent } from './Pages/empleado/empleado.component';



@NgModule({
  declarations: [
    CargoComponent,
    UnidadOrganicaComponent,
    ServiciosComponent,
    EmpleadoComponent,
    ModalCargoComponent,
    ModalUnidadComponent,
    ModalServicioComponent,
    ModalEmpleadoComponent
  ],
  imports: [
          DatosGeneralRoutingModule,
          SharedModule,
          BreadcrumbComponent,
          TablaComponent,
          LoadingComponent,
          PaginacionComponent,
             NgSelectModule
      ]
})
export class DatosGeneralesModule { }
