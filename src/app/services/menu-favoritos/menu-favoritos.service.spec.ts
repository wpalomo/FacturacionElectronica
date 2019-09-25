import { TestBed } from '@angular/core/testing';

import { MenuFavoritosService } from './menu-favoritos.service';

describe('MenuFavoritosService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: MenuFavoritosService = TestBed.get(MenuFavoritosService);
    expect(service).toBeTruthy();
  });
});
