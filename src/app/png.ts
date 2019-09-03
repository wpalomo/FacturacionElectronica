import { NgModule } from '@angular/core';

import { PanelModule } from 'primeng/panel';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { MessagesModule } from 'primeng/messages';
import { MessageModule } from 'primeng/message';
import { ToolbarModule } from 'primeng/toolbar';
import { MenubarModule } from 'primeng/menubar';
import { MenuModule } from 'primeng/menu';
import { SlideMenuModule } from 'primeng/slidemenu';
import { DropdownModule } from 'primeng/dropdown';
import { CalendarModule } from 'primeng/calendar';
import { TableModule } from 'primeng/table';
import { DialogModule } from 'primeng/dialog';
import { SplitButtonModule } from 'primeng/splitbutton';
import { CheckboxModule } from 'primeng/checkbox';
import { CardModule } from 'primeng/card';
import { ProgressBarModule } from 'primeng/progressbar';
import { MultiSelectModule } from 'primeng/multiselect';
import { KeyFilterModule } from 'primeng/keyfilter';
import { ProgressSpinnerModule } from 'primeng/progressspinner';
<<<<<<< HEAD
import { ConfirmDialogModule } from 'primeng/confirmdialog';
//import { ConfirmationService } from 'primeng/api';
=======
import {ConfirmDialogModule} from 'primeng/confirmdialog';
import {TabMenuModule} from 'primeng/tabmenu';
>>>>>>> 4f6327e1050c7c0b02590e9ca962bd9ed901fc12

@NgModule({
    imports: [
        PanelModule,
        ButtonModule,
        InputTextModule,
        MessagesModule,
        MessageModule,
        ToolbarModule,
        MenubarModule,
        SlideMenuModule,
        MenuModule,
        DropdownModule,
        CalendarModule,
        TableModule,
        DialogModule,
        SplitButtonModule,
        CheckboxModule,
        CardModule,
        ProgressBarModule,
        MultiSelectModule,
        KeyFilterModule,
        ProgressSpinnerModule,
        ConfirmDialogModule,
<<<<<<< HEAD
        //ConfirmationService
=======
        TabMenuModule
>>>>>>> 4f6327e1050c7c0b02590e9ca962bd9ed901fc12
    ],
    exports: [
        PanelModule,
        ButtonModule,
        InputTextModule,
        MessagesModule,
        MessageModule,
        ToolbarModule,
        MenubarModule,
        SlideMenuModule,
        MenuModule,
        DropdownModule,
        CalendarModule,
        TableModule,
        DialogModule,
        SplitButtonModule,
        CheckboxModule,
        CardModule,
        ProgressBarModule,
        MultiSelectModule,
        KeyFilterModule,
        ProgressSpinnerModule,
        ConfirmDialogModule,
<<<<<<< HEAD
        //ConfirmationService
=======
        TabMenuModule
>>>>>>> 4f6327e1050c7c0b02590e9ca962bd9ed901fc12
    ],
})
export class PrimeNGModule { }
