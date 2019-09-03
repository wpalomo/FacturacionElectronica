import { Component, OnInit, ViewChild, ElementRef, Renderer2 } from '@angular/core';
import { LazyLoadEvent, Message } from 'primeng/components/common/api';
<<<<<<< HEAD
=======
import { ConfirmationService } from 'primeng/api';

>>>>>>> 4f6327e1050c7c0b02590e9ca962bd9ed901fc12
//import { ConfirmationService } from 'primeng/api';
import { Observable } from 'rxjs';
import { MantenimientoPerfilService } from '../../../services/mantenimiento-perfil/mantenimiento-perfil.service';
import { EstadoService } from '../../../services/estado/estado.service';
import ITB_GEN_PERFILES from '../../../model/ITB_GEN_PERFILES';
import IEstados from '../../../model/IEstados';
import { Table } from 'primeng/table';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';

@Component({
  selector: 'app-mantenimiento-perfil',
  templateUrl: './mantenimiento-perfil.component.html',
  styleUrls: ['./mantenimiento-perfil.component.css'],
  //providers: [ConfirmationService]
})
export class MantenimientoPerfilComponent implements OnInit {
  @ViewChild('dt') dt: any;
  filtering() {
    //alert('fffff');
    this.dt.reset();
  }

  @ViewChild("txtElement") nameField: ElementRef;
  //@ViewChild("txtDescripcion") nameDescripcion: ElementRef;



  //editName(): void {
  //  //alert('fdefeeeeeeeeeeeeeee');
  //  this.nameField.nativeElement.focus();
  //  document.getElementById('descripcion_perfil').focus();
  //}

  displayDialog: boolean;
  displayWait: boolean;
  perfiles: ITB_GEN_PERFILES[];
  perfil: ITB_GEN_PERFILES = {};
  cols: any[];
  nuevoRegistro: boolean;
  disabled: boolean = true;
  hiddenButtonDelete: boolean;
  estados: IEstados[];
  estadosActivos: IEstados[];
  grades: IEstados[];
  errorMsg;
  displayMensaje: boolean;
  tipoMensaje: string;
  first = 0;
  selectedEstadoFilter: any;
  selectedMultipleEstadoFilter: any;
  selectedEstado: any;
  tipoOperacion: string = "";
  auxEvent: LazyLoadEvent;
  txtDescripcion: string;
  txtIdPerfil: string;
  msgs: Message[] = [];

  totalRecords$: Observable<number>;

  constructor(
    private mantenimientoPerfilService: MantenimientoPerfilService,
    private estadoService: EstadoService,
    public renderer: Renderer2,
<<<<<<< HEAD
    //private confirmationService: ConfirmationService
=======
    private confirmationService: ConfirmationService
>>>>>>> 4f6327e1050c7c0b02590e9ca962bd9ed901fc12
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



    //this.estadoService.getEstadosActivos().subscribe();

    //this.grades = [];
    //this.grades.push({ label: 'ACTIVO', value: 'ACTIVO' });
    //this.grades.push({ label: 'INACTIVO', value: 'INACTIVO' });

    this.inicializarPantalla();

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

    //this.nameDescripcion.nativeElement.value = 'JPABLOS';
    //this.nameDescripcion.nativeElement.focus();
  }

  inicializarPantalla() {
    this.txtDescripcion = '';
    this.txtIdPerfil = '';

    this.estadoService.getEstados().subscribe(
      data => {
        this.estados = data;
        //this.selectedEstadoFilter = { label: "ACTIVO", value: "A" };
      }
    );

    this.estadoService.getEstadosActivos().subscribe(
      data => {
        this.estadosActivos = data;
        //this.selectedMultipleEstadoFilter = { label: "ACTIVO", value: "A" };
      }
    );

    //this.estados = { label: "ACTIVO", value: "A" };
    //this.selectedEstado = { label: "ACTIVO", value: "A" };



    this.dt.reset();
    this.selectedEstadoFilter = { label: "TODOS", value: "T" };
    //this.selectedEstadoFilter = { value: "I" };
    //alert(this.selectedEstadoFilter);
    //this.estados.value 
  }

  loadLazy(event: LazyLoadEvent) {
    //alert(this.first);
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
    postData.append('action', 'getPerfiles');

    //this.mantenimientoPerfilService.getPerfiles(event).subscribe(
    //  data => {
    //    this.perfiles = data;
    //    console.log(this.perfiles);
    //  }
    //);


    this.mantenimientoPerfilService.getPerfiles2(postData).subscribe(
      data => {
        //alert(data);

        this.totalRecords$ = this.mantenimientoPerfilService.getTotalRecords();
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

  reset() {
    this.first = 0;
  }

  modificarRegistro(perfil: ITB_GEN_PERFILES) {
    this.tipoOperacion = 'U';
    this.nuevoRegistro = false;
    //alert(perfil.id_perfil);
    this.perfil = this.cloneRegistro(perfil);
    //alert(this.perfil.descripcion_perfil);
    this.displayDialog = true;
    this.hiddenButtonDelete = false;
    //this.selectedEstado.value = "A";
    //this.selectedEstado.label = "ACTIVO";
    //this.estados.v
    //this.selectedEstado = "A";
    //this.
    this.selectedEstado = { label: perfil.descripcion_estado_perfil, value: perfil.estado_perfil }
  }

  showDialogToAdd() {
    // let paginas;
    // //this.first = 17;
    // //alert(this.dt.rows);
    // console.log(this.totalRecords$);
<<<<<<< HEAD

    // //this.setCurrentPage(5);

    // this.totalRecords$.forEach(element => {
    //   console.log('x');
    //   console.log(element);
    //   console.log('x2');
    // });

    // this.totalRecords$.subscribe

    // console.log('-------------------');
    // this.totalRecords$.subscribe(num => {
    //   /*
    //   paginas = Math.floor(num / this.dt.rows);
    //   let residuo = num % this.dt.rows;
    //   console.log('paginas: ', paginas);
    //   console.log('residuo: ', residuo);

    //   if (residuo > 0) {
    //     paginas = paginas + 1
    //   }
    //   */

    //   paginas = num - 1;
    //   console.log('paginas: ', paginas);
    // }
    //   //this.stopwatchValue = num
    //   //console.log(num)
    //   //const paginas 
    // );
    // console.log('-------------------');

=======

    // //this.setCurrentPage(5);

    // this.totalRecords$.forEach(element => {
    //   console.log('x');
    //   console.log(element);
    //   console.log('x2');
    // });

    // this.totalRecords$.subscribe

    // console.log('-------------------');
    // this.totalRecords$.subscribe(num => {
    //   /*
    //   paginas = Math.floor(num / this.dt.rows);
    //   let residuo = num % this.dt.rows;
    //   console.log('paginas: ', paginas);
    //   console.log('residuo: ', residuo);

    //   if (residuo > 0) {
    //     paginas = paginas + 1
    //   }
    //   */

    //   paginas = num - 1;
    //   console.log('paginas: ', paginas);
    // }
    //   //this.stopwatchValue = num
    //   //console.log(num)
    //   //const paginas 
    // );
    // console.log('-------------------');

>>>>>>> 4f6327e1050c7c0b02590e9ca962bd9ed901fc12
    // this.totalRecords$.forEach(function (element) {
    //   console.log(element);
    // });

    // console.log('-------------------');
    // for (const [key, value] of Object.entries(this.totalRecords$)) {
    //   console.log(key);
    // }
    // console.log('-------------------');

    // //alert(this.dt.records);

    // //this.first = paginas;
    // //alert(paginas);
    // this.first = paginas;
    // this.loadLazy({ 'first': paginas, 'rows': 3, 'sortField': null, 'sortOrder': 1, 'filters': {} });



    //this.first = 

    //{first: 3, rows: 3, sortField: null, sortOrder: 1, filters: {…}, …}


    //this.totalRecords$.next().value
    //mapIter.next().value

    //this.totalRecords$.forEach(function callback(currentValue, index, array) {
    // tu iterador
    //});

    //this.totalRecords$.f

    //this.totalRecords$.forEach(element, function(value, key) { ... });

    //angular.forEach(this.totalRecords$, function(value, key) { ... })

    /*    forEach(this.totalRecords$, function (value, index) {
          // `this` will reference myArray: []
      }, myArray);*/

    //const element = this.renderer.selectRootElement('#myInput');
    this.tipoOperacion = 'I';
    this.nuevoRegistro = true;
    this.displayDialog = true;
    this.hiddenButtonDelete = true;
    this.perfil = {};
    //this.editName()
    //document.getElementById("descripcion_perfil").focus();
    //input.setFocus();
    this.selectedEstado = { label: "ACTIVO", value: "A" };
    //setTimeout(() => element.focus(), 0);

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

  onChange(event) {
    //alert('onChange');
    //alert('event :' + event);
    //alert(event.value);
    //alert(this.selectedEstado.label);
    //alert(this.selectedEstado.value);
    //this.first = 0;
  }

  setFocus(elm: HTMLInputElement) {
    setTimeout(() => {
      elm.focus()
    }, 500);
  }

  onKeydown(event, elm: HTMLInputElement) {
    if (event.key === "Enter") {
      console.log(event);
      this.setFocus(elm);
    }
  }

  onDialogClose(event, tipo) {
    //alert('close dialog');
    this.displayMensaje = event;
    //this.setFocus(this.nameField.nativeElement);
    if (tipo === 'F') {
      //this.setFocus(elm);
      this.setFocus(this.nameField.nativeElement);
    }
  }

  save() {
    this.displayMensaje = false;
    //alert('grabando');
    this.perfil.estado_perfil = this.selectedEstado.value;
    this.perfil.descripcion_estado_perfil = this.selectedEstado.label;
    //alert(this.perfil.id_perfil);
    //alert(this.perfil.descripcion_perfil);
    //alert(this.perfil.estado_perfil);

    //if (this.tipoOperacion === 'U' && this.perfil.id_perfil is undefined)
    if (this.tipoOperacion == 'U' && !(this.perfil.id_perfil)) {
      //alert('error en el id del perfil');
      return;
    }

    if (!(this.perfil.descripcion_perfil)) {
      //alert('error en la descripcion');
      this.displayMensaje = true;
      this.tipoMensaje = 'ERROR';
      this.errorMsg = 'Debe ingresar la descripcion del Perfil';
      //let element: HTMLInputElement = "#txtElement";
      this.setFocus(this.nameField.nativeElement);
      this.nameField.nativeElement.focus();
      return;
    }

    console.log(this.perfil);
    this.callService();

  }

  delete() {
<<<<<<< HEAD
=======
    this.confirmationService.confirm({
      message: 'Esta seguro que desea eliminar este registro ?',
      header: 'Confirmacion',
      icon: 'pi pi-info-circle',
      accept: () => {
        //alert('accpet');
        //console.log(this.perfil);
        //this.msgs = [{ severity: 'info', summary: 'Confirmed', detail: 'Record deleted' }];
        this.tipoOperacion = 'D';
        this.callService();
      },
      reject: () => {
        alert('regect');
        //this.msgs = [{ severity: 'info', summary: 'Rejected', detail: 'You have rejected' }];
      }
    });


>>>>>>> 4f6327e1050c7c0b02590e9ca962bd9ed901fc12
    // this.confirmationService.confirm({
    //   message: 'Do you want to delete this record?',
    //   header: 'Delete Confirmation',
    //   icon: 'pi pi-info-circle',
    //   accept: () => {
    //     this.msgs = [{ severity: 'info', summary: 'Confirmed', detail: 'Record deleted' }];
    //   },
    //   reject: () => {
    //     this.msgs = [{ severity: 'info', summary: 'Rejected', detail: 'You have rejected' }];
    //   }
    // });
  }

  callService() {
    const postData = new FormData();
    let action: string;
    let paginas;

    this.displayWait = true;

    //action = this.tipoOperacion === 'I' ? 'insert' : 'update';

    switch (this.tipoOperacion) {
      case 'I':
        action = 'insert';
        break;
      case 'U':
        action = 'update';
        break;
      case 'D':
        action = 'delete';
        break;
    }

    postData.append('perfil', JSON.stringify(this.perfil));
    postData.append('action', action);

    this.mantenimientoPerfilService.insert(postData).subscribe(
      data => {
        this.displayWait = false;
        this.tipoMensaje = 'OK';

        this.displayMensaje = true;
        this.errorMsg = data.mensaje;

        // if (this.tipoOperacion === 'U') {
        //   this.displayMensaje = true;
        //   this.tipoMensaje = 'OK';
        //   this.errorMsg = 'Se actualizo ';
        // }

        this.displayDialog = false;

        this.inicializarPantalla();
        //this.dt.reset();
        //this.loadLazy(this.auxEvent);
        //this.setPage();
        //this.first = 17;






        //this.loadLazy(this.auxEvent);
        //this.dt.reset();

        // alert(data.mensaje);
        //this.reset();
      },
      error => {
        this.displayWait = false;
        this.errorMsg = error;
        console.log(this.errorMsg);

        //this.displayWait = false;
        this.displayMensaje = true;
        this.tipoMensaje = 'ERROR';
      }
    );
  }

  setPage() {
    let paginas
    this.totalRecords$.subscribe(num => {
      /*
      paginas = Math.floor(num / this.dt.rows);
      let residuo = num % this.dt.rows;
      console.log('paginas: ', paginas);
      console.log('residuo: ', residuo);

      if (residuo > 0) {
        paginas = paginas + 1
      }
      */

      paginas = num - 1;
      console.log('paginas: ', paginas);
    })

    this.first = paginas;
    this.loadLazy({ 'first': paginas, 'rows': 3, 'sortField': null, 'sortOrder': 1, 'filters': {} });
  }

  setCurrentPage(n: number) {
    let paging = {
      first: ((n - 1) * this.dt.rows),
      rows: this.dt.rows
    };
    //this.paginate(paging);
  }

  paginate(event) {
    alert('xr');
    console.log(event);
    //event.first: Index of first record being displayed 
    //event.rows: Number of rows to display in new page 
    //event.page: Index of the new page 
    //event.pageCount: Total number of pages 
    //let pageIndex = event.first / event.rows + 1 // Index of the new page if event.page not defined.
  }
<<<<<<< HEAD
=======


>>>>>>> 4f6327e1050c7c0b02590e9ca962bd9ed901fc12
}
