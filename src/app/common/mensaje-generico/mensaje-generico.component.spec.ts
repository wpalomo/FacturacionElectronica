import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MensajeGenericoComponent } from './mensaje-generico.component';

describe('MensajeGenericoComponent', () => {
  let component: MensajeGenericoComponent;
  let fixture: ComponentFixture<MensajeGenericoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MensajeGenericoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MensajeGenericoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
