import {Component, ElementRef, ViewChild} from '@angular/core';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
// import * as bootstrap from 'bootstrap';

@Component({
    selector: 'app-dashboard',
    templateUrl: './dashboard.component.html',
    styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent {
    rutas: rutaBreadCrumb[] = [{nombre: 'Inicio'}];
    // carouser: any = '';
    // @ViewChild('carrousel') carrousel!: ElementRef;

    constructor() {
    }

    ngAfterViewInit() {
        /*this.carouser = new bootstrap.Carousel(this.carrousel.nativeElement, {
            keyboard: true,
            pause: "hover",
            interval: 2500,
            ride: true
        });*/
    }

    ngOnDestroy() {
        // this.carouser.dispose();
    }

}