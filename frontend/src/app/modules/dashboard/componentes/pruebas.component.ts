import {Component} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {CabeceraTabla} from "@shared/components/tabla/tabla.component";

@Component({
    selector: 'app-pruebas',
    templateUrl: './pruebas.component.html'
})
export class PruebasComponent {
    rutas: rutaBreadCrumb[] = [{nombre: 'Inicio', ruta: '/inicio'}, {nombre: 'Componentes'}]

    cabeceras: CabeceraTabla[] = [
        {nombre: 'Acciones', clase: 'w-20'},
        {nombre: 'T. Doc'},
        {nombre: 'Documento'},
        {nombre: 'Apellidos y Nombres'},
        {nombre: 'Filiacion'},
        {nombre: 'Estado'}
    ]
    datos: any[] = [{
        tipoDocumento: 'DNI',
        documento: '7231346',
        apellidos: 'PRUEBAS',
        filiacion: '????'
    }, {
        tipoDocumento: 'DNI',
        documento: '4923222',
        apellidos: 'PRUEBAS2',
        filiacion: '????2'
    },
    {
        tipoDocumento: 'DNI',
        documento: '4923222',
        apellidos: 'PRUEBAS2',
        filiacion: '????2'
    },
    {
        tipoDocumento: 'DNI',
        documento: '4923222',
        apellidos: 'PRUEBAS2',
        filiacion: '????2'
    },
    {
        tipoDocumento: 'DNI',
        documento: '4923222',
        apellidos: 'PRUEBAS2',
        filiacion: '????2'
    }]

    longitud: number = 1;

    constructor() {
    }

    enConsole(e: any) {
        console.log(e);
    }

}
