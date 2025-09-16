import Swal, {SweetAlertOptions, SweetAlertResult} from "sweetalert2";

export async function errorAlerta(titulo: string, mensaje: string, html?: string, opciones?: SweetAlertOptions) : Promise<SweetAlertResult> {
    return await Swal.fire({
        icon: 'error',
        title: titulo,
        text: mensaje,
        html: html,
        confirmButtonText: 'Cerrar',
        showCloseButton: false,
        ...opciones
    })
}

export async function warningAlerta(titulo: string, mensaje: string, tiempo = 3000, opciones?: SweetAlertOptions): Promise<SweetAlertResult> {
    return await Swal.fire({
        icon: 'warning',
        title: titulo,
        text: mensaje,
        timer: tiempo,
        showCloseButton: false,
        timerProgressBar: true,
        showConfirmButton: false,
        ...opciones
    })
}

export async function successAlerta(titulo: string, mensaje: string, tiempo = 3000) {
    await Swal.fire({
        icon: 'success',
        title: titulo,
        text: mensaje,
        timer: tiempo,
        timerProgressBar: true,
        showConfirmButton: false
    })
}
export async function errorAlertaValidacion(titulo: string, errores: string) {
    await Swal.fire({
        icon: 'error',
        title: titulo,
        html: formatearMensaje(errores),
        confirmButtonText: 'Cerrar',
        showCloseButton: false
    })
}
export async function infoAlerta(titulo: string, mensaje: string, html: string, opciones?: SweetAlertOptions): Promise<SweetAlertResult> {
    let modal = document.getElementsByClassName('modal show')[0];
    let target = (modal as HTMLElement) ?? 'body';
    return await Swal.fire({
        icon: 'info',
        title: titulo,
        text: mensaje,
        html: html,
        target: target,
        ...opciones
    })
}
function formatearMensaje(mensaje: string): string {
    let html = '';

    const errores = typeof mensaje === 'object' ? mensaje : JSON.parse(mensaje);
    for (const error in errores) {
        const campo = errores[error];
        campo.forEach((c: string) => {
            html += `<li style='margin:0;text-align:left;'>${c}</li>`;
        });
    }

    return html.slice(0, -5);
}
