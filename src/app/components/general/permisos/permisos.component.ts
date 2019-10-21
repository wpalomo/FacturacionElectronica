import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { MantenimientoPerfilService } from '../../../services/mantenimiento-perfil/mantenimiento-perfil.service';
import IEstados from 'src/app/model/IEstados';
import ICombo from 'src/app/model/ICombo';

@Component({
  selector: 'app-permisos',
  templateUrl: './permisos.component.html',
  styleUrls: ['./permisos.component.css']
})
export class PermisosComponent implements OnInit {
  @ViewChild("cmbPerfil1") cmbPerfil1Field: ElementRef;


  displayWait: boolean;
  errorMsg;
  displayMensaje: boolean;
  tipoMensaje: string;

  perfilesActivos: IEstados[];
  selectedPerfil: ICombo;

  constructor(
    private mantenimientoPerfilService: MantenimientoPerfilService,
  ) { }

  ngOnInit() {
    this.cargarPerfiles();
  }

  cargarPerfiles() {
    console.log('in cargarPerfiles');
    const postData = new FormData();
    postData.append('estado_perfil', 'A');
    postData.append('action', 'getPerfilesxEstado');

    this.displayWait = true;

    this.mantenimientoPerfilService.getPerfilesxEstado(postData).subscribe(
      data => {
        this.displayWait = false;
        //this.perfiles = data;
        console.log(data);
        console.log(data[0].id_perfil);
        console.log(data[0].descripcion_perfil);

        this.perfilesActivos = [];
        data.forEach(d => {
          console.log(d.id_perfil);

          this.perfilesActivos.push({ label: d.descripcion_perfil, value: d.id_perfil });
        });

        //if (this.tipoOperacion == 'I') {
        this.selectedPerfil = { label: data[1].descripcion_perfil, value: data[1].id_perfil };
        //this.cmbPerfil1Field.nativeElement.value(this.selectedPerfil);

        //document.getElementById('cmbPerfil1').
        //}

        //this.selectedPerfil = { label: this.perfilesActivos[3].label, value: this.perfilesActivos[3].value };

        //console.log('after then');
        //console.log(this.selectedPerfil);

        //this.tipoOperacion = 'I';
        //this.nuevoRegistro = true;
        //this.displayDialog = true;
        //this.hiddenButtonDelete = true;
        //this.usuario = {};
        //this.selectedEstado = { label: "ACTIVO", value: "A" };

        //this.buildForm();
        //this.setValidators();
      },
      error => {
        this.displayWait = false;
        this.errorMsg = error;
        this.displayMensaje = true;
        this.tipoMensaje = 'ERROR';
      }
    );

    return 99;
  }
}
