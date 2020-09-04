import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";

import { HttpClientModule } from "@angular/common/http";
import { TodoService } from "./todo-service";
const Services = [TodoService];

@NgModule({
  declarations: [],
  imports: [CommonModule, HttpClientModule],
  providers: [Services],
})
export class ServicesModule {}
