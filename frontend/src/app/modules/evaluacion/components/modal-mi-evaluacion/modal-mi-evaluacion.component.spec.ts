import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalMiEvaluacionComponent } from './modal-mi-evaluacion.component';

describe('ModalMiEvaluacionComponent', () => {
  let component: ModalMiEvaluacionComponent;
  let fixture: ComponentFixture<ModalMiEvaluacionComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ModalMiEvaluacionComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ModalMiEvaluacionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
