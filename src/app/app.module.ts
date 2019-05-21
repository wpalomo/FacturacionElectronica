import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';

import { AppComponent } from './app.component';

import { AppRoutesModule } from './app-routes';
import { PrimeNGModule } from './png';
import { LoginComponent } from './components/login/login.component';
import { HomeComponent } from './components/home/home.component';

import { EncrDecrService } from './services/encrypt/encr-decr.service';
import { LoginService } from './services/login/login.service';
import { AuthGuard } from './guards/auth.guard';
import { MensajeGenericoComponent } from './common/mensaje-generico/mensaje-generico.component';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    HomeComponent,
    MensajeGenericoComponent
  ],
  imports: [
    BrowserModule,
    AppRoutesModule,
    BrowserAnimationsModule,
    PrimeNGModule,
    ReactiveFormsModule,
    FormsModule,
    HttpClientModule
  ],
  providers: [
    AuthGuard,
    LoginService,
    EncrDecrService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
