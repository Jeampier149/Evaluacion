import {Directive, ElementRef, Input} from '@angular/core';
import {Tooltip} from "bootstrap";
import PopoverPlacement = Tooltip.PopoverPlacement;

@Directive({
    selector: '[bsTooltip]'
})
export class BsTooltipDirective {
    @Input("bsTooltip") title: any | undefined;
    @Input("placement") placement: PopoverPlacement = 'top';
    private tooltip: any;

    constructor(private elementRef: ElementRef) {
    }

    ngAfterViewInit() {
        const domElement: HTMLElement = this.elementRef.nativeElement;
        this.tooltip = new Tooltip(domElement, {
            title: this.title,
            placement: this.placement,
            trigger: 'hover'
        });
    }

    ngOnDestroy(): void {
        this.tooltip.dispose();
    }
}
