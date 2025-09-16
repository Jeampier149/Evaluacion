import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MiEvaluacionComponent } from './mi-evaluacion.component';

describe('MiEvaluacionComponent', () => {
  let component: MiEvaluacionComponent;
  let fixture: ComponentFixture<MiEvaluacionComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MiEvaluacionComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(MiEvaluacionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
