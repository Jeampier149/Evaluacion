import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormatoFormatoCriterioComponent } from './formato-formato-criterio.component';

describe('FormatoFormatoCriterioComponent', () => {
  let component: FormatoFormatoCriterioComponent;
  let fixture: ComponentFixture<FormatoFormatoCriterioComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormatoFormatoCriterioComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormatoFormatoCriterioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
