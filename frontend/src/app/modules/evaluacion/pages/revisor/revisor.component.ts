import {Component, ElementRef, ViewChild} from '@angular/core';
import { EmpleadoService } from '@services/datos-generales/empleado.service';
import { EvaluarService } from '@services/evaluacion/evaluar.service';
import {rutaBreadCrumb} from "@shared/components/breadcrumb/breadcrumb.component";
import {errorAlerta, successAlerta} from "@shared/utils";
import {finalize} from "rxjs";
import Swal from "sweetalert2";
import { Router } from '@angular/router';


@Component({
  selector: 'app-revisor',
  templateUrl: './revisor.component.html',
  styleUrl: './revisor.component.scss'
})
export class RevisorComponent {
  @ViewChild('inpFocus') inpFocus!: ElementRef;
    loading: boolean = true;
    rutas: rutaBreadCrumb[] = [{nombre: 'Empleado'}];
    longitud: number = 15;
    pagina: number = 1;
    esMultiple: boolean = false;
    empleados: any = [];
    periodos: any = [];
    resultados: any[] = [];
    seleccionados: number[] = [];
    filtros = {
        nombre: '',
        categoria: '',
        unidad:'',
        servicio:'',
        cargo:'',
        estado:'',
        periodo:'2024-03235'
        
    };

   constructor(private EmpleadoService$: EvaluarService,private router:Router) {}
 ngAfterViewInit() {
  this.listarPeriodos()
        this. listarEvalEmpleado();
        this.inpFocus.nativeElement.focus();
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.  listarEvalEmpleado();
    }

    filtrarEmpleado() {
        this.pagina = 1;
        this. listarEvalEmpleado();
    }

    listarEvalEmpleado() {
        let params: any= {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina
        }
        this.loading = true;
        this.EmpleadoService$. listarEvalEmpleadoRevisor(params)
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.empleados = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    } 
     
    listarPeriodos() {
        
        this.loading = true;
        this.EmpleadoService$. listarPeriodos()
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.periodos = datos!;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }
 limpiarEmpleado() {
        this.filtros = {
        nombre: '',
        categoria: '',
        unidad:'',
        servicio:'',
        cargo:'',
        estado:'',
        periodo:''
        };
        this. listarEvalEmpleado();
    }

    Evaluar(id:any,categoria:any) {
        let periodo= this.filtros.periodo
        this.router.navigate(['/evaluacion/formulario_evaluar_revisor'], { state: { id: id,periodo,categoria} });
     }

  

cambiarFiltroPeriodo(){
   this.empleados=[]
   this.listarEvalEmpleado()

}


     async generarPdf(idEmpleado: string,categoria:any) {
          let periodo = this.filtros.periodo;
        this.loading = true;
        this.EmpleadoService$.generarPdf(idEmpleado,periodo,categoria)
            .pipe(finalize(() => this.loading = false))
             .subscribe((response:Blob) => {
                const fileURL = URL.createObjectURL(response);
                const downloadLink = document.createElement('a');
                downloadLink.href = fileURL;
                downloadLink.download ='reporte evaluacion pruebas';
                document.body.appendChild(downloadLink);
                downloadLink.click();
            });
       
     }
     async firmar() {
       
     }
      seleccionarVarios() {
        this.esMultiple = !this.esMultiple;
        if (this.esMultiple) {
            this.resultados.map((resultado, index) => {
                // if (this.filtros.fuente == 1 && resultado.indFirmado != 1) {
                //     this.seleccionados.push(index);
                // }
            })
        } else {
            this.seleccionados = [];
        }
    }

    seleccionarResultados(indice: any) {
        if (this.seleccionados.includes(indice)) {
            const i = this.seleccionados.findIndex(e => e === indice);
            this.seleccionados.splice(i, 1);
        } else {
            this.seleccionados.push(indice);
        }
    }
   

    firmarLote() {
        // if (!this.seleccionados) {
        //     errorAlerta('Error!', 'Seleccione al menos un resultado.').then();
        //     return
        // }

        // let resultados: {}[] = [];
        // this.seleccionados.forEach(e => {
        //     resultados.push({
        //         numSolicitud: this.resultados[e].numSolicitud,
        //         numItem: this.resultados[e].numItem,
        //         fechaSolicitud: this.resultados[e].fechaSolicitud,
        //         codExamen: this.resultados[e].codExamen,
        //         historia: this.resultados[e].historia,
        //     });
        // })

        // this.loading = true;
        // this.mensajeLoading = 'Obteniendo datos de la firma digital...';
        // this.ResultadosExamenesService$.obtenerDatosFirmaMasivaClipper(this.tipoExamen, resultados).pipe(
        //     finalize(() => {
        //         this.loading = false;
        //     })
        // ).subscribe(({estado, mensaje, datos}: any) => {
        //     if (estado) {
        //         this.mensajeLoading = 'Comprimiendo y firmado lote de resultados...';
        //         // @ts-ignore
        //         initInvoker('W');
        //         this.argumentos = datos;
        //     }
        // });
    }
}
