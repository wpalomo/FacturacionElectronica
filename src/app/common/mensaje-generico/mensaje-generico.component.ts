import { Component, Input, Output, OnInit, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-mensaje-generico',
  templateUrl: './mensaje-generico.component.html',
  styleUrls: ['./mensaje-generico.component.css']
})
export class MensajeGenericoComponent implements OnInit {
  @Input() title: string;
  @Input() errorMsg: string;
  @Input() tipoMensaje: string;
  @Output() displayChange = new EventEmitter();
  displayMensaje: boolean;
  trueTipoMensaje: boolean;
  public stockClasses;

  constructor() { }

  ngOnInit() {
    //alert(this.tipoMensaje);
    this.displayMensaje = true;

    this.trueTipoMensaje = false;
    if (this.tipoMensaje === 'OK') {
      this.trueTipoMensaje = true;
    }

    this.stockClasses = {
      "my-panel-ok": this.trueTipoMensaje,
      "my-panel": !this.trueTipoMensaje
    };
  }

  isTrueTipoMensaje(): boolean {
    // return this.price >= this.previousPrice;
    // alert(this.tipoMensaje);
    //alert(this.tipoMensaje === 'OK');
    return this.tipoMensaje === 'OK';
  }

  getStyles() {

  }

  onClose() {
    //alert('xddddddddddddddd');
    this.displayChange.emit(false);
  }

  onHide(e: any) {
    //alert('hide');
    this.displayChange.emit(false);
  }

  // Work against memory leak if component is destroyed
  ngOnDestroy() {
    this.displayChange.unsubscribe();
  }
}
