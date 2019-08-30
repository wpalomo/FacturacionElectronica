import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LoginService } from './../../services/login/login.service';
import { EncrDecrService } from './../../services/encrypt/encr-decr.service';
import { BehaviorSubject } from 'rxjs';


import ILogin from '../../model/ILogin';
import ISesion from '../../model/ISesion';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  public errorMsg;
  displayError: boolean;
  displayWait: boolean;
  loginForm: FormGroup;
  login: string;
  clave: string;
  checked = false;
  ilogin: ILogin = {};
  iSesion: ISesion = {};
  title: string;
  key = 'IntimodaFE2019$#';

  private menux: BehaviorSubject<any> = new BehaviorSubject<any>([]);


  /* variables que tendran */
  loginPC = null;
  clavePC = null;

  constructor(
    private fb: FormBuilder,
    private loginService: LoginService,
    private encrDecr: EncrDecrService
  ) { }

  ngOnInit() {
    localStorage.removeItem('loginPC');
    localStorage.removeItem('clavePC');

    // const encrypted = this.encrDecr.set(this.key, 'password@123456');
    // const decrypted = this.encrDecr.get(this.key, encrypted);

    // console.log('Encrypted :' + encrypted);
    // console.log('Encrypted :' + decrypted);

    // this.displayWait = true;
    this.title = 'Mensaje del Sistema';

    this.loginPC = localStorage.getItem('loginPC');
    this.clavePC = localStorage.getItem('clavePC');
    this.buildForm();

    if (this.loginPC) {
      this.login = this.loginPC;
      this.clave = this.encrDecr.get(this.key, this.clavePC);
      this.callService();
    }
  }

  buildForm() {
    this.loginForm = this.fb.group({
      txtUsuario: ['', Validators.required],
      txtClave: ['', Validators.required],
      chkRecordar: [false]
    });
  }

  onSubmit() {
    this.displayError = false;
    this.displayWait = true;

    if (this.loginForm.valid) {
      console.log('submit');
      this.login = this.loginForm.get('txtUsuario').value;
      this.clave = this.loginForm.get('txtClave').value;
      this.callService();
      // this.loginService.login();
    }
  }

  callService() {
    const postData = new FormData();
    const postData2 = new FormData();

    // alert(this.encrDecr.set(this.key, this.clave));
    // alert(this.encrDecr.get(this.key, this.encrDecr.set(this.key, this.clave)));

    this.clave = this.encrDecr.set(this.key, this.clave);

    postData.append('login', this.login);
    postData.append('clave', this.clave);
    postData.append('action', 'login');

    // this.loginService.login(postData);

    this.loginService.login(postData).subscribe(
      data => {
        //alert('1');
        console.log(data);
        this.iSesion = data[0];
        console.log(this.iSesion);
        this.displayWait = false;

        console.log(this.iSesion.descripcion_perfil);

        // si login ok y check "recordar sesion" en 'on' entonces grabar datos en pc
        if (this.loginForm.get('chkRecordar').value) {
          localStorage.setItem('loginPC', this.login);
          localStorage.setItem('clavePC', this.clave);
        }

        setTimeout(() => {
          //alert('2');
        }, 5000);

        //alert('3');
        console.log('3');
        console.log(this.iSesion);
        console.log(this.iSesion.id_usuario);

        postData2.append('id_usuario', this.iSesion.id_usuario.toString());
        postData2.append('action', 'getMenuUsuario');

        this.loginService.getMenu(postData2).subscribe(
          data2 => {
            //alert('99');
            console.log('99');
            console.log(data2);
          }, error => {
            this.errorMsg = error;
            console.log(this.errorMsg);

            this.displayWait = false;
            this.displayError = true;
          }
        );
      },
      error => {
        this.errorMsg = error;
        console.log(this.errorMsg);

        this.displayWait = false;
        this.displayError = true;
      }
    );


  }
}
