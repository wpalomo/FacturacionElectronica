import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { Router } from '@angular/router';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';

import ILogin from './../../model/ILogin';


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

  login(postData): Observable<ILogin> {
    // console.log(this.url);
    // console.log(postData);
    // console.log(res);
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          // console.log(this.url);
          // console.log(postData);
          // console.log(res);
          if (res.success) {
            // console.log(res);
            // console.log(res.data);
            this.loggedIn.next(true);
            this.router.navigate(['/home']);
            return res.data as ILogin;
          } else {
            console.log('error');
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
