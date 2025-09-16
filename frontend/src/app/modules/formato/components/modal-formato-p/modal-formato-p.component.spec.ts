import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalFormatoPComponent } from './modal-formato-p.component';

describe('ModalFormatoPComponent', () => {
  let component: ModalFormatoPComponent;
  let fixture: ComponentFixture<ModalFormatoPComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ModalFormatoPComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ModalFormatoPComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
