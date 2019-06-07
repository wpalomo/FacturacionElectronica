import { Component, Input, OnInit, ViewChild, ViewEncapsulation, Output, EventEmitter } from '@angular/core';
import { LoginService } from '../../services/login/login.service';
import { Observable } from 'rxjs';
import { Router } from '@angular/router';

import ISesion from './../../model/ISesion';

@Component({
    selector: 'app-layout',
    /*templateUrl: './app-layout.component.html',*/
    template: `
        <div class="f-column">
            <div class="main-header f-row" *ngIf="isLoggedIn$ | async as isLoggedIn">
                <div class="f-row f-full">
                    <div class="main-title f-animate f-row" [style.width.px]="width">
                        <img class="app-logo" src="https://www.jeasyui.com/favicon.ico">
                        <span *ngIf="!collapsed">{{title}}</span>
                    </div>
                    <div class="main-bar f-full">
                        <span class="main-toggle fa fa-bars" (click)="toggle()"></span>
                    </div>
                    <div class="main-bar f-full">
                        <span>USUARIO: {{ sesion.apellido_nombre }} -> {{ sesion.descripcion_perfil }}</span>
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-default" (click)="logout()">
                            <span class="btn-label" aria-hidden="true">
                                <i class="fa fa-lock fa-lg"></i>
                            </span> Salir
                        </button>
                    </div>
                </div>
            </div>
            <div class="f-row f-full">
                <div class="sidebar-body f-animate" [style.width.px]="width" *ngIf="isLoggedIn$ | async as isLoggedIn">
                    <div *ngIf="!collapsed" class="sidebar-user">
                        Menu
                    </div>
                    <eui-sidemenu [data]="menus" [border]="false" [collapsed]="collapsed" (itemClick)="onItemClick($event)"></eui-sidemenu>
                </div>
                <div class="main-body f-full">
                    <ng-content> </ng-content>
                </div>
            </div>
        </div>
    `,
    styleUrls: ['./app-layout.component.css'],
    encapsulation: ViewEncapsulation.None
})
export class AppLayoutComponent implements OnInit {
    @Input() menus;
    @Input() sesion;
    @Input() title = null;
    @Output() itemClick = new EventEmitter();

    isLoggedIn$: Observable<boolean>;
    iSession$: Observable<any>;

    width = 260;
    collapsed = false;

    constructor(
        private loginService: LoginService,
        private router: Router
    ) { }

    ngOnInit() {
        this.isLoggedIn$ = this.loginService.isLoggedIn;
        this.iSession$ = this.loginService.getSesion;
    }

    toggle() {
        this.collapsed = !this.collapsed;
        this.width = this.collapsed ? 50 : 260;

        // console.log(this.iSession$.value);

        // alert(this.iSession$.);
        console.log(this.menus[0].text);
        console.log(this.sesion.apellido_nombre);
    }

    logout() {
        this.loginService.logout();
    }

    onItemClick(item) {
        this.itemClick.emit(item);

        // if (!this.collapsed) {
        // this.router.navigate([item.routerLink]);
        //    this.toggle();
        // }
    }
}
