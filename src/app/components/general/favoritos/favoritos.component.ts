import { Component, OnInit, ViewChild, ElementRef, Renderer2 } from '@angular/core';
import { Observable } from 'rxjs';

import { LoginService } from '../../../services/login/login.service';
import { MenuFavoritosService } from '../../../services/menu-favoritos/menu-favoritos.service';
import ITB_GEN_FAVORITOS from 'src/app/model/ITB_GEN_FAVORITOS';

@Component({
  selector: 'app-favoritos',
  templateUrl: './favoritos.component.html',
  styleUrls: ['./favoritos.component.css']
})
export class FavoritosComponent implements OnInit {
  iSession$: Observable<any>;
  favoritos: ITB_GEN_FAVORITOS[];
  favorito: ITB_GEN_FAVORITOS = {};
  selectedFavoritos: ITB_GEN_FAVORITOS[];
  cols: any[];
  id_usuario: any;
  errorMsg;
  displayWait: boolean;
  displayMensaje: boolean;
  tipoMensaje: string;
  title: string = 'Menu Favoritos';

  constructor(
    private loginService: LoginService,
    private menuFavoritosService: MenuFavoritosService
  ) { }

  ngOnInit() {
    this.iSession$ = this.loginService.getSesion;

    this.iSession$.subscribe(val => this.id_usuario = val.id_usuario);

    console.log(this.iSession$);

    const postData = new FormData();
    //alert(event.first.toString());
    //alert(event.rows.toString());
    postData.append('id_usuario', this.id_usuario);
    postData.append('action', 'getMenuFavoritos');


    //this.menuFavoritosService.getFavoritos().subscribe(
    //  data => {
    //    this.favoritos = data;
    //    console.log(this.favoritos);
    //    console.log('despues');
    //    console.log(this.favoritos[0]);

    //    let selected: ITB_GEN_FAVORITOS[] = this.favoritos.filter(favorito => favorito.acceso == 'S');

    //    this.selectedFavoritos = this.favoritos.filter(favorito => favorito.acceso == 'S');

    //    /*
    //    console.log(selected);

    //    for (let i = 0; i < this.favoritos.length; i++) {
    //      console.log(this.favoritos[i].acceso);
    //      this.selectedFavoritos.push(this.favoritos[i]);
    //      //var currentNumber = numbers[i];
    //      //if (currentNumber > 10) {
    //      //  greaterTen.push(currentNumber)
    //      //}
    //    }
    //    */
    //    //this.selectedFavoritos = [selected];
    //    //this.selectedFavoritos.push({
    //    //  "id_menu_favoritos": 1
    //    //});
    //  }
    //);


    this.menuFavoritosService.getMenuFavoritos(postData).subscribe(
      data => {
        //alert(data);

        //this.totalRecords$ = this.mantenimientoPerfilService.getTotalRecords();
        //this.favoritos = data;
        //console.log(this.perfiles);
        console.log('menu-favoritos');
        console.log(data);
        this.favoritos = data;

        this.selectedFavoritos = this.favoritos.filter(favorito => favorito.estado_menu_favoritos == 'A');
      },
      error => {
        //this.displayWait = false;
        this.errorMsg = error;
        //console.log(this.errorMsg);

        //this.displayWait = false;
        this.displayMensaje = true;
        this.tipoMensaje = 'ERROR';
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

  onClickBtnFavoritos(selectedFavoritos) {
    const postData = new FormData();
    postData.append('json', JSON.stringify(this.favoritos));
    postData.append('action', 'updateMenuFavoritos');

    this.displayWait = true;

    this.menuFavoritosService.updateMenuFavoritos(postData).subscribe(
      data => {
        this.displayWait = false;
        //this.tipoMensaje = 'OK';
        //this.displayMensaje = true;
        this.errorMsg = data.mensaje;
        // alert(data.mensaje);

        this.tipoMensaje = 'OK';

        this.displayMensaje = true;

        this.updateMenuGeneral();
      },
      error => {
        this.displayWait = false;
        this.errorMsg = error;
        console.log(this.errorMsg);

        //this.displayWait = false;
        //this.displayMensaje = true;
        //this.tipoMensaje = 'ERROR';
      }
    );



  }

  onRowSelect(event) {
    //this.messageService.add({ severity: 'info', summary: 'Car Selected', detail: 'Vin: ' + event.data.vin });
    //alert('onRowSelect');
    //console.log(event.data);
    //this.favoritos.findIndex(x => {
    //  x.id_menu_favoritos == event.data.id_menu_favoritos
    //});

    this.favoritos.forEach(d => {
      if (d.id_menu_favoritos === event.data.id_menu_favoritos) {
        d.estado_menu_favoritos = 'A'
      }
    });
  }

  onRowUnselect(event) {
    //this.messageService.add({ severity: 'info', summary: 'Car Unselected', detail: 'Vin: ' + event.data.vin });
    //alert('onRowUnselect');
    this.favoritos.forEach(d => {
      if (d.id_menu_favoritos === event.data.id_menu_favoritos) {
        d.estado_menu_favoritos = 'I'
      }
    });
  }

  onHeaderCheckboxToggle(event) {
    this.favoritos.forEach(d => {
      console.log(d.acceso);

      console.log(event.checked);

      d.estado_menu_favoritos = (event.checked === true ? 'A' : 'I');

      //this.perfilesActivos.push({ label: d.descripcion_perfil, value: d.id_perfil });
    });
  }

  updateMenuGeneral() {
    const postData2 = new FormData();
    //this.displayMensaje = false;

    postData2.append('id_usuario', this.id_usuario.toString());
    postData2.append('action', 'getMenuUsuario');

    this.loginService.getMenu(postData2).subscribe(
      data => {
        //alert('99');
        console.log('99');
        console.log(data);

      }, error => {
        this.errorMsg = error;
        console.log(this.errorMsg);

        this.displayWait = false;
        this.displayMensaje = true;
        this.tipoMensaje = 'ERROR';
        //this.displayError = true;
      }
    );
  }

  onDialogClose(event, tipo) {
    //alert('close dialog');
    this.displayMensaje = event;
    //this.setFocus(this.nameField.nativeElement);
    //if (tipo === 'F') {
    //this.setFocus(elm);
    //  this.setFocus(this.nameField.nativeElement);
    //}
  }
}
