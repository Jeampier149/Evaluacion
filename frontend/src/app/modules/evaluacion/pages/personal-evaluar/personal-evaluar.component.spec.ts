import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PersonalEvaluarComponent } from './personal-evaluar.component';

describe('PersonalEvaluarComponent', () => {
  let component: PersonalEvaluarComponent;
  let fixture: ComponentFixture<PersonalEvaluarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PersonalEvaluarComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(PersonalEvaluarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
