import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, catchError } from 'rxjs/operators';

import ITB_GEN_FAVORITOS from '../../model/ITB_GEN_FAVORITOS';


@Injectable({
  providedIn: 'root'
})
export class MenuFavoritosService {

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
}
