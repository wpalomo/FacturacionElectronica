import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { BehaviorSubject } from 'rxjs';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';

import ITB_GEN_USUARIOS from '../../model/ITB_GEN_USUARIOS';

@Injectable({
  providedIn: 'root'
})
export class MantenimientoUsuarioService {
  url = environment.baseUrl + 'usuarios.php';
  private totalRecords: BehaviorSubject<number> = new BehaviorSubject<number>(0);

  constructor(
    private http: HttpClient
  ) { }

  getTotalRecords() {
    return this.totalRecords.asObservable();
  }

  getUsuarios(postData): Observable<ITB_GEN_USUARIOS[]> {
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          if (res.success) {
            //alert('fddddd');
            //if (res.ok === 'S') {
            //  alert(res);
            this.totalRecords.next(res.total);
            return res.data as ITB_GEN_USUARIOS[];
            //} else {
            //  throw (res.mensaje);
            //}
          } else {
            console.log('error');
            console.log('res.mensaje');
            throw (res.mensaje);
          }
        }),
        catchError(transformError)
      );
  }

  insert(postData): any {
    console.log(postData);
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          if (res.success) {
            if (res.ok === 'S') {
              //alert(res);
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
