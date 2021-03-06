import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { BehaviorSubject } from 'rxjs';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';

//import { Injectable } from '@angular/core';
//import { BehaviorSubject } from 'rxjs';
import { Router } from '@angular/router';
//import { HttpClient, HttpErrorResponse } from '@angular/common/http';
//import { Observable, throwError } from 'rxjs';
//i//mport { map, catchError } from 'rxjs/operators';
//i//mport { environment } from '../../../environments/environment';
//import { transformError } from '../../common/common';

import ITB_GEN_PERFILES from '../../model/ITB_GEN_PERFILES';
import ICombo from '../../model/ICombo';


@Injectable({
  providedIn: 'root'
})
export class MantenimientoPerfilService {
  // TODO: que la ruta en php la traiga en variable y no quemada en codigo.
  url = environment.baseUrl + 'perfiles.php';
  private totalRecords: BehaviorSubject<number> = new BehaviorSubject<number>(0);
  private perfilesCombo: BehaviorSubject<ICombo[]> = new BehaviorSubject<ICombo[]>([]);

  constructor(
    private http: HttpClient,
    private router: Router
  ) { }

  get getPerfilesCombos() {
    return this.perfilesCombo.asObservable();
  }

  getTotalRecords() {
    return this.totalRecords.asObservable();
  }
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

  //getFormasPago(): Observable<IFormasPago[]> {
  getPerfiles2(postData): Observable<ITB_GEN_PERFILES[]> {

    //console.log('event.first: ' + postData.start);
    //console.log('event.rows: ' + event.rows);
    //console.log('event.sortField: ' + event.sortField);
    //console.log('event.sortOrder: ' + event.sortOrder);
    //console.log('event.filters: ' + event.filters);


    /*
    postData.append('start', event.first.toString());
    postData.append('limit', event.rows.toString());
    postData.append('sortField', event.sortField);
    postData.append('sortOrder', event.sortOrder.toString());
    postData.append('filters', event.filters.toString())
    postData.append('action', 'Q');
    */

    /*
    return this.http.get<any>(environment.baseUrl)
          .pipe(
            map(res => {
              if (res.success) {
                return res.data as IFormasPago[];
              } else {
    */

    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          if (res.success) {
            //alert('fddddd');
            //if (res.ok === 'S') {
            //  alert(res);
            this.totalRecords.next(res.total);
            return res.data as ITB_GEN_PERFILES[];
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

  getPerfilesxEstado(postData): Observable<ITB_GEN_PERFILES[]> {
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          if (res.success) {
            this.totalRecords.next(res.total);
            return res.data as ITB_GEN_PERFILES[];
          } else {
            console.log('error');
            console.log('res.mensaje');
            throw (res.mensaje);
          }
        }),
        catchError(transformError)
      );
  }

  getPerfilesxEstadoToCombo(postData): Observable<ICombo[]> {
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          if (res.success) {
            //return res.data as ICombo[];
            let perfilesActivos: ICombo[] = [];
            res.data.forEach(d => {
              //console.log(d.id_perfil);

              perfilesActivos.push({ label: d.descripcion_perfil, value: d.id_perfil });
            });

            //console.log(perfilesActivos);
            this.perfilesCombo.next(perfilesActivos);
            return perfilesActivos as ICombo[];
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
