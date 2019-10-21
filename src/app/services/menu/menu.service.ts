import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
//import 'rxjs/add/operator/map';
import { environment } from '../../../environments/environment';
import { transformError } from '../../common/common';
//import { TreeNode } from '../../components/common/api';
import { TreeNode } from 'primeng/api';


@Injectable({
  providedIn: 'root'
})
export class MenuService {
  url = environment.baseUrl + 'menuFavoritos.php';

  constructor(
    private http: HttpClient,
  ) { }

  getTouristPlaces(): Observable<any[]> {
    return this.http.get<any>('/assets/data/menu.json')
      .pipe(
        map(res => res.data)
      );

    //.map(response => response.json().data);
  }


}
