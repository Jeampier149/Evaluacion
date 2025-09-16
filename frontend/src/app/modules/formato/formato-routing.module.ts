import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {MenuComponent} from "@modules/configuracion/pages/menu/menu.component";
import { FormatoPComponent } from './pages/formato-p/formato-p.component';
import { FormatoCabComponent } from './pages/formato-cab/formato-cab.component';
import { FormatoFormatoCriterioComponent } from './pages/formato-formato-criterio/formato-formato-criterio.component';



const routes: Routes = [
    {path: 'menu', component: MenuComponent, title: 'Menu | SIEVAL'},
    {path: 'formato_p', component: FormatoPComponent, title: 'formato | SIEVAL'},
    {path: 'formato_cab', component: FormatoCabComponent, title: 'formato Cab | SIEVAL'},
    {path: 'formato_criterio', component: FormatoFormatoCriterioComponent, title: 'formato Criterio| SIEVAL'}
   

];
@NgModule({
    imports: [RouterModule.forChild(routes)]
})
export class FormatoRoutingModule {
}
