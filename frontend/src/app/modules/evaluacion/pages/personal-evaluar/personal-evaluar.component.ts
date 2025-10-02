import { ChangeDetectorRef, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { EvaluarService } from '@services/evaluacion/evaluar.service';
import { finalize } from 'rxjs/operators';
import { errorAlerta, successAlerta } from '@shared/utils';
import { rutaBreadCrumb } from '@shared/components/breadcrumb/breadcrumb.component';
import { PersonalEvaluarService } from '@services/evaluacion/personal-evaluar.service';
import { ModalPersonalEvaluarComponent } from '@modules/evaluacion/components/modal-personal-evaluar/modal-personal-evaluar.component';
import { ModalEditarPersonalEvaluarComponent } from '@modules/evaluacion/components/modal-editar-personal-evaluar/modal-editar-personal-evaluar.component';

@Component({
  selector: 'app-personal-evaluar',
  templateUrl: './personal-evaluar.component.html',
  styleUrl: './personal-evaluar.component.scss'
})
export class PersonalEvaluarComponent {
    @ViewChild(ModalPersonalEvaluarComponent) modalPersonal!: ModalPersonalEvaluarComponent
    @ViewChild(ModalEditarPersonalEvaluarComponent) modalPersonalE!: ModalEditarPersonalEvaluarComponent
    @ViewChild('inpFocus') inpFocus!: ElementRef;
    idEmpleado: string = '';
    categoria: string = '';
    loading: boolean = false;
    factores: any[] = [];
    data: any = null;
    longitud: number = 15;
    pagina: number = 1;
    periodos: any[] = [];
    historial: any[] = [];
    factorActual: any = null; // Para rastrear el factor actual
    rutas: rutaBreadCrumb[] = [{nombre: 'Personal a Evaluar'}];

    filtros = {
        periodo:'',
        apellidos: '',
        nombre:'',
        categoria: '',
        servicio: '',
        cargo: '',
    };

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        public EvaluarService$: EvaluarService,
        public PersonalEvaluar$:PersonalEvaluarService

    ) {

    }

    ngOnInit(): void {
          this.listarPeriodos()
          this.cambiarFiltroPeriodo();
    }

    filtrarEmpleado() {
        this.pagina = 1;
        this.listarEmpleados();
    }

    listarEmpleados() {
        if(!this.filtros.periodo){
         return
        }

        let params: any = {
            ...this.filtros,
            longitud: this.longitud,
            pagina: this.pagina,
        };

        this.loading = true;
        this.PersonalEvaluar$.listarEmpleados(params)
            .pipe(finalize(() => (this.loading = false)))
            .subscribe(({ estado, mensaje, datos }) => {
                if (estado) {
                    this.historial = datos || [];
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            });
    }
 listarPeriodos() {
        
        this.loading = true;
        this.EvaluarService$.listarPeriodos()
            .pipe(finalize(() => this.loading = false))
            .subscribe(({estado, mensaje, datos}) => {
                if (estado) {
                    this.periodos = datos!;
                    this.filtros.periodo=this.periodos[0].id
                    this.listarEmpleados()
                } else {
                    errorAlerta('Error!', mensaje).then();
                }
            })

    }
    cambioPagina(pagina: number) {
        this.pagina = pagina;
        this.listarEmpleados();
    }

  cambiarFiltroPeriodo(){
   this.historial=[]
   this.listarEmpleados()

}

 async abrirNuevoPersonalEvaluar() {
        let respuesta = await this.modalPersonal.openModal(1,this.filtros.periodo);
        if (respuesta) {
          this.listarEmpleados()
        }
    }
 async abrirEditarPersonalEvaluar(id:any) {
        let respuesta = await this.modalPersonalE.openModal(2,this.filtros.periodo,id);
        
    }

}
