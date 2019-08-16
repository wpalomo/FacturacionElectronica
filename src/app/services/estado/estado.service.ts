import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';

import IEstados from '../../model/IEstados';

@Injectable({
  providedIn: 'root'
})
export class EstadoService {

  constructor(
    private http: HttpClient
  ) { }

  getEstados() {
    return this.http.get<any>('/assets/data/estados.json')
      .pipe(
        map(res => res.data as IEstados[])
      );
  }
}
