import {  Component, OnInit, ViewChild, ElementRef, Renderer2 } from '@angular/core';
import { Observable } from 'rxjs';

import { MenuFavoritosService } from '../../../services/menu-favoritos/menu-favoritos.service';
import ITB_GEN_FAVORITOS from 'src/app/model/ITB_GEN_FAVORITOS';

@Component({
  selector: 'app-favoritos',
  templateUrl: './favoritos.component.html',
  styleUrls: ['./favoritos.component.css']
})
export class FavoritosComponent implements OnInit {
  favoritos: ITB_GEN_FAVORITOS[];
  favorito: ITB_GEN_FAVORITOS = {};

  constructor(
    private menuFavoritosService: MenuFavoritosService
  ) { }

  ngOnInit() {
    this.menuFavoritosService.getFavoritos().subscribe(
      data => {
        this.favoritos = data;
        console.log(this.favoritos);
      }
    );
  }

}
