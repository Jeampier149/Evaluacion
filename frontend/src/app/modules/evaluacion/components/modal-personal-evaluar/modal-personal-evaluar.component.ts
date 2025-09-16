import { Component, ViewChild } from '@angular/core';
import { Modal } from 'bootstrap';
import { finalize } from 'rxjs';
import { errorAlerta, successAlerta } from '@shared/utils';
import { PeriodoService } from '@services/evaluacion/periodo.service';
import { AnimationStyleMetadata } from '@angular/animations';
import { PersonalEvaluarService } from '@services/evaluacion/personal-evaluar.service';
@Component({
  selector: 'app-modal-personal-evaluar',
  templateUrl: './modal-personal-evaluar.component.html',
  styleUrl: './modal-personal-evaluar.component.scss'
})
export class ModalPersonalEvaluarComponent {
 @ViewChild('modalEmpleadoEvaluarNuevo') modalEl!: any;
    modal: any;
    resolve: any;
    reject: any;
    idPeriodo: string = '';
    longitud: number = 15;
    pagina: number = 1;
    tipo: number = 1; //
    loading: boolean = false;
    filtros = {
        periodo:'',
        apellidos: '',
        nombre:'',
        categoria: '',
        servicio: '',
        cargo: '',
    };
    data:any=[]
     empleadosSeleccionados: Set<number> = new Set();
    todosSeleccionados: boolean = false;
    datos:any=[]
    constructor(private EmpleadoEvaluar$:PersonalEvaluarService) {}

    ngAfterViewInit() {
        this.modal = new Modal(this.modalEl.nativeElement, {
            backdrop: 'static',
            keyboard: false,
        });
    }
   cambioPagina(pagina: number) {
        this.pagina = pagina;
        this. listarEmpleadosNuevos();
    }

    openModal(tipo: number,periodo:any): Promise<boolean> {
        this.modal.show();
        this.tipo = tipo;
        this.filtros.periodo=periodo
        this.listarEmpleadosNuevos()
        return new Promise((resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;
        });
    }

    closeModal() {
        this.modal.hide();
        this.resolve(false);
        this.resetModal();
    }

    listarEmpleadosNuevos() {
        this.loading = true;
        this.EmpleadoEvaluar$.listarEmpleadosNuevos(this.filtros)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.data = datos;
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }

    
    resetModal() {
        this.tipo = 1;
        this.filtros = {
        periodo:'',
        apellidos: '',
        nombre:'',
        categoria: '',
        servicio: '',
        cargo: '',
        };
    }
    filtrarEmpleado() {
        this.pagina = 1;
        this.listarEmpleadosNuevos();
    }
    limpiarCampos() {
 this.filtros = {
         periodo:'',
        apellidos: '',
        nombre:'',
        categoria: '',
        servicio: '',
        cargo: '',
        };
 this.listarEmpleadosNuevos()
    }

    ngOnDestroy() {
        this.modal.dispose();
    }

    estaSeleccionado(id: number): boolean {
    return this.empleadosSeleccionados.has(id);
  }

  
  // Alternar selección de un empleado individual
  toggleSeleccionEmpleado(id: number): void {
    if (this.estaSeleccionado(id)) {
      this.empleadosSeleccionados.delete(id);
    } else {
      this.empleadosSeleccionados.add(id);
    }
    this.actualizarEstadoSeleccionTodos();
  }

  // Alternar selección de todos los empleados
  toggleSeleccionTodos(){
    if (this.todosSeleccionados) {
      this.limpiarSeleccion();
    } else {
      this.seleccionarTodos();
    }
  }

  // Seleccionar todos los empleados
  seleccionarTodos() {
    this.empleadosSeleccionados.clear();
    this.datos.forEach((item:any) => {
      this.empleadosSeleccionados.add(item.id);
    });
    this.todosSeleccionados = true;
  }

  // Deseleccionar todos los empleados
  limpiarSeleccion() {
    this.empleadosSeleccionados.clear();
    this.todosSeleccionados = false;
  }

  // Actualizar el estado de "seleccionar todos"
  actualizarEstadoSeleccionTodos(){
    this.todosSeleccionados = this.datos.length > 0 && 
                             this.empleadosSeleccionados.size === this.datos.length;
  }
}
