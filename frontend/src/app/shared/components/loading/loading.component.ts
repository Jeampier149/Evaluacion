import {Component, Input} from '@angular/core';
import {NgClass, NgForOf, NgIf, NgStyle} from "@angular/common";
import {RouterLink} from "@angular/router";


@Component({
    selector: 'app-loading',
    templateUrl: './loading.component.html',
    imports: [
        NgForOf,
        NgClass,
        NgIf,
        RouterLink,
        NgStyle
    ],
    standalone: true
})
export class LoadingComponent {
    @Input() mensaje: string;
    @Input() mostrar: boolean;
    constructor() {
        this.mensaje = 'Cargando...';
        this.mostrar = false;
    }

    protected readonly blur = blur;
}
