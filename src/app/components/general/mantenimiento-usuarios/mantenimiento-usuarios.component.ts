import { Component, OnInit, ViewChild, Renderer2, ElementRef } from '@angular/core';
import { LazyLoadEvent, Message } from 'primeng/components/common/api';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { ConfirmationService } from 'primeng/api';
import { MessageService } from 'primeng/api';

import { Observable } from 'rxjs';
import { MantenimientoUsuarioService } from '../../../services/mantenimiento-usuario/mantenimiento-usuario.service';
import { MantenimientoPerfilService } from '../../../services/mantenimiento-perfil/mantenimiento-perfil.service';
import { EstadoService } from '../../../services/estado/estado.service';
import ITB_GEN_USUARIOS from '../../../model/ITB_GEN_USUARIOS';
import ITB_GEN_PERFILES from '../../../model/ITB_GEN_PERFILES';
import IEstados from '../../../model/IEstados';
import { Dropdown } from 'primeng/dropdown';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';

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

  @ViewChild("txtNombre1") nameField: ElementRef;
  @ViewChild("txtApellido1") apellidoField: ElementRef;
  @ViewChild("txtLogin1") loginField: ElementRef;
  //@ViewChild("cmbPerfil1") perfilField: ElementRef;
  //@ViewChild('cmbPerfil1') perfilField: Dropdown;
  @ViewChild("txtEmail1") emailField: ElementRef;
  @ViewChild("txtClave1") claveField: ElementRef;
  @ViewChild("txtConfirmarClave1") confirmarClaveField: ElementRef;
  @ViewChild("txtCambiarClave1") cambiarClaveField: ElementRef;
  @ViewChild("txtConfirmarCambiarClave1") confirmarCambiarClaveField: ElementRef;
  @ViewChild("btnSave") saveField: ElementRef;



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
  perfilesActivos: IEstados[];
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
  //txtDescripcion: string;
  //txtNombreApellido: string;
  //txtIdUsuario: string;
  //txtLogin: string;
  //txtCambiarClave: string;
  //txtConfirmarClave: string;
  //txtConfirmarCambiarClave: string;
  msgs: Message[] = [];

  totalRecords$: Observable<number>;

  form: FormGroup;

  constructor(
    private fb: FormBuilder,
    private mantenimientoUsuarioService: MantenimientoUsuarioService,
    private mantenimientoPerfilService: MantenimientoPerfilService,
    private estadoService: EstadoService,
    public renderer: Renderer2,
    private confirmationService: ConfirmationService,
    private messageService: MessageService
  ) { }

  ngOnInit() {
    this.inicializarPantalla();
    this.buildForm();

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
    //this.txtDescripcion = '';
    //this.txtIdUsuario = '';
    //this.txtLogin = '';
    //this.txtNombreApellido = '';

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
    //this.txtCambiarClave = '';
    //this.txtConfirmarCambiarClave = '';
    this.tipoOperacion = 'U';
    this.nuevoRegistro = false;
    this.usuario = this.cloneRegistro(usuario);
    this.displayDialog = true;
    this.hiddenButtonDelete = false;
    this.selectedEstado = { label: usuario.descripcion_estado_usuario, value: usuario.estado_usuario }
  }

  showDialogToAdd() {
    //this.buildForm();

    /**/
    //this.tipoOperacion = 'I';
    //this.nuevoRegistro = true;
    //this.displayDialog = true;
    //this.hiddenButtonDelete = true;
    //this.usuario = {};
    //this.selectedEstado = { label: "ACTIVO", value: "A" };
    /**/

    //this.form.controls['txtNombre'].value = 'xxx';


    //this.newForm.setValue({
    //  firstName: 'abc',
    //  lastName: 'def'
    //});

    // TODO: manejar los errores al cargar los perfiles, ya que cuando dio un error no se presento el error en angular.
    const postData = new FormData();
    postData.append('estado_perfil', 'A');
    postData.append('action', 'getPerfilesxEstado');
    //this.txtConfirmarClave = '';


    this.mantenimientoPerfilService.getPerfilesxEstado(postData).subscribe(
      data => {
        this.perfiles = data;
        console.log(data);
        console.log(data[0].id_perfil);
        console.log(data[0].descripcion_perfil);

        this.perfilesActivos = [];
        data.forEach(d => {
          console.log(d.id_perfil);

          this.perfilesActivos.push({ label: d.descripcion_perfil, value: d.id_perfil });
        });

        this.selectedPerfil = { label: data[1].descripcion_perfil, value: data[1].id_perfil };

        this.tipoOperacion = 'I';
        this.nuevoRegistro = true;
        this.displayDialog = true;
        this.hiddenButtonDelete = true;
        this.usuario = {};
        this.selectedEstado = { label: "ACTIVO", value: "A" };

        this.buildForm();
        this.setValidators();
      },
      error => {
        this.errorMsg = error;
        this.displayMensaje = true;
        this.tipoMensaje = 'ERROR';
      }
    );

  }

  buildForm() {
    console.log(this.selectedPerfil);

    this.form = this.fb.group({
      txtNombre: ['', Validators.required],
      txtApellido: ['', Validators.required],
      txtLogin: ['', Validators.required],
      cmbPerfil: [this.selectedPerfil, Validators.required],
      txtEmail: ['', Validators.email],
      txtClave: ['', Validators.required],
      txtConfirmarClave: ['', Validators.required],
      cmbEstado: [this.selectedEstado, Validators.required],
      txtCambiarClave: [''],
      txtConfirmarCambiarClave: ['']

      /*
      txtNombre: ['', Validators.required],
      txtApellido: ['', Validators.required],
      txtLogin: ['', Validators.required],
      cmbPerfil: [this.selectedPerfil, Validators.required],
      txtEmail: ['', Validators.email],
      txtClave: ['', Validators.required],
      txtConfirmarClave: ['', Validators.required],
      cmbEstado: ['', Validators.required],
      txtCambiarClave: [''],
      txtConfirmarCambiarClave: ['']
      */
    });
  }

  setValidators() {
    if (this.tipoOperacion == 'I') {
      let txtCambiarClaveControl = this.form.get('txtCambiarClave');
      let txtConfirmarCambiarClaveControl = this.form.get('txtConfirmarCambiarClave');

      txtCambiarClaveControl.setValidators(null);
      txtConfirmarCambiarClaveControl.setValidators(null);
    }

    if (this.tipoOperacion == 'U') {
      let txtClaveControl = this.form.get('txtClave');
      let txtConfirmarClaveControl = this.form.get('txtConfirmarClave');

      txtClaveControl.setValidators(null);
      txtConfirmarClaveControl.setValidators(null);
    }
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

  setFocus(elm: HTMLInputElement) {
    setTimeout(() => {
      elm.focus()
    }, 300);
  }

  onKeydown(event) {
    if (event.key === "Enter") {
      console.log(event);
      //console.log(field);
      console.log(event.target.id);
      this.ordenFocus(event.target.id);
      //this.setFocus(elm);
      //let ht:HTMLInputElement = document.getElementById(field);
      //this.setFocus(ht);
    }
  }

  ordenFocus(field: string) {
    switch (field) {
      case 'nombre':
        this.setFocus(this.apellidoField.nativeElement)
        break;
      case 'apellido':
        if (this.tipoOperacion == 'I') {
          this.setFocus(this.loginField.nativeElement)
        } else {
          //this.setFocus(this.perfilField.nativeElement)
          //this.perfilField.nativeElement.focus();
          //this.perfilField.nativeElement.focus();
          //this.perfilField.focus();
          //this.perfilField.nativeElement.style.focus();
          //document.getElementById('cmbPerfil').focus();
          //this.perfilField.panel
          //this.perfilField.panelVisible = true;
          this.setFocus(this.emailField.nativeElement);
        }
        break;
      case 'login':
        this.setFocus(this.emailField.nativeElement);
        break;
      case 'email':
        if (this.tipoOperacion == 'I') {
          this.setFocus(this.claveField.nativeElement);
        } else {
          this.setFocus(this.cambiarClaveField.nativeElement);
        }
        break;
      case 'clave':
        this.setFocus(this.confirmarClaveField.nativeElement);
        break;
      case 'confirmarClave':
        this.setFocus(this.saveField.nativeElement);
        break;
      case 'cambiarClave':
        this.setFocus(this.confirmarCambiarClaveField.nativeElement);
        break;
      case 'confirmarCambiarClave':
        this.setFocus(this.saveField.nativeElement);
        break;
    }
  }

  save() {
    //this.messageService.add({ severity: 'error', summary: 'Error Message', detail: 'Validation failed' });
    //this.form.setValue({ txtNombre: 'xxx' });


    this.form.controls.cmbPerfil.setValue(this.selectedPerfil);




    this.hiddenButtonDelete = !this.hiddenButtonDelete;

    //this.form.controls.txtLogin.disable(this.hiddenButtonDelete)
    /*
    this.form.setValue({
      txtNombre: 'xxx',
      txtApellido: 'yyy'
    });

    this.form.controls.txtNombre.setValue('abc');

    this.usuario = {
      nombre: "string",
      apellido: "string"
    }


    console.log(this.usuario);

    this.form.setValue({
      txtNombre: this.usuario.nombre,
      txtApellido: this.usuario.apellido
    });
    */

    //this.form = Object.assign({}, this.usuario);

    //this.form.controls.txtNombre.setValue({
    //  txtNombre: 'abc'
    //});
  }

  /*
  onKeydown(event, elm: HTMLInputElement) {
    if (event.key === "Enter") {
      console.log(event);
      console.log(elm);
      this.setFocus(elm);
    }
  }
  */
}
