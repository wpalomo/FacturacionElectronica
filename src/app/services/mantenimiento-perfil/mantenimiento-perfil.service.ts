import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';

import ITB_GEN_PERFILES from '../../model/ITB_GEN_PERFILES';

@Injectable({
  providedIn: 'root'
})
export class MantenimientoPerfilService {

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
}
