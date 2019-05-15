import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  loginForm: FormGroup;
  checked: boolean = false;

  constructor(
    private fb: FormBuilder
  ) { }

  ngOnInit() {
    this.buildForm();
  }

  buildForm() {
    this.loginForm = this.fb.group({
      txtUsuario: ['', Validators.required],
      txtClave: ['', Validators.required],
      chkRecordar: [false]
    });
  }

  onSubmit() {
    if (this.loginForm.valid) {
      alert('submit');
    }
  }
}
