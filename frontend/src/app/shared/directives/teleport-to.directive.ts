import {Directive, Inject, Input, TemplateRef, ViewContainerRef} from '@angular/core';
import {DOCUMENT} from "@angular/common";

@Directive({
    selector: '[teleportTo]'
})
export class TeleportToDirective {
    @Input("teleportTo") selector: any | undefined;
    private host: any;
    viewRef : any;

    constructor(
        private tpl: TemplateRef<any>,
        private vcr: ViewContainerRef,
        @Inject(DOCUMENT) private document: Document
    ) {
    }

    ngOnInit() {
        this.viewRef = this.vcr.createEmbeddedView(this.tpl);
        if (this.selector) {
            this.host = this.document.getElementsByTagName(this.selector)[0];
            this.viewRef.rootNodes.forEach((node: any) => this.host.appendChild(node));
        }
    }

    ngOnDestroy() {
        // this.host.innerHTML = "";
        this.viewRef.destroy();
    }
}
