import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';

import IResponse from './../../model/IResponse';

@Injectable({
  providedIn: 'root'
})
export class CambioClaveService {
  // TODO: que la ruta en php la traiga en variable y no quemada en codigo.
  url = environment.baseUrl + 'cambioClave.php';

  constructor(
    private http: HttpClient
  ) { }

  cambioClave(postData): any {
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          if (res.success) {
            if (res.ok === 'S') {
              alert(res);
              return res;
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
  }
}
