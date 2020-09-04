import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MaterialModule } from './theme/material';
import { FormsModule, ReactiveFormsModule } from "@angular/forms";
import { ServicesModule } from './services/services.module';

const requireModules = [
  CommonModule,
  MaterialModule,
  FormsModule,
  ReactiveFormsModule,
  ServicesModule
];
@NgModule({
  exports: [requireModules]
})


export class SharedModule { }
