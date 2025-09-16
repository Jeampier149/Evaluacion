import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { EvaluarService } from '@services/evaluacion/evaluar.service';
import { finalize } from 'rxjs/operators';
import { errorAlerta, successAlerta } from '@shared/utils';

@Component({
    selector: 'app-formulario-evaluar',
    templateUrl: './formulario-evaluar.component.html',
    styleUrls: ['./formulario-evaluar.component.scss'],
})
export class FormularioEvaluarComponent implements OnInit {
    idEmpleado: string = '';
    periodo: string = '';
    categoria: string = '';
    loading: boolean = true;
    factores: any[] = [];
    data: any = null;
    longitud: number = 15;
    pagina: number = 1;
    empleados: any[] = [];
    historial: any[] = [];
    factorActual: any = null; // Para rastrear el factor actual

    otros = {
        recomendacion: '',
        capacitacion: '',
        capa: '',
    };

    filtros = {
        periodo: '',
        categoria: '',
        servicio: '',
        cargo: '',
    };

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        public EvaluarService$: EvaluarService

    ) {
        const navigation = this.router.getCurrentNavigation();
        this.idEmpleado = navigation?.extras?.state?.['id'] || '';
        this.periodo = navigation?.extras?.state?.['periodo'] || '';
        this.categoria = navigation?.extras?.state?.['categoria'] || '';
    }

    ngOnInit(): void {
        this.listarEvaluacion();
        this.listarHistorial();
    }

    listarEvaluacion() {
        if (!this.idEmpleado || !this.periodo || !this.categoria) {
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
   

                    // Inicializar la propiedad valores para cada factor
                    if(this.categoria=='05'){
                        if (this.data.factores) {
                        this.data.factores.forEach((factor: any) => {
                            factor.valores = []; // Inicializar array vacío para cada factor
                            if (
                                factor.puntaje_asig !== null &&
                                factor.puntaje_asig !== undefined
                            ) {
                                factor.puntaje_asig = Math.round(
                                    parseFloat(factor.puntaje_asig)
                                );
                            }
                            // Si ya hay un criterio seleccionado, generar los valores correspondientes
                            if (factor.id_factor_criterio && factor.criterios) {
                                const criterioSeleccionado =
                                    factor.criterios.find(
                                        (c: any) =>
                                            c.id_factor_criterio ===
                                            factor.id_factor_criterio
                                    );

                                if (criterioSeleccionado) {
                                    
                                    this.asignarPuntajeCas(
                                        criterioSeleccionado.desde,
                                        criterioSeleccionado.hasta,
                                        factor,
                                        criterioSeleccionado.id_factor_criterio
                                    );

                                    // También asegurar que el puntaje esté asignado
                                    if (
                                        factor.puntaje_asig === null ||
                                        factor.puntaje_asig === undefined
                                    ) {
                                        factor.puntaje_asig = parseFloat(
                                            criterioSeleccionado.desde
                                        );
                                    }
                                }
                            }
                        });
                    }
                    }
              
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }

    asignarPuntaje(factor: any, puntaje: any, idCriterio: any, hasta: any) {
        if (factor && puntaje !== undefined && puntaje !== null) {
            factor.puntaje_asig = parseFloat(puntaje);
        }
        factor.id_factor_criterio = idCriterio;
    }

    asignarPuntajeCas(desde: any, hasta: any, factor: any, idCriterio?: any) {
        factor.valores = this.generarRango(desde, hasta);
         factor.id_factor_criterio = idCriterio;
    }


    generarRango(desde: any, hasta: any): number[] {
        const start = parseInt(desde);
        const end = parseInt(hasta);
        return Array.from({ length: end - start + 1 }, (_, i) => i + start);
    }

    // Resto del código se mantiene igual...
    puntajeTotal(): number {
        let total = 0;

        if (this.data && this.data.factores) {
            for (const factor of this.data.factores) {
                total += parseFloat(factor.puntaje_asig) || 0;
            }
        }

        if (this.data) {
            if (!this.data.info_evaluacion) {
                this.data.info_evaluacion = {};
            }
            this.data.info_evaluacion.ni_puntaje_dcl = total;
        }

        return parseFloat(total.toFixed(2));
    }

    filtrarEmpleado() {
        this.pagina = 1;
        this.listarHistorial();
    }

    listarHistorial() {
        if (!this.idEmpleado) {
            return;
        }

        let params: any = {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina,
        };

        this.loading = true;
        this.EvaluarService$.listarHistorial(params, this.idEmpleado)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.historial = datos || [];
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }

    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarHistorial();
    }

    guardarEvaluacion() {
        let data = this.data;
        this.loading = true;
        this.EvaluarService$.guardarEvaluar(data)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    successAlerta(
                        'Exito!',
                        'Evaluacion registrada correctamente'
                    );
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }

}
