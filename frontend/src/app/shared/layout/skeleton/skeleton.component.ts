import {Component, ViewChild} from '@angular/core';
import {SharedModule} from "@shared/shared.module";
import {RouterOutlet} from "@angular/router";
import {HeaderComponent} from "@layout/header/header.component";
import {SidebarComponent} from "@layout/sidebar/sidebar.component";
import {FooterComponent} from "@layout/footer/footer.component";
import {ModalLoginComponent} from "@shared/components/modal-login/modal-login.component";
import {LoginService} from "@services/login.service";

@Component({
    standalone: true,
    templateUrl: './skeleton.component.html',
    imports: [
        SharedModule,
        RouterOutlet,
        HeaderComponent,
        SidebarComponent,
        FooterComponent,
        ModalLoginComponent,
    ],
})
export class SkeletonComponent {
    @ViewChild(SidebarComponent) private sidebar!: SidebarComponent;
    @ViewChild(ModalLoginComponent) modalLogin?: ModalLoginComponent;

    loopVal: any;

    constructor(private loginService$: LoginService) {
    }

    ngAfterViewInit(){
        this.validarSession();
    }

    validarSession() {
        this.loginService$.validarSesion().subscribe((e) => {
            if (e === 1) {
                this.modalLogin?.openModal();
            }
        })
        this.loginService$.requireLogin();

        this.loopVal = setInterval(() => {
            this.loginService$.requireLogin();
        }, 600000) // Cada 10 min
    }

    handleToggle() {
        this.sidebar.toggleMenu();
    }

    ngOnDestroy() {
        clearInterval(this.loopVal);
    }

}
