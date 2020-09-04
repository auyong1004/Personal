import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";

import { TodoRoutingModule } from "./todo-routing.module";
import { TodoComponent } from "./todo.component";
import { SharedModule } from "src/app/shared/shared.module";
import { TodoFormComponent } from "./components/todo-form/todo-form.component";

@NgModule({
  declarations: [TodoComponent, TodoFormComponent],
  imports: [CommonModule, TodoRoutingModule, SharedModule],
  entryComponents: [TodoFormComponent],
})
export class TodoModule {}

/**
 * 1-snack
 * 2-full screen
 * 3-fb
 * 4-tasklist service
 * 5-drawer
 */
