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

  getEstadosActivos() {
    return this.http.get<any>('/assets/data/estados.json')
      .pipe(
        map(res => {
          let estados = [];

          res.data.forEach(element => {
            //alert('eewww');
            console.log('ewwww');
            console.log(element);
            if (element.value !== 'T') {
              if (element.value !== 'X') {
                //    console.log(element);
                estados.push(element);
              }
            }
          });

          console.log(estados);
          return estados;
        })
      );

    /*.pipe(
      map(res => {
        let estados: any[];
        res.data.forEach(element => {
          //alert('99');
          //alert(element.label);
          //alert(element.value);
          if (element.value !== 'T') {
            if (element.value !== 'X') {
              estados.push(element);
            }
          });
      }
      return estados;
      )
    );*/
  }
}
