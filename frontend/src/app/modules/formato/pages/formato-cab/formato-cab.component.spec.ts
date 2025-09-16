import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormatoCabComponent } from './formato-cab.component';

describe('FormatoCabComponent', () => {
  let component: FormatoCabComponent;
  let fixture: ComponentFixture<FormatoCabComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormatoCabComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(FormatoCabComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
