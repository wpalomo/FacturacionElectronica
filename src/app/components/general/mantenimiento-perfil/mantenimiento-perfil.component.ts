import { Component, OnInit } from '@angular/core';
import { LazyLoadEvent } from 'primeng/components/common/api';
import { MantenimientoPerfilService } from '../../../services/mantenimiento-perfil/mantenimiento-perfil.service';
import ITB_GEN_PERFILES from '../../../model/ITB_GEN_PERFILES';

@Component({
  selector: 'app-mantenimiento-perfil',
  templateUrl: './mantenimiento-perfil.component.html',
  styleUrls: ['./mantenimiento-perfil.component.css']
})
export class MantenimientoPerfilComponent implements OnInit {
  displayDialog: boolean;
  perfiles: ITB_GEN_PERFILES[];
  perfil: ITB_GEN_PERFILES = {};
  cols: any[];
  nuevoRegistro: boolean;
  disabled: boolean = true;
  hiddenButtonDelete: boolean;

  constructor(
    private mantenimientoPerfilService: MantenimientoPerfilService
  ) { }

  ngOnInit() {
    /*
    this.mantenimientoPerfilService.getPerfiles().subscribe(
      data => {
        this.perfiles = data;
        console.log(this.perfiles);
      }
    );
      */

    this.cols = [
      {
        field: 'id_perfil',
        header: 'Codigo',
        filterMatchMode: 'equals',
        width: '20%'
      },
      {
        field: 'descripcion_perfil',
        header: 'Descripcion',
        filterMatchMode: 'contains',
        width: '60%'
      },
      {
        field: 'descripcion_estado_perfil',
        header: 'Estado',
        filterMatchMode: 'contains',
        width: '20%'
      }
    ];
  }

  loadLazy(event: LazyLoadEvent) {
    //event.first = First row offset
    //event.rows = Number of rows per page
    //event.sortField = Field name to sort with
    //event.sortOrder = Sort order as number, 1 for asc and -1 for dec
    //filters: FilterMetadata object having field as key and filter value, filter matchMode as value

    //alert(event.first);
    //alert(event.rows);
    //alert(event.sortField);
    //alert(event.sortOrder);
    //alert(event.filters);

    //console.log(event.first);
    //console.log(event.rows);
    //console.log(event.sortField);
    //console.log(event.sortOrder);
    //console.log(event.filters);

    this.mantenimientoPerfilService.getPerfiles(event).subscribe(
      data => {
        this.perfiles = data;
        console.log(this.perfiles);
      }
    );

    //this.browserService.getBrowsers().subscribe((browsers: any) =>
    //  this.browsers = browsers.slice(event.first, (event.first + event.rows)));
  }

  modificarRegistro(perfil: ITB_GEN_PERFILES) {
    this.nuevoRegistro = false;
    alert(perfil.id_perfil);
    this.perfil = this.cloneRegistro(perfil);
    alert(this.perfil.descripcion_perfil);
    this.displayDialog = true;
    this.hiddenButtonDelete = false;
  }

  showDialogToAdd() {
    this.nuevoRegistro = true;
    this.displayDialog = true;
    this.hiddenButtonDelete = true;
    this.perfil = {};
  }

  cloneRegistro(c: ITB_GEN_PERFILES): ITB_GEN_PERFILES {
    const perfil = {};
    for (const prop in c) {
      if (c) {
        perfil[prop] = c[prop];
      }

    }
    return perfil;
  }
}
