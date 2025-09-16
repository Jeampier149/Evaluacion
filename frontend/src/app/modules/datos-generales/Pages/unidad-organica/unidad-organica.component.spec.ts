import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UnidadOrganicaComponent } from './unidad-organica.component';

describe('UnidadOrganicaComponent', () => {
  let component: UnidadOrganicaComponent;
  let fixture: ComponentFixture<UnidadOrganicaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UnidadOrganicaComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(UnidadOrganicaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
