import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalEditarPersonalEvaluarComponent } from './modal-editar-personal-evaluar.component';

describe('ModalEditarPersonalEvaluarComponent', () => {
  let component: ModalEditarPersonalEvaluarComponent;
  let fixture: ComponentFixture<ModalEditarPersonalEvaluarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ModalEditarPersonalEvaluarComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ModalEditarPersonalEvaluarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
