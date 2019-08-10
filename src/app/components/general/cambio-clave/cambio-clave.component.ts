// TODO: cuando se envia un campo vacio php responde correctamente, pero desde angular no se esta
//       presentando el mensaje correctamente.

import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LoginService } from './../../../services/login/login.service';
import { CambioClaveService } from './../../../services/cambio-clave/cambio-clave.service';
import { EncrDecrService } from './../../../services/encrypt/encr-decr.service';

@Component({
  selector: 'app-cambio-clave',
  templateUrl: './cambio-clave.component.html',
  styleUrls: ['./cambio-clave.component.css']
})
export class CambioClaveComponent implements OnInit {
  public errorMsg;
  displayMensaje: boolean;
  tipoMensaje: string;
  form: FormGroup;
  clave: string;
  claveNueva: string;
  displayWait: boolean;
  title: string;
  key = 'IntimodaFE2019$#'; // TODO: poner el key dentro del enviroment


  constructor(
    private fb: FormBuilder,
    private loginService: LoginService,
    private cambioClaveService: CambioClaveService,
    private encrDecr: EncrDecrService
  ) { }

  ngOnInit() {
    this.title = 'Mensaje del Sistema';
    this.buildForm();
  }

  buildForm() {
    // TODO: no olvidar poner mas validators a los campos, y su respectivo mensaje controlar la
    //       longitud de la cadena para que coincida con la de la tabla
    this.form = this.fb.group({
      txtClave: ['', Validators.required],
      txtClaveNueva: ['', Validators.required]
    });
  }

  onSubmit() {
    if (this.form.valid) {
      this.displayMensaje = false;
      this.displayWait = true;
      console.log('submit cambio-clave');
      this.callService();
    }
  }

  callService() {
    const postData = new FormData();
    let idUsuario: string;
    let login: string;

    this.loginService.getSesion2().subscribe(
      data => {
        console.log('camboya 2');
        console.log(data);
        console.log(data.login);
        console.log(data.id_sesion);
        console.log(data.id_usuario);

        idUsuario = data.id_usuario;
        login = data.login;
      }
    );

    postData.append('id_usuario', idUsuario);
    postData.append('login', login);
    postData.append('clave', this.encrDecr.set(this.key, this.form.get('txtClave').value));
    postData.append('clave_nueva', this.encrDecr.set(this.key, this.form.get('txtClaveNueva').value));
    postData.append('action', 'cambioClave');

    this.cambioClaveService.cambioClave(postData).subscribe(
      data => {
        this.displayWait = false;
        this.tipoMensaje = 'OK';
        this.displayMensaje = true;
        this.errorMsg = data.mensaje;
        // alert(data.mensaje);
      },
      error => {
        this.displayWait = false;
        this.errorMsg = error;
        console.log(this.errorMsg);

        this.displayWait = false;
        this.displayMensaje = true;
        this.tipoMensaje = 'ERROR';
      }
    );
  }
}
