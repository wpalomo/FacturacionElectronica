import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { MantenimientoPerfilService } from '../../../services/mantenimiento-perfil/mantenimiento-perfil.service';
import { MenuService } from '../../../services/menu/menu.service';
import IEstados from 'src/app/model/IEstados';
import ICombo from 'src/app/model/ICombo';
import { TreeNode } from 'primeng/api';

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

  checkboxSelectionTree: TreeNode[];
  selectMultiplePlaces: TreeNode;

  dataArray: string[] = [];
  selectedFiles: TreeNode[] = [];

  constructor(
    private mantenimientoPerfilService: MantenimientoPerfilService,
    private menuService: MenuService,
  ) { }

  ngOnInit() {
    this.cargarPerfiles();
    this.menuService.getTouristPlaces().subscribe((data: any) => {
      console.log(data);
      this.checkboxSelectionTree = data
      this.expandAll();

      this.selectedFiles = [
        {
          data: "4",
          label: "Mantenimiento de Usuarios",
          icon: "fa fa-users"
        }
      ]

      //this.dataArray = ["4", "6"];
      //this.checkNode(this.checkboxSelectionTree, this.dataArray);
    });
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

  onClickBtnPerfil(selectMultiplePlaces) {
    console.log(selectMultiplePlaces);
    //console.log(JSON.stringify(selectMultiplePlaces));
    selectMultiplePlaces.forEach(node => {
      console.log(node.label, node.data);
    });
  }

  expandAll() {
    this.checkboxSelectionTree.forEach(node => {
      this.expandRecursive(node, true);
    });
  }

  nodeSelect(event) {
    //event.node = selected node
  }

  nodeUnselect(event) {

  }

  private expandRecursive(node: TreeNode, isExpand: boolean) {
    node.expanded = isExpand;
    if (node.children) {
      node.children.forEach(childNode => {
        this.expandRecursive(childNode, isExpand);
      });
    }
  }

  checkNode(nodes: TreeNode[], str: string[]) {
    console.log('in check node');
    for (let i = 0; i < nodes.length; i++) {
      if (!nodes[i].leaf) {
        for (let j = 0; j < nodes[i].children.length; j++) {
          if (str.includes(nodes[i].children[j].data)) {
            if (!this.selectedFiles.includes(nodes[i].children[j])) {
              this.selectedFiles.push(nodes[i].children[j]);
            }
          }
        }
      } else {
        if (str.includes(nodes[i].data)) {
          if (!this.selectedFiles.includes(nodes[i])) {
            this.selectedFiles.push(nodes[i]);
          }
        }
      }
      if (nodes[i].leaf) {
        continue;
      } else {
        this.checkNode(nodes[i].children, str);
        const count = nodes[i].children.length;
        let c = 0;
        for (let j = 0; j < nodes[i].children.length; j++) {
          if (this.selectedFiles.includes(nodes[i].children[j])) {
            c++;
          }
          if (nodes[i].children[j].partialSelected) {
            nodes[i].partialSelected = true;
          }
        }
        if (c === 0) { } else if (c === count) {
          nodes[i].partialSelected = false;
          if (!this.selectedFiles.includes(nodes[i])) {
            this.selectedFiles.push(nodes[i]);
            console.log(this.selectedFiles);
          }
        } else {
          nodes[i].partialSelected = true;
        }
      }
    }
  }
}
/*  checkNode(nodes: TreeNode[], str: string[]) {
    for (let i = 0; i < nodes.length; i++) {
      if (!nodes[i].leaf && nodes[i].children[0].leaf) {
        for (let j = 0; j < nodes[i].children.length; j++) {
          if (str.includes(nodes[i].children[j].data)) {
            if (!this.selectedFiles.includes(nodes[i].children[j])) {
              this.selectedFiles.push(nodes[i].children[j]);
            }
          }
        }
      }
      if (nodes[i].leaf) {
        return;
      }
      this.checkNode(nodes[i].children, str);
      let count = nodes[i].children.length;
      let c = 0;
      for (let j = 0; j < nodes[i].children.length; j++) {
        if (this.selectedFiles.includes(nodes[i].children[j])) {
          c++;
        }
        if (nodes[i].children[j].partialSelected) nodes[i].partialSelected = true;
      }
      if (c == 0) { }
      else if (c == count) {
        nodes[i].partialSelected = false;
        if (!this.selectedFiles.includes(nodes[i])) {
          this.selectedFiles.push(nodes[i]);
        }
      }
      else {
        nodes[i].partialSelected = true;
      }
    }
  }
}
*/