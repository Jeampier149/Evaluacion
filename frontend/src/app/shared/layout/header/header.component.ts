import {Component, EventEmitter, Output} from '@angular/core';
import {LoginService} from "@services/login.service";
import {Store} from "@ngrx/store";
import {Router, RouterLink} from "@angular/router";
import {HttpClientModule} from "@angular/common/http";
import {CommonModule} from "@angular/common";
import {Observable} from "rxjs";
import {eliminarSession, Session} from "@store/session.actions";
import {SessionSelectors} from "@store/index.session";
import {SharedModule} from "@shared/shared.module";

@Component({
    selector: 'app-header',
    templateUrl: './header.component.html',
    providers: [],
    imports: [HttpClientModule, CommonModule, RouterLink, SharedModule],
    standalone: true
})
export class HeaderComponent {
    @Output() toggleMenu = new EventEmitter<boolean>();
    session$: Observable<Session> = this.store.select(SessionSelectors.session);
    nombres?: string;
    usuario?: string;
    tema: string = 'light';

    constructor(private LoginServicio: LoginService, private store: Store<any>, private router: Router) {
        this.session$.subscribe(e => {
            this.nombres = e.nombres;
            this.usuario = e.usuario
        });
    }

    logOut(e: Event) {
        e.preventDefault();
        this.LoginServicio.logOut().subscribe(respuesta => {
            if (respuesta.estado) {
                this.store.dispatch(eliminarSession());
                this.router.navigate(['login']).then();
            }
        })
    }

    cambioTema() {
        const body = document.getElementsByTagName("body")[0];
        if (body.hasAttribute("data-bs-theme") && body.getAttribute("data-bs-theme") == "dark") {
            document.body.setAttribute('data-bs-theme', 'light');
            document.body.setAttribute('data-topbar', 'light');
            document.body.setAttribute('data-sidebar', 'light');
            this.tema = 'light';
        } else {
            document.body.setAttribute('data-bs-theme', 'dark');
            document.body.setAttribute('data-topbar', 'dark');
            document.body.setAttribute('data-sidebar', 'dark');
            this.tema = 'dark';
        }
    }

    menuExpand(e: Event): void {
        e.preventDefault();
        this.toggleMenu.emit();
    }

}
