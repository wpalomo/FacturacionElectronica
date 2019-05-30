import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';

import { LoginComponent } from './components/login/login.component';
import { HomeComponent } from './components/home/home.component';
import { MantenimientoUsuariosComponent } from './components/general/mantenimiento-usuarios/mantenimiento-usuarios.component';
import { CambioClaveComponent } from './components/general/cambio-clave/cambio-clave.component';
import { FavoritosComponent } from './components/general/favoritos/favoritos.component';
import { MantenimientoPerfilComponent } from './components/general/mantenimiento-perfil/mantenimiento-perfil.component';
import { PermisosComponent } from './components/general/permisos/permisos.component';

import { AuthGuard } from './guards/auth.guard';

const appRoutes: Routes = [
    { path: '', component: LoginComponent },
    { path: 'home', component: HomeComponent, canActivate: [AuthGuard] },
    { path: 'mantenimiento-usuarios', component: MantenimientoUsuariosComponent, canActivate: [AuthGuard] },
    { path: 'cambio-clave', component: CambioClaveComponent, canActivate: [AuthGuard] },
    { path: 'favoritos', component: FavoritosComponent, canActivate: [AuthGuard] },
    { path: 'mantenimiento-perfil', component: MantenimientoPerfilComponent, canActivate: [AuthGuard] },
    { path: 'permisos', component: PermisosComponent, canActivate: [AuthGuard] },
    { path: '**', redirectTo: '' }
];

@NgModule({
    declarations: [],
    imports: [
        CommonModule,
        RouterModule.forRoot(appRoutes)
    ],
    exports: [
        RouterModule
    ]
})
export class AppRoutesModule { }
