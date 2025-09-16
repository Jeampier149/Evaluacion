import {Component, EventEmitter, Input, Output} from '@angular/core';
import {NgClass, NgForOf} from "@angular/common";
import {RouterLink} from "@angular/router";
import {Select2Module} from "ng-select2-component";

@Component({
    selector: 'app-paginado',
    templateUrl: './paginacion.component.html',
    imports: [
        NgForOf,
        RouterLink,
        NgClass,
        Select2Module
    ],
    standalone: true
})
export class PaginacionComponent<t> {
    @Input({required: true}) datos?: t[];
    @Input() longitud: number;
    @Output() longitudChange = new EventEmitter<number>();
    @Input() pagina: number;
    @Input() longitudes: number[] = [];
    @Input() ver: boolean = false;
    @Output() cambioPagina = new EventEmitter<number>();
    @Output() cambioVer = new EventEmitter<undefined>();

    defaultLongitudes = [15, 30, 50, 100];

    constructor() {
        this.pagina = 1;
        this.longitud = 0;
        this.datos = [];
    }


    hasSiguiente(): boolean {
 
        return !(this.datos!.length < this.longitud);
    }

    hasAtras(): boolean {
        return this.pagina !== 1;
    }

    irSiguiente(e: Event) {
        e.preventDefault();
        this.pagina++;
        this.cambioPagina.emit(this.pagina);
    }

    irAnterior(e: Event) {
        e.preventDefault();
        this.pagina--;
        this.cambioPagina.emit(this.pagina);
    }

    cambiarCantidad(){
        this.longitudChange.emit(this.longitud);
        this.cambioVer.emit();
    }

}
