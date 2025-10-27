import { ChangeDetectorRef, Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { EvaluarService } from '@services/evaluacion/evaluar.service';
import { finalize } from 'rxjs/operators';
import { errorAlerta, successAlerta } from '@shared/utils';
import { MiEvaluacionService } from '@services/evaluacion/mi-evaluacion.service';
import { RefirmaComponent } from '@shared/components/refirma/refirma.component';

@Component({
  selector: 'app-mi-evaluacion',
  templateUrl: './mi-evaluacion.component.html',
  styleUrl: './mi-evaluacion.component.scss'
})
export class MiEvaluacionComponent implements OnInit{
     @ViewChild(RefirmaComponent) refirma!: RefirmaComponent;
    idEmpleado= localStorage.getItem('cc_empleado');
    periodo: string = '';
    categoria= localStorage.getItem('categoria');
    loading: boolean = true;
    factores: any[] = [];
    data: any = null;
    longitud: number = 15;
    pagina: number = 1;
    empleados: any[] = [];
    periodos: any[] = [];
    valor:any=''
    argumentos: any = '';
    archivo:any=''

    constructor(
        public EvaluarService$: EvaluarService,
        public MiEvaluacion$:MiEvaluacionService
    ) {
   this.listarPeriodosMievaluacion()
    }

    ngOnInit(): void {
        this.listarEvaluacion();
    
    }

    listarEvaluacion() {
        if (!this.idEmpleado ) {
            errorAlerta(
                'Error!',
                'Faltan parámetros necesarios para cargar la evaluación'
            ).then();
            return;
        }

        this.loading = true;
        this.EvaluarService$.listarEvalFormF(
            this.idEmpleado,
            this.periodo,
            this.categoria
        )
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.data = datos || {};
                    this.archivo=datos.info_evaluacion[0].archivo

                    // Inicializar propiedades para evaluador y revisor
                    if (this.data.factores) {
                        this.data.factores.forEach((factor: any) => {
                            // Inicializar arrays para evaluador y revisor
                            factor.valoresEvaluador = [];
                            factor.valoresRevisor = [];
                            
                            // Inicializar IDs de criterios seleccionados
                            factor.id_factor_criterio = factor.id_factor_criterio || null;
                            factor.id_factor_criterio_revisor = factor.id_factor_criterio_revisor || null;

                            // Procesar puntaje del evaluador
                            if (factor.puntaje_asig !== null && factor.puntaje_asig !== undefined) {
                                if (this.categoria == '05') {
                                    factor.puntaje_asig = Math.round(parseFloat(factor.puntaje_asig));
                                } else {
                                    factor.puntaje_asig = parseFloat(factor.puntaje_asig);
                                }
                            }

                            // Procesar puntaje del revisor
                            if (factor.puntaje_asig_revisor !== null && factor.puntaje_asig_revisor !== undefined) {
                                if (this.categoria == '05') {
                                    factor.puntaje_asig_revisor = Math.round(parseFloat(factor.puntaje_asig_revisor));
                                } else {
                                    factor.puntaje_asig_revisor = parseFloat(factor.puntaje_asig_revisor);
                                }
                            }

                            // Si ya hay criterios seleccionados, generar los valores correspondientes
                            if (this.categoria == '05') {
                                this.inicializarCriteriosSeleccionados(factor);
                            }
                        });
                    }
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }

    inicializarCriteriosSeleccionados(factor: any) {
        // Para evaluador
        if (factor.id_factor_criterio_evaluador && factor.criterios) {
            const criterioEvaluador = factor.criterios.find(
                (c: any) => c.id_factor_criterio === factor.id_factor_criterio_evaluador
            );
            if (criterioEvaluador) {
                this.asignarPuntajeCas(
                    criterioEvaluador.desde,
                    criterioEvaluador.hasta,
                    factor,
                    criterioEvaluador.id_factor_criterio,
                    'evaluador'
                );
            }
        }

        // Para revisor
        if (factor.id_factor_criterio_revisor && factor.criterios) {
            const criterioRevisor = factor.criterios.find(
                (c: any) => c.id_factor_criterio === factor.id_factor_criterio_revisor
            );
            if (criterioRevisor) {
                this.asignarPuntajeCas(
                    criterioRevisor.desde,
                    criterioRevisor.hasta,
                    factor,
                    criterioRevisor.id_factor_criterio,
                    'revisor'
                );
            }
        }
    }

    asignarPuntaje(factor: any, puntaje: any, idCriterio: any, hasta: any, tipo: 'evaluador' | 'revisor') {
        if (factor && puntaje !== undefined && puntaje !== null) {
            if (tipo === 'evaluador') {
                factor.puntaje_asig = parseFloat(puntaje);
                factor.id_factor_criterio_evaluador = idCriterio;
            } else {
                factor.puntaje_asig_revisor = parseFloat(puntaje);
                factor.id_factor_criterio_revisor = idCriterio;
            }
        }
    }

    asignarPuntajeCas(desde: any, hasta: any, factor: any, idCriterio: any, tipo: 'evaluador' | 'revisor') {
        const valores = this.generarRango(desde, hasta);
        
        if (tipo === 'evaluador') {
            factor.valoresEvaluador = valores;
            factor.id_factor_criterio_evaluador = idCriterio;
            
            // Establecer el primer valor como predeterminado
            if (valores.length > 0 && (!factor.puntaje_asig || factor.puntaje_asig === 0)) {
                factor.puntaje_asig = valores[0];
            }
        } else {
            factor.valoresRevisor = valores;
            factor.id_factor_criterio_revisor = idCriterio;
            
            // Establecer el primer valor como predeterminado
            if (valores.length > 0 && (!factor.puntaje_asig_revisor || factor.puntaje_asig_revisor === 0)) {
                factor.puntaje_asig_revisor = valores[0];
            }
        }
    }

    actualizarPuntajeDesdeSelect(factor: any, tipo: 'evaluador' | 'revisor') {
        // Cuando se cambia el select, actualizar el criterio seleccionado
        if (tipo === 'evaluador' && factor.puntaje_asig) {
            // Buscar el criterio que corresponde al puntaje seleccionado
            const criterio = factor.criterios.find((c: any) => {
                const desde = parseFloat(c.desde);
                const hasta = parseFloat(c.hasta);
                const puntaje = parseFloat(factor.puntaje_asig);
                return puntaje >= desde && puntaje <= hasta;
            });
            
            if (criterio) {
                factor.id_factor_criterio_evaluador = criterio.id_factor_criterio;
            }
        } else if (tipo === 'revisor' && factor.puntaje_asig_revisor) {
            // Buscar el criterio que corresponde al puntaje seleccionado
            const criterio = factor.criterios.find((c: any) => {
                const desde = parseFloat(c.desde);
                const hasta = parseFloat(c.hasta);
                const puntaje = parseFloat(factor.puntaje_asig_revisor);
                return puntaje >= desde && puntaje <= hasta;
            });
            
            if (criterio) {
                factor.id_factor_criterio_revisor = criterio.id_factor_criterio;
            }
        }
    }

    generarRango(desde: any, hasta: any): number[] {
        const start = parseInt(desde);
        const end = parseInt(hasta);
        return Array.from({ length: end - start + 1 }, (_, i) => i + start);
    }

    puntajeTotalEvaluador(): number {
        let total = 0;
        if (this.data && this.data.factores) {
            for (const factor of this.data.factores) {
                total += parseFloat(factor.puntaje_asig) || 0;
            }
        }

        if (this.data) {
            if (!this.data.info_evaluacion) {
                this.data.info_evaluacion = [{}];
            }
            this.data.info_evaluacion[0].ni_puntaje_dcl = total;
        }

        return parseFloat(total.toFixed(2));
    }

    puntajeTotalRevisor(): number {
        let total = 0;
        if (this.data && this.data.factores) {
            for (const factor of this.data.factores) {
                total += parseFloat(factor.puntaje_asig_revisor) || 0;
            }
        }
        return parseFloat(total.toFixed(2));
    }



    guardarConformidad() {
        let data = this.data;
        let periodo=this.periodo

        this.loading = true;
        this.MiEvaluacion$.guardarConformidadEvaluado(data,periodo)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    successAlerta(
                        'Exito!',
                        'Conformidad registrada correctamente'
                    );
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }
   cambiarFiltroPeriodo(){
   this.listarEvaluacion()
    }
    listarPeriodosMievaluacion(){
        let empleado=localStorage.getItem('cc_empleado');
        let categoria=localStorage.getItem('categoria');
        this.loading = true;
        this.MiEvaluacion$.listarPeriodosMievaluacion()
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.periodos = datos!;
                    this.periodo=this.periodos[0].cc_periodo
                    this.listarEvaluacion()
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })
    }
    generarCalificacion(){
        let total=this.data.info_evaluacion[0].ni_puntaje_cap +   this.data.info_evaluacion[0].ni_puntaje_pun+     this.data.info_evaluacion[0].ni_puntaje_asi +this.data.info_evaluacion[0].ni_puntaje_dcl
        console.log(total)
       
        if(this.categoria=='05'){
            if(total>1 && total<=70) {
                this.valor='DEFICIENTE'

            }else if(total>=71 && total<=80){
                 this.valor='REGULAR'
            }else if(total>=81 &&  total<=90){
                  this.valor= 'BUENO'
            }else if(total>=91 && total<=100){
                  this.valor= 'EXCELENTE'
            }else{
                'NO DEFINIDO'
            }

        }else{
            if(total>0 &&  total<=29) {
                  this.valor= 'INFERIOR'
            }else if(total>=30 && total<=59){
                 this.valor='INFERIOR AL PROMEDIO'
            }else if(total>=60 && total<=70){
                  this.valor= 'PROMEDIO'
            }else if(total>=71 && total<=90){
                  this.valor= 'SUPERIOR AL PROMEDIO'
            }else if(total>=91 && total<=100){
                  this.valor= 'SUPERIOR'
            } else{
                'NO DEFINIDO'
            } 
        }
        return  this.valor
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
            this.listarEvaluacion()
        }
     firmar() {
            let empleado=this.idEmpleado
            let periodo=this.periodo;
            let archivo=this.archivo
            this.loading = true;    
             this.MiEvaluacion$.obtenerArgumentosFirma(empleado,periodo,archivo)
                 .pipe(
                     finalize(() => {
                         this.loading = false;
                     })
                 )
                 .subscribe(({ estado, mensaje, datos }: any) => {
                     if (estado) {
                         //@ts-ignore
                         this.argumentos = datos;
                         this.refirma.iniciarFirma(this.argumentos);
                   }
                 });
        }
}
