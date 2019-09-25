import { Component, OnInit, ViewChild, ElementRef, Renderer2 } from '@angular/core';
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
  selectedFavoritos: ITB_GEN_FAVORITOS[];
  cols: any[];

  constructor(
    private menuFavoritosService: MenuFavoritosService
  ) { }

  ngOnInit() {
    this.menuFavoritosService.getFavoritos().subscribe(
      data => {
        this.favoritos = data;
        console.log(this.favoritos);
        console.log('despues');
        console.log(this.favoritos[0]);

        let selected: ITB_GEN_FAVORITOS[] = this.favoritos.filter(favorito => favorito.acceso == 'S');

        this.selectedFavoritos = this.favoritos.filter(favorito => favorito.acceso == 'S');

        /*
        console.log(selected);

        for (let i = 0; i < this.favoritos.length; i++) {
          console.log(this.favoritos[i].acceso);
          this.selectedFavoritos.push(this.favoritos[i]);
          //var currentNumber = numbers[i];
          //if (currentNumber > 10) {
          //  greaterTen.push(currentNumber)
          //}
        }
        */
        //this.selectedFavoritos = [selected];
        //this.selectedFavoritos.push({
        //  "id_menu_favoritos": 1
        //});
      }
    );

    this.cols = [
      {
        field: 'id_menu_favoritos',
        header: 'Codigo',
        width: '10%'
      },
      {
        field: 'nombre_menu',
        header: 'Menu',
        width: '40%'
      }
    ];



    //this.selectedFavoritos.push({
    //  id_menu_favoritos: 50, id_menu: 5, nombre_menu: "Cambio de Clave", acceso: "S", estado_menu_favoritos: "A"
    //})
    //this.selectedFavoritos.id_menu_favoritos = 2;
  }

}
