import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-mensaje-generico',
  templateUrl: './mensaje-generico.component.html',
  styleUrls: ['./mensaje-generico.component.css']
})
export class MensajeGenericoComponent implements OnInit {
  @Input() title: string;
  @Input() errorMsg: string;
  @Input() tipoMensaje: string;
  displayMensaje: boolean;
  trueTipoMensaje: boolean;

  constructor() { }

  ngOnInit() {
    alert(this.tipoMensaje);
    this.displayMensaje = true;

    this.trueTipoMensaje = false;
    if (this.tipoMensaje === 'OK') {
      this.trueTipoMensaje = true;
    }
  }

  isTrueTipoMensaje(): boolean {
    // return this.price >= this.previousPrice;
    // alert(this.tipoMensaje);
    return this.tipoMensaje === 'OK';
  }
}
