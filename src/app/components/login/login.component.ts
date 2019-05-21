import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LoginService } from './../../services/login/login.service';
import { EncrDecrService } from './../../services/encrypt/encr-decr.service';

import ILogin from '../../model/ILogin';

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
  title: string;

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

    const encrypted = this.encrDecr.set('123456$#@$^@1ERF', 'password@123456');
    const decrypted = this.encrDecr.get('123456$#@$^@1ERF', encrypted);

    console.log('Encrypted :' + encrypted);
    console.log('Encrypted :' + decrypted);

    // this.displayWait = true;
    this.title = 'Mensaje del Sistema';

    this.loginPC = localStorage.getItem('loginPC');
    this.clavePC = localStorage.getItem('clavePC');

    this.buildForm();

    if (this.loginPC) {
      this.login = this.loginPC;
      this.clave = this.clavePC;
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
    postData.append('login', this.login);
    postData.append('clave', this.clave);
    postData.append('action', 'login');

    // this.loginService.login(postData);

    this.loginService.login(postData).subscribe(
      data => {
        // alert('xxx');
        console.log(data);
        this.ilogin = data[0];
        console.log(this.ilogin);
        this.displayWait = false;

        // si login ok y check "recordar sesion" en 'on' entonces grabar datos en pc
        if (this.loginForm.get('chkRecordar').value) {
          localStorage.setItem('loginPC', this.login);
          localStorage.setItem('clavePC', this.clave);
        }
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
