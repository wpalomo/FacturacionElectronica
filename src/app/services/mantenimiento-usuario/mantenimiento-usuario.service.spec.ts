import { TestBed } from '@angular/core/testing';

import { MantenimientoUsuarioService } from './mantenimiento-usuario.service';

describe('MantenimientoUsuarioService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: MantenimientoUsuarioService = TestBed.get(MantenimientoUsuarioService);
    expect(service).toBeTruthy();
  });
});
