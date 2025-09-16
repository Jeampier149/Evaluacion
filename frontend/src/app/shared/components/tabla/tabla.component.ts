import {Component, Input} from '@angular/core';
import {NgClass, NgForOf, NgIf, NgTemplateOutlet} from "@angular/common";
import {RouterLink} from "@angular/router";

export interface CabeceraTabla{
    nombre: string,
    clase?: string,
    estilo?: string
}


@Component({
    selector: 'app-tabla',
    templateUrl: './tabla.component.html',
    imports: [
        NgForOf,
        NgTemplateOutlet
    ],
    standalone: true
})
export class TablaComponent {
    @Input() template : any;
    @Input() cabeceras : CabeceraTabla[];
    @Input() datos: any[];
    constructor() {
        this.cabeceras = [];
        this.datos = [];
    }

}
