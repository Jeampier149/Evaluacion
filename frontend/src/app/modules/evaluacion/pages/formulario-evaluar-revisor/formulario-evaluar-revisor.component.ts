import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { EvaluarService } from '@services/evaluacion/evaluar.service';
import { finalize } from 'rxjs/operators';
import { errorAlerta, successAlerta } from '@shared/utils';


@Component({
  selector: 'app-formulario-evaluar-revisor',
  templateUrl: './formulario-evaluar-revisor.component.html',
  styleUrl: './formulario-evaluar-revisor.component.scss'
})
export class FormularioEvaluarRevisorComponent {
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
        public EvaluarService$: EvaluarService,
        private cdRef: ChangeDetectorRef
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
        console.log(data)
        this.loading = true;
        this.EvaluarService$.guardarEvaluarRevisor(data)
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
