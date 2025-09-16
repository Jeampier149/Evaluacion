export interface HttpResponseApi<t = any> {
    estado: number | boolean,
    mensaje: string,
    datos?: t
}

import {AbstractControl, FormControl, FormGroup} from "@angular/forms";

export type FormType<T> = {
    [K in keyof T]:  FormControl | FormGroup;
}
