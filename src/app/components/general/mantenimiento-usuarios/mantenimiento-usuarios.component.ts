import { Component, OnInit } from '@angular/core';
import { LazyLoadEvent, Message } from 'primeng/components/common/api';
import { ConfirmationService } from 'primeng/api';

import { Observable } from 'rxjs';
import { MantenimientoUsuarioService } from '../../../services/mantenimiento-usuario/mantenimiento-usuario.service';

@Component({
  selector: 'app-mantenimiento-usuarios',
  templateUrl: './mantenimiento-usuarios.component.html',
  styleUrls: ['./mantenimiento-usuarios.component.css']
})
export class MantenimientoUsuariosComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
