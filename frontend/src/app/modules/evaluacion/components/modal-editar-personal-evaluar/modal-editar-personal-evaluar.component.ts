import { Component, ViewChild } from '@angular/core';
import { Modal } from 'bootstrap';
import { finalize } from 'rxjs';
import { errorAlerta, successAlerta } from '@shared/utils';
import { PeriodoService } from '@services/evaluacion/periodo.service';
import { AnimationStyleMetadata } from '@angular/animations';
import { PersonalEvaluarService } from '@services/evaluacion/personal-evaluar.service';

@Component({
    selector: 'app-modal-editar-personal-evaluar',
    templateUrl: './modal-editar-personal-evaluar.component.html',
    styleUrl: './modal-editar-personal-evaluar.component.scss',
})
export class ModalEditarPersonalEvaluarComponent {
    @ViewChild('modalEditarPersonalEvaluar') modalEl!: any;
    modal: any;
    resolve: any;
    reject: any;
    idPeriodo: string = '';
    cc_empleado: any;
    tipo: number = 1; // 1 Nuevo, 2 Editar
    loading: boolean = false;
    unidad: any = [];
    servicios: any = [];
    categoria: any = [];
    condicionLaboral: any = [];
    cargos: any = [];
    niveles: any = [];
    personal: any = [];
    estados: any = [];
    dataTotal: any = [];

    formulario = {
        idUnidad: '',
        idServicio: '',
        idCategoria: '',
        idCondicion: '',
        idCargo: '',
        idNivel: '',
        evaluador: '',
        factor_asistencia: '',
        puntaje_asistencia: '',
        revisor: '',
        idEstado:''
    };
    nombre = '';
    constructor(private PersonalEvaluarService$: PersonalEvaluarService) {
      
    }

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false,
        }); 
     
    }

     openModal(tipo: number, idPerfil: string, id: any){
        this.modal.show();
        this.tipo = tipo;
        this.cc_empleado = id;  
        this.idPeriodo = idPerfil!;
         this.obtenerSelects();
         this.obtenerDatos(); 
 
    }

    closeModal() {
        this.modal.hide();
        this.resolve(false);
        this.resetModal();  
    }
obtenerSelects(): Promise<void> {
    return new Promise((resolve) => {
        this.loading = true;
        this.PersonalEvaluarService$.obtenerSelects()
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.unidad = datos['unidades'];
                    this.servicios = datos['servicios'];
                    this.categoria = datos['categorias'];
                    this.niveles = datos['niveles'];
                    this.condicionLaboral = datos['condicion_laboral'];
                    this.cargos = datos['cargos'];
                    this.personal = datos['empleado'];
                    this.estados = datos['estados'];

                } else {
                    errorAlerta('Error!', mensaje).then();
                }
                resolve();
            });
    });
}

obtenerDatos() {
    this.loading = true;
    this.PersonalEvaluarService$.obtenerEmpleadoEval(
        this.cc_empleado,
        this.idPeriodo
    )
        .pipe(finalize(() => (this.loading = false)))
        .subscribe(({ estado, mensaje, datos }) => {
            if (estado) {
                console.log('DATA DEL EMPLEADO:', datos);
                
           if (datos && datos.length > 0) {
           const empleado = datos[0];
            
            // Mapear los datos al formulario
            this.formulario.idUnidad = empleado.unidad || '';
            this.formulario.idServicio = empleado.idServicio || '';
            this.formulario.idCategoria = empleado.idCategoria || '';
            this.formulario.idCondicion = empleado.idCondicion || '';
            this.formulario.idCargo = empleado.idCargo || '';
            this.formulario.idNivel = empleado.idNivel || '';
            this.formulario.evaluador = empleado.evaluador || '';
            this.formulario.factor_asistencia = empleado.factor_asistencia || '';
            this.formulario.puntaje_asistencia = empleado.puntaje_asistencia || '';
            this.formulario.revisor = empleado.revisor || '';
            this.formulario.idEstado = empleado.idEstado || '';
            this.formulario.idNivel = empleado.idNivel|| '';     
 
          
                }
            } else {
                errorAlerta('Error!', mensaje).then();
            }
        });
}

    guardarPersonalEval() {
        this.loading = true;
        let periodo=this.idPeriodo
        let cc_empleado=this.cc_empleado
        let params: any = {
            ...this.formulario,
        };

        this.PersonalEvaluarService$.editarEmpleadoEval(params,periodo,cc_empleado)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje }) => {
                if (estado) {
                    successAlerta('Éxito!', mensaje).then(() => {
                        this.modal.hide();
                        this.resolve(true);
                        this.resetModal();
                    });
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });

        // this.obtenerPerfil();
    }

    resetModal() {
        this.tipo = 1;
        this.idPeriodo = '';
        this.formulario = {
         idUnidad: '',
        idServicio: '',
        idCategoria: '',
        idCondicion: '',
        idCargo: '',
        idNivel: '',
        evaluador: '',
        factor_asistencia: '',
        puntaje_asistencia: '',
        revisor: '',
        idEstado:''
        };
    }

    limpiarCampos() {}

    ngOnDestroy() {
        this.modal.dispose();
    }
}
