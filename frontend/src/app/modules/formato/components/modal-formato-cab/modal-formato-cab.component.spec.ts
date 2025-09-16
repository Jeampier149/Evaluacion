import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalFormatoCabComponent } from './modal-formato-cab.component';

describe('ModalFormatoCabComponent', () => {
  let component: ModalFormatoCabComponent;
  let fixture: ComponentFixture<ModalFormatoCabComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ModalFormatoCabComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ModalFormatoCabComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
