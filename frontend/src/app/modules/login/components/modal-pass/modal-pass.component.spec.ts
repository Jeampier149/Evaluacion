import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalPassComponent } from './modal-pass.component';

describe('ModalPassComponent', () => {
  let component: ModalPassComponent;
  let fixture: ComponentFixture<ModalPassComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ModalPassComponent]
    });
    fixture = TestBed.createComponent(ModalPassComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
