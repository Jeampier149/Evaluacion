import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalPersonalEvaluarComponent } from './modal-personal-evaluar.component';

describe('ModalPersonalEvaluarComponent', () => {
  let component: ModalPersonalEvaluarComponent;
  let fixture: ComponentFixture<ModalPersonalEvaluarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ModalPersonalEvaluarComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ModalPersonalEvaluarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
