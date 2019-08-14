import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';

import ITB_GEN_PERFILES from '../../model/ITB_GEN_PERFILES';

@Injectable({
  providedIn: 'root'
})
export class MantenimientoPerfilService {
  // TODO: que la ruta en php la traiga en variable y no quemada en codigo.
  url = environment.baseUrl + 'perfiles.php';

  constructor(
    private http: HttpClient
  ) { }

  getPerfiles(event): Observable<ITB_GEN_PERFILES[]> {
    console.log(event.first);
    console.log(event.rows);
    console.log(event.sortField);
    console.log(event.sortOrder);
    console.log(event.filters);


    return this.http.get<any>('/assets/data/TB_GEN_PERFILES.json')
      .pipe(
        map(res => res.data as ITB_GEN_PERFILES[])
      );
  }

  getPerfiles2(event, postData): any {
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          if (res.success) {
            alert('fddddd');
            //if (res.ok === 'S') {
            //  alert(res);
            return res;
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
}
