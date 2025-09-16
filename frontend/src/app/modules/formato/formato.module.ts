import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormatoCabComponent } from './pages/formato-cab/formato-cab.component';
import { FormatoPComponent } from './pages/formato-p/formato-p.component';
import { FormatoFormatoCriterioComponent } from './pages/formato-formato-criterio/formato-formato-criterio.component';
import { FormatoRoutingModule } from './formato-routing.module';
import { SharedModule } from '@shared/shared.module';
import { BreadcrumbComponent } from '@shared/components/breadcrumb/breadcrumb.component';
import { TablaComponent } from '@shared/components/tabla/tabla.component';
import { LoadingComponent } from '@shared/components/loading/loading.component';
import { PaginacionComponent } from '@shared/components/paginacion/paginacion.component';
import { NgSelectModule } from '@ng-select/ng-select';
import { ModalFormatoPComponent } from './components/modal-formato-p/modal-formato-p.component';
import { ModalFormatoCabComponent } from './components/modal-formato-cab/modal-formato-cab.component';



@NgModule({
  declarations: [
    FormatoCabComponent,
    FormatoPComponent,
    FormatoFormatoCriterioComponent,
    ModalFormatoPComponent,
    ModalFormatoCabComponent
  ],
   imports: [
                FormatoRoutingModule,
                SharedModule,
                BreadcrumbComponent,
                TablaComponent,
                LoadingComponent,
                PaginacionComponent,
                  NgSelectModule
    ]
})
export class FormatoModule { }
