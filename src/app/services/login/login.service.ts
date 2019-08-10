// TODO: Revisar bien sobre como pasar una variable u objeto observable
//       y corregirlo ya que se esta enviando la interfaz sesion desde una funcion con un get adelante
//       y otra sin el get (getSesion2) dejar solo una.

import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { Router } from '@angular/router';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';

import ILogin from './../../model/ILogin';
import ISesion from './../../model/ISesion';

@Injectable()

export class LoginService {
  private loggedIn: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);
  private isVisible: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(true);
  private menu: BehaviorSubject<any> = new BehaviorSubject<any>([]);
  private iSesion: BehaviorSubject<any> = new BehaviorSubject<any>({});
  // private iSession = new BehaviorSubject<ISesion>({});
  url = environment.baseUrl + 'login.php';
  urlMenu = environment.baseUrl + 'menu.php';

  constructor(
    private router: Router,
    private http: HttpClient
  ) { }

  get visible() {
    return this.isVisible.asObservable();
  }

  get isLoggedIn() {
    return this.loggedIn.asObservable();
  }

  get getMenus() {
    // return this.menu.value;
    return this.menu.asObservable();
  }

  get getSesion() {
    return this.iSesion.asObservable();
  }

  getSesion2() {
    return this.iSesion.asObservable();
  }

  login(postData): Observable<ISesion> {
    // console.log(this.url);
    // console.log(postData);
    // console.log(res);
    // alert(postData.get('login'));
    // alert(postData.get('clave'));
    // alert(postData.get('action'));
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          // console.log(this.url);
          // console.log(postData);
          // console.log(res);
          if (res.success) {
            if (res.ok === 'S') {
              this.loggedIn.next(true);
              this.isVisible.next(false);
              this.router.navigate(['/home']);
              this.iSesion.next(res.data[0]);
              return res.data as ISesion;
            } else {
              throw (res.mensaje);
            }
          } else {
            console.log('error');
            console.log('res.mensaje');
            throw (res.mensaje);
          }
        }),
        catchError(transformError)
      );
    /*
    alert(postData.get('login'));
    alert(postData.get('clave'));
    alert(postData.get('action'));
    this.loggedIn.next(true);
    this.router.navigate(['/home']);
    */


  }

  getMenu(postData) {
    return this.http.post<any>(this.urlMenu, postData)
      .pipe(
        map(res => {
          // alert(res.json().toString());
          console.log('xxx');
          // console.log(res.json());
          this.menu.next(res);
          console.log(this.menu.value);
          return res;
        }),
        catchError(transformError)
      );
  }

  logout() {
    this.loggedIn.next(false);
    this.router.navigate(['/login']);
  }
}
