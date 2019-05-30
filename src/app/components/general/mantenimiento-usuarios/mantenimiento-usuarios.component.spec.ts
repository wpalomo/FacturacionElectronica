import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MantenimientoUsuariosComponent } from './mantenimiento-usuarios.component';

describe('MantenimientoUsuariosComponent', () => {
  let component: MantenimientoUsuariosComponent;
  let fixture: ComponentFixture<MantenimientoUsuariosComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MantenimientoUsuariosComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MantenimientoUsuariosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
