import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { EasyUIModule } from 'ng-easyui/components/easyui/easyui.module';
//import { FlexLayoutModule } from '@angular/flex-layout'

import { AppComponent } from './app.component';

import { AppRoutesModule } from './app-routes';
import { PrimeNGModule } from './png';
import { LoginComponent } from './components/login/login.component';
import { HomeComponent } from './components/home/home.component';

import { EncrDecrService } from './services/encrypt/encr-decr.service';
import { LoginService } from './services/login/login.service';
import { MantenimientoPerfilService } from './services/mantenimiento-perfil/mantenimiento-perfil.service';
import { AuthGuard } from './guards/auth.guard';
import { MensajeGenericoComponent } from './common/mensaje-generico/mensaje-generico.component';
import { AppLayoutComponent } from './components/app-layout/app-layout.component';
import { MantenimientoUsuariosComponent } from './components/general/mantenimiento-usuarios/mantenimiento-usuarios.component';
import { CambioClaveComponent } from './components/general/cambio-clave/cambio-clave.component';
import { FavoritosComponent } from './components/general/favoritos/favoritos.component';
import { MantenimientoPerfilComponent } from './components/general/mantenimiento-perfil/mantenimiento-perfil.component';
import { PermisosComponent } from './components/general/permisos/permisos.component';
//import { ConfirmationService } from 'primeng/api';
//import { ConfirmationService } from 'primeng/components/common/api';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    HomeComponent,
    MensajeGenericoComponent,
    AppLayoutComponent,
    MantenimientoUsuariosComponent,
    CambioClaveComponent,
    FavoritosComponent,
    MantenimientoPerfilComponent,
    PermisosComponent
  ],
  imports: [
    BrowserModule,
    AppRoutesModule,
    BrowserAnimationsModule,
    PrimeNGModule,
    ReactiveFormsModule,
    FormsModule,
    HttpClientModule,
    EasyUIModule
    //FlexLayoutModule
  ],
  providers: [
    AuthGuard,
    LoginService,
    EncrDecrService,
    MantenimientoPerfilService,
    //ConfirmationService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
