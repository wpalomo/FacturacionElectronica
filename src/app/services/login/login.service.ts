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
  url = environment.baseUrl + 'login.php';

  constructor(
    private router: Router,
    private http: HttpClient
  ) { }

  get isLoggedIn() {
    return this.loggedIn.asObservable();
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
              this.router.navigate(['/home']);
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

  logout() {
    this.loggedIn.next(false);
    this.router.navigate(['/login']);
  }
}
