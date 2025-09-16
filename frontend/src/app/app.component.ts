import {Component} from '@angular/core';
import 'bootstrap';
import * as Waves from 'node-waves'

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent {
    title = 'Sighos';

    constructor() {

    }

    ngOnInit(){
        Waves.init();
    }

}
