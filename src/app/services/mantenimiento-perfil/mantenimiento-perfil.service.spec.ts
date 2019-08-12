import { TestBed } from '@angular/core/testing';

import { MantenimientoPerfilService } from './mantenimiento-perfil.service';

describe('MantenimientoPerfilService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: MantenimientoPerfilService = TestBed.get(MantenimientoPerfilService);
    expect(service).toBeTruthy();
  });
});
