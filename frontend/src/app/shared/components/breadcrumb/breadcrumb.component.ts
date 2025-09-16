import {Component, Input} from '@angular/core';
import {NgClass, NgForOf, NgIf} from "@angular/common";
import {RouterLink} from "@angular/router";

export interface rutaBreadCrumb {
    nombre: string,
    ruta?: string,
}

@Component({
    selector: 'app-breadcrumb',
    templateUrl: './breadcrumb.component.html',
    imports: [
        NgForOf,
        NgClass,
        NgIf,
        RouterLink
    ],
    standalone: true
})
export class BreadcrumbComponent {
    @Input({required: true}) titulo!: string;
    @Input({required: true}) rutas!: rutaBreadCrumb[];
}
