import {NgModule} from '@angular/core';
import {LoginRoutingModule} from "./login-routing.module";
import {LoginComponent} from '@modules/login/login.component';
import {SharedModule} from "@shared/shared.module";
import {LoginService} from "@services/login.service";
import {ModalPassComponent} from "@modules/login/components/modal-pass/modal-pass.component";

@NgModule({
    declarations: [
        LoginComponent,
        ModalPassComponent
    ],
    imports: [
        SharedModule,
        LoginRoutingModule,
    ],
    providers:[LoginService]
})
export class LoginModule {}
