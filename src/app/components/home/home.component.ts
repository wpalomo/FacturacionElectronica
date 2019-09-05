import { Component, OnInit } from '@angular/core';
import { MenuItem } from 'primeng/api';
import { TabPanel } from 'primeng/components/tabview/tabview';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  items1: MenuItem[];

  items2: MenuItem[];

  activeItem: MenuItem;

  //items: TabPanel

  //items: any = [{ header: 'tabbdfd', selected: false, disabled: false, closable: true, closed: false, content: 'app-cambio-clave' }, { header: '3333', selected: false, disabled: false, closable: true, closed: false, content: 'camboya' }];

  items: any = [];

  constructor() { }

  ngOnInit() {
    /*
    this.items1 = [
      { label: 'Stats', icon: 'fa fa-fw fa-bar-chart', routerLink: ['/cambio-clave'] },
      { label: 'Calendar', icon: 'fa fa-fw fa-calendar' },
      { label: 'Documentation', icon: 'fa fa-fw fa-book' },
      { label: 'Support', icon: 'fa fa-fw fa-support' },
      { label: 'Social', icon: 'fa fa-fw fa-twitter' }
    ];

    this.items2 = [
      { label: 'Stats', icon: 'fa fa-fw fa-bar-chart' },
      { label: 'Calendar', icon: 'fa fa-fw fa-calendar' },
      { label: 'Documentation', icon: 'fa fa-fw fa-book' },
      { label: 'Support', icon: 'fa fa-fw fa-support' },
      { label: 'Social', icon: 'fa fa-fw fa-twitter' }
    ];

    this.activeItem = this.items2[0];
    */
  }

  closeItem(event, index) {
    this.items2 = this.items2.filter((item, i) => i !== index);
    event.preventDefault();
    this.items2.push({ label: 'Socialite france', icon: 'fa fa-fw fa-twitter' })
  }

  handleClick(event) {
    alert('xdd');
    this.items.push({ header: 'xyy', selected: false, disabled: false, closable: true, closed: false, content: 'app-mantenimiento-perfil' });
  }
}
