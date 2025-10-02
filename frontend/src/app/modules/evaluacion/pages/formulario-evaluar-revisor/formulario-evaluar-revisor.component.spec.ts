import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormularioEvaluarRevisorComponent } from './formulario-evaluar-revisor.component';

describe('FormularioEvaluarRevisorComponent', () => {
  let component: FormularioEvaluarRevisorComponent;
  let fixture: ComponentFixture<FormularioEvaluarRevisorComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormularioEvaluarRevisorComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormularioEvaluarRevisorComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
