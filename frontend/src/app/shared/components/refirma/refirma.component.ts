import {Component, EventEmitter, HostListener, Output} from '@angular/core';
import {SharedModule} from "@shared/shared.module";

@Component({
    selector: 'app-refirma',
    templateUrl: './refirma.component.html',
    imports: [
        SharedModule
    ],
    standalone: true
})
export class RefirmaComponent {
    @Output('onOk') firmaCorrecta = new EventEmitter();
    @Output('onCancel') firmaCancelada = new EventEmitter();
    arg: string = '';
    @HostListener('window:getArguments', ['$event'])
    obtenerArgumentos = (event: any) => {
        const type = event.detail;
        if (type === 'W') {
            // @ts-ignore
            dispatchEventClient('sendArguments', this.arg);
        }
    }

    @HostListener('window:invokerOk', [])
    invokerOk() {
        console.log('OK');
        this.firmaCorrecta.emit();
    }

    @HostListener('window:invokerCancel', [])
    invokerCancel() {
        console.log('CANCELADA');
        this.firmaCancelada.emit();
    }

    ngAfterViewInit() {
        this.cargarScript();
    }

    cargarScript() {
        // Validar si ya se encuentra importado
        if (document.getElementById('refirma')) {
            return
        }
        const url = 'https://dsp.reniec.gob.pe/refirma_invoker/resources/js/clientclickonce.js';

        const nodoScript = document.createElement('script');
        nodoScript.id = 'refirma';
        nodoScript.src = url;
        nodoScript.type = 'text/javascript';
        nodoScript.async = true;
        document.getElementsByTagName('head')[0].appendChild(nodoScript);
    }

    /**
     * Inicia la firma
     * @param arg Argumento
     * @param modo (W: Web, L:Local) Defecto: W
     */
    iniciarFirma(arg: string, modo: string = 'W') {
        this.arg = arg;
        // @ts-ignore
        insertFrame();
        // @ts-ignore
        initInvoker(modo);
    }

    ngOnDestroy() {
        //document.getElementsByTagName('head')[0].removeChild(this.nodoScript);
        //console.log(this.nodoScript);
    }
}
