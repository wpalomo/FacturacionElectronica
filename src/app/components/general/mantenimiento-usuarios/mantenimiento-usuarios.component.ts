import { Component, OnInit, ViewChild, Renderer2 } from '@angular/core';
import { LazyLoadEvent, Message } from 'primeng/components/common/api';
import { ConfirmationService } from 'primeng/api';

import { Observable } from 'rxjs';
import { MantenimientoUsuarioService } from '../../../services/mantenimiento-usuario/mantenimiento-usuario.service';
import { MantenimientoPerfilService } from '../../../services/mantenimiento-perfil/mantenimiento-perfil.service';
import { EstadoService } from '../../../services/estado/estado.service';
import ITB_GEN_USUARIOS from '../../../model/ITB_GEN_USUARIOS';
import ITB_GEN_PERFILES from '../../../model/ITB_GEN_PERFILES';
import IEstados from '../../../model/IEstados';

@Component({
  selector: 'app-mantenimiento-usuarios',
  templateUrl: './mantenimiento-usuarios.component.html',
  styleUrls: ['./mantenimiento-usuarios.component.css']
})
export class MantenimientoUsuariosComponent implements OnInit {
  @ViewChild('dt') dt: any;
  filtering() {
    //alert('fffff');
    this.dt.reset();
  }

  displayDialog: boolean;
  displayWait: boolean;
  usuarios: ITB_GEN_USUARIOS[];
  usuario: ITB_GEN_USUARIOS = {};
  perfiles: ITB_GEN_PERFILES[];
  cols: any[];
  nuevoRegistro: boolean;
  disabled: boolean = true;
  hiddenButtonDelete: boolean;
  estados: IEstados[];
  estadosActivos: IEstados[];
  perfilesActivos: IEstados[] = [];
  grades: IEstados[];
  errorMsg;
  displayMensaje: boolean;
  tipoMensaje: string;
  first = 0;
  selectedEstadoFilter: any;
  selectedMultipleEstadoFilter: any;
  selectedEstado: any;
  selectedPerfil: any;
  tipoOperacion: string = "";
  auxEvent: LazyLoadEvent;
  txtDescripcion: string;
  txtNombreApellido: string;
  txtIdUsuario: string;
  txtLogin: string;
  msgs: Message[] = [];

  totalRecords$: Observable<number>;

  constructor(
    private mantenimientoUsuarioService: MantenimientoUsuarioService,
    private mantenimientoPerfilService: MantenimientoPerfilService,
    private estadoService: EstadoService,
    public renderer: Renderer2,
    private confirmationService: ConfirmationService
  ) { }

  ngOnInit() {
    this.inicializarPantalla();

    this.cols = [
      /*
      {
        field: 'id_usuario',
        header: 'Codigo',
        filterMatchMode: 'startsWith',
        width: '20%'
      },
      */
      {
        field: 'nombre_apellido',
        header: 'Nombre',
        filterMatchMode: 'contains',
        width: '25%',
        display: 'table-cell'
      },
      {
        field: 'login',
        header: 'Login',
        filterMatchMode: 'contains',
        width: '25%',
        display: 'table-cell'
      },
      {
        field: 'descripcion_perfil',
        header: 'Perfil',
        filterMatchMode: 'contains',
        width: '25%',
        display: 'table-cell'
      },

      {
        field: 'estado_usuario',
        header: 'Estado',
        filterMatchMode: 'equals',
        width: '20%',
        display: 'table-cell'
      }
    ];

  }

  inicializarPantalla() {
    this.txtDescripcion = '';
    this.txtIdUsuario = '';
    this.txtLogin = '';
    this.txtNombreApellido = '';

    this.estadoService.getEstados().subscribe(
      data => {
        this.estados = data;
      }
    );


    this.estadoService.getEstadosActivos().subscribe(
      data => {
        this.estadosActivos = data;
        //this.selectedMultipleEstadoFilter = { label: "ACTIVO", value: "A" };
      }
    );



    this.dt.reset();
    this.selectedEstadoFilter = { label: "TODOS", value: "T" };
  }

  loadLazy(event: LazyLoadEvent) {
    this.auxEvent = event;
    //event.first = First row offset
    //event.rows = Number of rows per page
    //event.sortField = Field name to sort with
    //event.sortOrder = Sort order as number, 1 for asc and -1 for dec
    //filters: FilterMetadata object having field as key and filter value, filter matchMode as value

    console.log(event);
    console.log(event.filters);


    //alert(event.first);
    //alert(event.rows);
    //alert(event.sortField);
    //alert(event.sortOrder);
    //alert(event.filters);

    console.log(event.filters);

    //console.log(event.first);
    //console.log(event.rows);
    //console.log(event.sortField);
    //console.log(event.sortOrder);

    //if (event.filters) {
    //  console.log(event.filters);
    //  console.log(event.filters.id_perfil);
    //  console.log(event.filters.id_perfil.value);
    //}


    //this.filtering();

    const postData = new FormData();
    //alert(event.first.toString());
    //alert(event.rows.toString());
    postData.append('start', event.first.toString());
    postData.append('limit', event.rows.toString());

    if (event.sortField) {
      postData.append('sortField', event.sortField);
      postData.append('sortOrder', event.sortOrder.toString());
    }

    postData.append('filters', JSON.stringify(event.filters));
    postData.append('action', 'getUsuarios');

    //this.mantenimientoPerfilService.getPerfiles(event).subscribe(
    //  data => {
    //    this.perfiles = data;
    //    console.log(this.perfiles);
    //  }
    //);


    this.mantenimientoUsuarioService.getUsuarios(postData).subscribe(
      data => {
        //alert(data);

        this.totalRecords$ = this.mantenimientoUsuarioService.getTotalRecords();
        this.usuarios = data;
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

  reset() {
    this.first = 0;
  }

  modificarRegistro(usuario: ITB_GEN_USUARIOS) {
    this.tipoOperacion = 'U';
    this.nuevoRegistro = false;
    this.usuario = this.cloneRegistro(usuario);
    this.displayDialog = true;
    this.hiddenButtonDelete = false;
    this.selectedEstado = { label: usuario.descripcion_estado_usuario, value: usuario.estado_usuario }
  }

  showDialogToAdd() {
    const postData = new FormData();
    postData.append('estado_perfil', 'A');
    postData.append('action', 'getPerfilesxEstado');

    this.mantenimientoPerfilService.getPerfilesxEstado(postData).subscribe(
      data => {
        this.perfiles = data;
        //this.perfilesActivos = data;
        //console.log(this.perfiles);
        console.log(data);
        console.log(data[0].id_perfil);
        console.log(data[0].descripcion_perfil);


        //this.perfiles.find()

        data.forEach(d => {
          console.log(d.id_perfil);

          this.perfilesActivos.push({ label: d.descripcion_perfil, value: d.id_perfil });
        });

        
        this.selectedPerfil = { label: data[0].descripcion_perfil, value: data[0].id_perfil };

        this.tipoOperacion = 'I';
        this.nuevoRegistro = true;
        this.displayDialog = true;
        this.hiddenButtonDelete = true;
        this.usuario = {};
        this.selectedEstado = { label: "ACTIVO", value: "A" };
      },
      error => {
        this.errorMsg = error;
        this.displayMensaje = true;
        this.tipoMensaje = 'ERROR';
      }
    );


  }

  cloneRegistro(c: ITB_GEN_USUARIOS): ITB_GEN_USUARIOS {
    const perfil = {};
    for (const prop in c) {
      if (c) {
        perfil[prop] = c[prop];
      }

    }
    return perfil;
  }
}
