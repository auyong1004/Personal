import {
  Component,
  OnInit,
  ChangeDetectionStrategy,
  ViewEncapsulation,
  Inject,
  ChangeDetectorRef,
} from '@angular/core';
import { TodoService } from '../../shared/services/todo-service';
import { FormBuilder, FormGroup, FormControl } from '@angular/forms';
import { Task } from 'src/app/shared';
import { Observable } from 'rxjs';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material/dialog';

import { TodoFormComponent } from './components/todo-form/todo-form.component';
import {
  HttpClient,
  HttpParams,
  HttpHeaders,
  HttpErrorResponse,
  HttpResponse,
} from '@angular/common/http';

@Component({
  selector: 'app-todo',
  templateUrl: './todo.component.html',
  styleUrls: ['./todo.component.scss'],
  encapsulation: ViewEncapsulation.None,
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class TodoComponent implements OnInit {
  taskLists: Array<any>;
  taskList: any = 0;
  taskForm: FormControl;
  private _tasks: Array<Task> = [];
  constructor(
    private todoService: TodoService,
    private fb: FormBuilder,
    private cdf: ChangeDetectorRef,
    private snackBar: MatSnackBar,
    private matDialog: MatDialog,
    private hc: HttpClient
  ) {}

  ngOnInit() {
    this.taskForm = new FormControl(1);

    let options = {},
      headers = new HttpHeaders({
        'Content-Type': 'application/json',
      });
    this.hc
      .get(`http://34.72.125.134/personal/public/api/taskList`, options)
      .subscribe((resp: any) => {
        this.taskLists = resp;
      });

    this.todoService.tasks.subscribe((resp) => {
      this._tasks = resp;
      this.cdf.detectChanges();
    });
  }
  onTab(tabIndex) {
    this.taskList = this.taskLists[tabIndex].id;
  }

  createTask() {
    let task: Task = {
      id: '',
      subject: this.taskForm.value,
      description: '-',
      dueDate: new Date(),
      createDate: new Date(),
      priority: '1',
      stat: false,

      list: '1',
      listColor: '',
      listName: '',
    };
    console.log('create task');
    this.todoService.create(task).subscribe((resp) => {
      task.id = resp.id;
      this._tasks.push(task);
      //this._tasks=[...this._tasks];

      this.openSnackBar('Had been created', 'Close');
      this.cdf.detectChanges();
      this.taskForm.patchValue('');
    });
  }
  addTask() {
    let dialogRef = this.matDialog.open(TodoFormComponent, {
      data: {
        mode: 'post',
      },
      panelClass: 'todo-form',
    });
    dialogRef.afterClosed().subscribe((tempTask) => {
      if (!tempTask) return;
      this._tasks.push(tempTask);
      this.openSnackBar('Had been created', 'Close');
    });
  }
  editTask(task) {
    let dialogRef = this.matDialog.open(TodoFormComponent, {
      data: {
        mode: 'put',
        task: task,
      },
      panelClass: 'todo-form',
    });
    dialogRef.afterClosed().subscribe((tempTask) => {
      if (!tempTask) return;
      let selectedTask = this._tasks.find((task) => task.id == tempTask.id);
      if (selectedTask == undefined) return;
      this._tasks[this._tasks.indexOf(selectedTask)] = tempTask;
      this.cdf.detectChanges();
    });
  }
  deleteTask(task) {
    this._tasks.splice(this._tasks.indexOf(task), 1);

    this.todoService.delete(task).subscribe(() => {
      this.openSnackBar('Had been delete', 'Close');
    });
  }
  statTask(task: Task) {
    task.stat = !task.stat;
    //this.cdf.detectChanges();

    this.todoService.update(task).subscribe(() => {
      this.openSnackBar('Had been updated', 'Close');
    });
  }

  openSnackBar(message: string, action: string) {
    this.snackBar.open(message, action, {
      duration: 2000,
    });
  }

  get remaining() {
    return this._tasks
      .filter((task) => task.list == this.taskList)
      .filter((task) => !task.stat).length;
  }
  get completed() {
    return this._tasks
      .filter((task) => task.list == this.taskList)
      .filter((task) => task.stat).length;
  }
  get tasks() {
    return this._tasks.filter((task) => task.list == this.taskList);
  }
}
