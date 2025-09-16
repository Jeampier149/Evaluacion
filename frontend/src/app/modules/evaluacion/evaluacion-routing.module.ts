import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {MenuComponent} from "@modules/configuracion/pages/menu/menu.component";
import { PeriodoComponent } from './pages/periodo/periodo.component';
import { MiEvaluacionComponent } from './pages/mi-evaluacion/mi-evaluacion.component';
import { EvaluarComponent } from './pages/evaluar/evaluar.component';
import { PersonalEvaluarComponent } from './pages/personal-evaluar/personal-evaluar.component';
import { FormularioEvaluarComponent } from './pages/formulario-evaluar/formulario-evaluar.component';


const routes: Routes = [
    {path: 'menu', component: MenuComponent, title: 'Menu | SIEVAL'},
    {path: 'periodo', component: PeriodoComponent, title: 'Periodo | SIEVAL'},
    {path: 'mi_evaluacion', component: MiEvaluacionComponent, title: 'Mi evaluacion | SIEVAL'},
    {path: 'evaluar', component: EvaluarComponent, title: 'Evaluar| SIEVAL'},
    {path: 'personal_evaluar', component: PersonalEvaluarComponent, title: '´PersonalEvaluar| SIEVAL'},
    {path: 'formulario_evaluar', component: FormularioEvaluarComponent, title: '´FormularioEvaluar| SIEVAL'}

];
@NgModule({
    imports: [RouterModule.forChild(routes)]
})
export class EvaluacionRoutingModule {
}
