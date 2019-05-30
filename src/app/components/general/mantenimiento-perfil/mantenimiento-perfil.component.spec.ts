import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MantenimientoPerfilComponent } from './mantenimiento-perfil.component';

describe('MantenimientoPerfilComponent', () => {
  let component: MantenimientoPerfilComponent;
  let fixture: ComponentFixture<MantenimientoPerfilComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MantenimientoPerfilComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MantenimientoPerfilComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
