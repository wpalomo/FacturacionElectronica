import { TestBed } from '@angular/core/testing';

import { CambioClaveService } from './cambio-clave.service';

describe('CambioClaveService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: CambioClaveService = TestBed.get(CambioClaveService);
    expect(service).toBeTruthy();
  });
});
