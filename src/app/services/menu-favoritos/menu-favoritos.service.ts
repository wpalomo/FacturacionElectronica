import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';

import ITB_GEN_FAVORITOS from '../../model/ITB_GEN_FAVORITOS';


@Injectable({
  providedIn: 'root'
})
export class MenuFavoritosService {
  url = environment.baseUrl + 'menuFavoritos.php';

  constructor(
    private http: HttpClient,
  ) { }

  getFavoritos(): Observable<ITB_GEN_FAVORITOS[]> {
    //console.log(event.first);
    //console.log(event.rows);
    //console.log(event.sortField);
    //console.log(event.sortOrder);
    //console.log(event.filters);


    return this.http.get<any>('/assets/data/TB_GEN_FAVORITOS.json')
      .pipe(
        map(res => res.data as ITB_GEN_FAVORITOS[])
      );
  }

  getMenuFavoritos(postData): Observable<ITB_GEN_FAVORITOS[]> {
    return this.http.post<any>(this.url, postData)
      .pipe(
        map(res => {
          if (res.success) {
            console.log(res.data);
            return res.data as ITB_GEN_FAVORITOS[];
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
