import { Component, ElementRef, ViewChild } from '@angular/core';
import { EmpleadoService } from '@services/datos-generales/empleado.service';
import { EvaluarService } from '@services/evaluacion/evaluar.service';
import { rutaBreadCrumb } from '@shared/components/breadcrumb/breadcrumb.component';
import { errorAlerta, successAlerta } from '@shared/utils';
import { finalize } from 'rxjs';
import Swal from 'sweetalert2';
import { Router } from '@angular/router';
import { RefirmaComponent } from '@shared/components/refirma/refirma.component';

@Component({
    selector: 'app-evaluar',
    templateUrl: './evaluar.component.html',
    styleUrl: './evaluar.component.scss',
})
export class EvaluarComponent {
    //ViewChild(ModalEmpleadoComponent) modalEmpleado!: ModalEmpleadoComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    @ViewChild(RefirmaComponent) refirma!: RefirmaComponent;

    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{ nombre: 'Empleado' }];
    longitud: number = 15;
    pagina: number = 1;
    esMultiple: boolean = false;
    empleados: any = [];
    periodos: any = [];
    resultados: any[] = [];
    esTodos: boolean = false;
    seleccionados: any[] = [];
    filtros = {
        nombre: '',
        categoria: '',
        unidad: '',
        servicio: '',
        cargo: '',
        estado: '',
        periodo: '',
    };

    argumentos: any = '';
    constructor(
        private EvaluarService$: EvaluarService,
        private router: Router
    ) {}
    ngAfterViewInit() {
        this.listarPeriodos();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarEvalEmpleado();
    }

    filtrarEmpleado() {
        this.pagina = 1;
        this.listarEvalEmpleado();
    }

    listarEvalEmpleado() {
        let params: any = {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina,
        };
        this.loading = true;
        this.EvaluarService$.listarEvalEmpleado(params)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.empleados = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }

    listarPeriodos() {
        this.loading = true;
        this.EvaluarService$.listarPeriodos()
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.periodos = datos!
                    this.filtros.periodo=datos[0].id;
                            this.listarEvalEmpleado();
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }
    limpiarEmpleado() {
        this.filtros = {
            nombre: '',
            categoria: '',
            unidad: '',
            servicio: '',
            cargo: '',
            estado: '',
            periodo: '',
        };
        this.listarEvalEmpleado();
    }

    Evaluar(id: any, categoria: any) {
        let periodo = this.filtros.periodo;
        this.router.navigate(['/evaluacion/formulario_evaluar'], {
            state: { id: id, periodo, categoria },
        });
    }
    cancelarFirma() {
        errorAlerta('Error!', 'Se canceló la firma digital.', '', {
            didOpen: () => {
                this.loading = false
            }
        }).then()
    }

    confirmarFirma() {
        this.loading = false
        successAlerta('Éxito!', 'Se guardó el documento firmado.', 1500)
        this.listarEvalEmpleado()
    }
    cambiarFiltroPeriodo() {
        this.empleados = [];
        this.listarEvalEmpleado();
    }

    async generarPdf(idEmpleado: string, categoria: any) {
        let periodo = this.filtros.periodo;
        this.loading = true;
        this.EvaluarService$.generarPdf(idEmpleado, periodo, categoria)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe((response: Blob) => {
                const fileURL = URL.createObjectURL(response);
                const downloadLink = document.createElement('a');
                downloadLink.href = fileURL;
                downloadLink.download = 'reporte evaluacion pruebas';
                document.body.appendChild(downloadLink);
                downloadLink.click();
            });
    }
    async firmar(empleado: any) {
         this.seleccionados.push({
            idEmpleado: empleado.id,
            periodo: this.filtros.periodo,
            categoria: empleado.idCategoria,
        });
        this.firmarLote();
    }

    seleccionarTodos() {

        this.esTodos = !this.esTodos;
         this.seleccionados = [];
        if (this.esTodos) {
           const emp = this.empleados
  .filter((valor: any) => valor.indFirmado_eva != 1)
  .map((valor: any) => {
    return {
      idEmpleado: valor.id,
      periodo: this.filtros.periodo,
      categoria: valor.idCategoria,
    };
  });
this.seleccionados = emp;
            console.log(this.seleccionados)
        } else {
            this.seleccionados = [];
        }
    }

    seleccionarVarios() {
        this.esMultiple = !this.esMultiple;
        this.seleccionados = [];
        if (this.esMultiple) {
            this.resultados.map((resultado, index) => {
                if (resultado.indFirmadoEva != 1) {
                    this.seleccionados.push(index);
                }
            });
        } else {
            this.seleccionados = [];
        }
    }

    seleccionarResultados(empleado: any) {
        this.seleccionados.push({
            idEmpleado: empleado.id,
            periodo: this.filtros.periodo,
            categoria: empleado.idCategoria,
        });
    }
    estaSeleccionado(idEmpleado: string): boolean {
        return this.seleccionados.some((emp) => emp.idEmpleado === idEmpleado);
    }
    firmarLote() {
        if (!this.seleccionados) {
            errorAlerta('Error!', 'Seleccione al menos una evaluacion.').then();
            return;
        }
        let resultados: {}[] = [];
        let periodo=this.filtros.periodo;
        resultados=this.seleccionados
        this.loading = true;

        this.EvaluarService$.obtenerArgumentosFirma(resultados,periodo)
            .pipe(
                finalize(() => {
                    this.loading = false;
                })
            )
            .subscribe(({ estado, mensaje, datos }: any) => {
                this.seleccionados = [];
                if (estado) {
                    //@ts-ignore
                    this.argumentos = datos;
                    this.refirma.iniciarFirma(this.argumentos);
                }
            });
    }
     imprimirFichaEvaluacion(empleado: string, archivo: string) {
        this.loading = true;
        let periodo= this.filtros.periodo

           this.EvaluarService$.imprimirFichaEvaluacion( archivo, periodo).pipe(
                finalize(() => {
                    this.loading = false
                })
            ).subscribe((respuesta: any) => {

                let blob = new Blob([respuesta], {type: 'application/pdf'});
                let url = URL.createObjectURL(blob);
                window.open(url);
            });
        
    }
}
