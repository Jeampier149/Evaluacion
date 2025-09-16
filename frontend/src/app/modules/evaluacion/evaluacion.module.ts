import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { EvaluacionRoutingModule } from './evaluacion-routing.module';
import { SharedModule } from '@shared/shared.module';
import { BreadcrumbComponent } from '@shared/components/breadcrumb/breadcrumb.component';
import { TablaComponent } from '@shared/components/tabla/tabla.component';
import { LoadingComponent } from '@shared/components/loading/loading.component';
import { PaginacionComponent } from '@shared/components/paginacion/paginacion.component';
import { NgSelectModule } from '@ng-select/ng-select';
import { PeriodoComponent } from './pages/periodo/periodo.component';
import { ModalPeriodoComponent } from './components/modal-periodo/modal-periodo.component';
import { EvaluarComponent } from './pages/evaluar/evaluar.component';
import { FormularioEvaluarComponent } from './pages/formulario-evaluar/formulario-evaluar.component';
import { PersonalEvaluarComponent } from './pages/personal-evaluar/personal-evaluar.component';
import { ModalPersonalEvaluarComponent } from './components/modal-personal-evaluar/modal-personal-evaluar.component';



@NgModule({
  declarations: [
 PeriodoComponent,
 ModalPeriodoComponent,
 EvaluarComponent,
 FormularioEvaluarComponent,
 PersonalEvaluarComponent,
 ModalPersonalEvaluarComponent

  ],
  imports: [
              EvaluacionRoutingModule,
              SharedModule,
              BreadcrumbComponent,
              TablaComponent,
              LoadingComponent,
              PaginacionComponent,
                NgSelectModule
  ]
})
export class EvaluacionModule { }
