import { Component, OnInit } from '@angular/core';
import { LoginService } from './services/login/login.service';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'FacturacionElectronica';
  isLoggedIn$: Observable<boolean>;
  menu$: Observable<any>;
  selectedMenu = null;

  data = [
    { code: 'FI-SW-01', name: 'Koi', unitcost: 10.00, status: 'P', listprice: 36.50, attr: 'Large', itemid: 'EST-1' },
    { code: 'K9-DL-01', name: 'Dalmation', unitcost: 12.00, status: 'P', listprice: 18.50, attr: 'Spotted Adult Female', itemid: 'EST-10' },
    { code: 'RP-SN-01', name: 'Rattlesnake', unitcost: 12.00, status: 'P', listprice: 38.50, attr: 'Venomless', itemid: 'EST-11' },
    { code: 'RP-SN-01', name: 'Rattlesnake', unitcost: 12.00, status: 'P', listprice: 26.50, attr: 'Rattleless', itemid: 'EST-12' },
    { code: 'RP-LI-02', name: 'Iguana', unitcost: 12.00, status: 'P', listprice: 35.50, attr: 'Green Adult', itemid: 'EST-13' },
    { code: 'FL-DSH-01', name: 'Manx', unitcost: 12.00, status: 'P', listprice: 158.50, attr: 'Tailless', itemid: 'EST-14' },
    { code: 'FL-DSH-01', name: 'Manx', unitcost: 12.00, status: 'P', listprice: 83.50, attr: 'With tail', itemid: 'EST-15' },
    { code: 'FL-DLH-02', name: 'Persian', unitcost: 12.00, status: 'P', listprice: 23.50, attr: 'Adult Female', itemid: 'EST-16' },
    { code: 'FL-DLH-02', name: 'Persian', unitcost: 12.00, status: 'P', listprice: 89.50, attr: 'Adult Male', itemid: 'EST-17' },
    { code: 'AV-CB-01', name: 'Amazon Parrot', unitcost: 92.00, status: 'P', listprice: 63.50, attr: 'Adult Male', itemid: 'EST-18' }
  ];

  menus = [
    {
      text: 'Favoritos',
      iconCls: 'fa fa-star',
      state: 'open',
      children: [
        {
          text: 'Cambio de Clave',
          routerLink: '/cambio-clave'
        },
        {
          text: 'Favoritos',
          routerLink: '/favoritos'
        },
        {
          text: 'Ambiente',
          routerLink: '/ambiente'
        },
        {
          text: 'Parametros',
          routerLink: '/parametros'
        },
        {
          text: 'Unidades de Tiempo',
          routerLink: '/ambiente'
        },
        {
          text: 'Formas de Pago',
          routerLink: '/ambiente'
        },
        {
          text: 'Procesar Documentos Electronicos',
          routerLink: '/parametros'
        },
        {
          text: 'Consulta de Documentos Electrónicos',
          routerLink: '/parametros'
        }
      ]
    },
    {
      text: 'Modulo General',
      iconCls: 'fa fa-home',
      children: [
        {
          text: 'Usuarios',
          children: [
            {
              text: 'Mantenimiento de Usuarios',
              routerLink: '/mantenimiento-usuarios'
            },
            {
              text: 'Cambio de Clave',
              routerLink: '/cambio-clave'
            },
            {
              text: 'Favoritos',
              routerLink: '/favoritos'
            }
          ]
        },
        {
          text: 'Seguridades',
          children: [
            {
              text: 'Mantenimiento de Perfil',
              routerLink: '/ambiente'
            },
            {
              text: 'Permisos',
              routerLink: '/ambiente'
            }
          ]
        }
      ]
    },
    {
      text: 'Parametros',
      iconCls: 'fa fa-wpforms',
      children: [
        {
          text: 'Ambiente',
          routerLink: '/ambiente'
        },
        {
          text: 'Parametros',
          routerLink: '/parametros'
        },
        {
          text: 'Unidades de Tiempo',
          routerLink: '/ambiente'
        },
        {
          text: 'Formas de Pago',
          routerLink: '/ambiente'
        }
      ]
    },
    {
      text: 'Transacciones',
      iconCls: 'fa fa-at',
      selected: true,
      children: [
        {
          text: 'Procesar Documentos Electronicos',
          routerLink: '/parametros'
        }
      ]
    }, {
      text: 'Consultas',
      iconCls: 'fa fa-table',
      children: [
        {
          text: 'Consulta de Documentos Electrónicos'
        }
      ]
    }
  ];

  constructor(
    private loginService: LoginService
  ) { }

  ngOnInit() {
    this.isLoggedIn$ = this.loginService.isLoggedIn;
    this.menu$ = this.loginService.getMenus;
    alert(this.menu$);
  }

  onItemClick(item) {
    alert('1');
  }
}
