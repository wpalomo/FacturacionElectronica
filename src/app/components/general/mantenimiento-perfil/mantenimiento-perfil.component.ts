import { Component, OnInit } from '@angular/core';
import { LazyLoadEvent } from 'primeng/components/common/api';
import { MantenimientoPerfilService } from '../../../services/mantenimiento-perfil/mantenimiento-perfil.service';
import { EstadoService } from '../../../services/estado/estado.service';
import ITB_GEN_PERFILES from '../../../model/ITB_GEN_PERFILES';
import IEstados from '../../../model/IEstados';

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
  totalRecords = 150;
  estados: IEstados[];
  grades: IEstados[];
  errorMsg;
  displayMensaje: boolean;
  tipoMensaje: string;

  constructor(
    private mantenimientoPerfilService: MantenimientoPerfilService,
    private estadoService: EstadoService
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

    this.estadoService.getEstados().subscribe(
      data => {
        this.estados = data;
      }
    )

    this.grades = [];
    this.grades.push({ label: 'ACTIVO', value: 'ACTIVO' });
    this.grades.push({ label: 'INACTIVO', value: 'INACTIVO' });

    this.cols = [
      {
        field: 'id_perfil',
        header: 'Codigo',
        filterMatchMode: 'startsWith',
        width: '20%'
      },
      {
        field: 'descripcion_perfil',
        header: 'Descripcion',
        filterMatchMode: 'contains',
        width: '40%',
        display: 'table-cell'
      },
      {
        field: 'estado_perfil',
        header: 'Estado',
        filterMatchMode: 'equals',
        width: '20%',
        display: 'table-cell'
      },


      {
        field: 'descripcion_estado_perfil',
        header: 'Estado',
        filterMatchMode: 'in',
        width: '20%',
        display: 'table-cell'
      }
    ];

    //this.estados = [];
    //this.estados.push({ label: "ACTIVO", value: "A" })
    //this.estados.push({ label: "INACTIVO", value: "I" })
    //this.estados.push({ label: "TODOS", value: "T" })
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

    //if (event.filters) {
    //  console.log(event.filters);
    //  console.log(event.filters.id_perfil);
    //  console.log(event.filters.id_perfil.value);
    //}

    const postData = new FormData();
    postData.append('start', event.first.toString());
    postData.append('limit', event.rows.toString());

    if (event.sortField) {
      postData.append('sortField', event.sortField);
      postData.append('sortOrder', event.sortOrder.toString());
    }

    postData.append('filters', JSON.stringify(event.filters));
    postData.append('action', 'getPerfiles');

    //this.mantenimientoPerfilService.getPerfiles(event).subscribe(
    //  data => {
    //    this.perfiles = data;
    //    console.log(this.perfiles);
    //  }
    //);


    this.mantenimientoPerfilService.getPerfiles2(postData).subscribe(
      data => {
        alert(data);
        this.perfiles = data;
        //console.log(this.perfiles);
        console.log(data);
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
