import {Component, Input} from '@angular/core';
import {Store} from "@ngrx/store";
import {Session} from "@store/session.actions";
import {Observable} from "rxjs";
import {SessionSelectors} from "@store/index.session";
import {Router, RouterLink} from "@angular/router";
import {NgForOf, NgIf} from "@angular/common";
import MetisMenu from "metismenujs";
import 'boxicons'

@Component({
    selector: 'app-sidebar',
    templateUrl: './sidebar.component.html',
    imports: [RouterLink, NgForOf, NgIf],
    standalone: true
})
export class SidebarComponent {
    @Input() isOpen?: boolean;
    session$: Observable<Session> = this.store.select(SessionSelectors.session);
    nombre?: string = '';
    perfil?: string = '';
    menus?: [];
    base?: never[];

    constructor(public store: Store<Session>, private router: Router) {
        this.session$.subscribe(e => {
            this.nombre = e.nombres;
            this.perfil = e.perfil;
            this.menus = e.menu;
        });
        this.base = this.menus?.filter(e => e['PADRE'] === null);
    }

    ngAfterViewInit() {
        new MetisMenu('#side-menu');
    }

    validarHijos(codigo: string) {
        let hijos = this.menus?.filter(e => parseInt(e['PADRE']) === parseInt(codigo));
        if (hijos === undefined) {
            return false
        } else {
            return hijos.length !== 0
        }
    }

    obtenerHijos(codigo: string) {
        let hijos = this.menus?.filter(e => parseInt(e['PADRE']) === parseInt(codigo));
        if (hijos === undefined) {
            return []
        } else {
            return hijos;
        }
    }

    toggleMenu(): void {
        let currentSIdebarSize = document.body.getAttribute('data-sidebar-size');
        document.getElementsByTagName("body")[0].classList.toggle('sidebar-enable');
        if (window.innerWidth >= 992) {
            if (currentSIdebarSize == null) {
                (document.body.getAttribute('data-sidebar-size') == null || document.body.getAttribute('data-sidebar-size') == "lg") ? document.body.setAttribute('data-sidebar-size', 'sm') : document.body.setAttribute('data-sidebar-size', 'lg')
            } else if (currentSIdebarSize == "md") {
                (document.body.getAttribute('data-sidebar-size') == "md") ? document.body.setAttribute('data-sidebar-size', 'sm') : document.body.setAttribute('data-sidebar-size', 'md')
            } else {
                (document.body.getAttribute('data-sidebar-size') == "sm") ? document.body.setAttribute('data-sidebar-size', 'lg') : document.body.setAttribute('data-sidebar-size', 'sm')
            }
        }
    }

    irARuta(e: Event, ruta: string) {
        e.preventDefault();
        this.router.navigate([ruta]).then();
    }


}
