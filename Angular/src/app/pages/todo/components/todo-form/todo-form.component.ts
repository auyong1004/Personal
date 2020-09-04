import { Component, OnInit, Inject } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  FormControl,
  Validators,
} from '@angular/forms';
import {
  MatDialog,
  MAT_DIALOG_DATA,
  MatDialogRef,
} from '@angular/material/dialog';
import { Task } from 'src/app/shared';
import {
  HttpClient,
  HttpParams,
  HttpHeaders,
  HttpErrorResponse,
  HttpResponse,
} from '@angular/common/http';
import { TodoService } from 'src/app/shared/services/todo-service';
@Component({
  selector: 'app-todo-form',
  templateUrl: './todo-form.component.html',
  styleUrls: ['./todo-form.component.scss'],
})
export class TodoFormComponent implements OnInit {
  taskForm: FormGroup;
  taskLists: Array<any> = [];
  priorities: Array<any> = [];
  constructor(
    public dialogRef: MatDialogRef<TodoFormComponent>,
    @Inject(MAT_DIALOG_DATA) public dialogData: Object | any,
    private fb: FormBuilder,
    private hc: HttpClient,
    private todoService: TodoService
  ) {
    this.setupOption();
  }

  setupOption() {
    this.priorities = [
      {
        value: '1',
        label: 'Urgent',
      },
      {
        value: '2',
        label: 'Important',
      },
      {
        value: '3',
        label: 'Normal',
      },
    ];
    let options = {},
      headers = new HttpHeaders({
        'Content-Type': 'application/json',
      });
    options['headers'] = headers;
    this.hc
      .get(`http://34.72.125.134/personal/public/api/taskList`, options)
      .subscribe((resp: any) => {
        this.taskLists = resp;
      });
  }

  ngOnInit() {
    console.log(this.dialogData);
    this.registerForm();
    if (this.dialogData.mode == 'put') this.patchForm();
  }
  patchForm() {
    this.taskForm.patchValue(this.dialogData.task);
  }
  registerForm() {
    this.taskForm = this.fb.group({
      id: new FormControl(),
      subject: new FormControl('', Validators.required),
      description: new FormControl('', Validators.required),
      createDate: new FormControl({ value: new Date(), disabled: true }),
      dueDate: new FormControl(
        new Date(new Date().getTime() + 2 * 24 * 60 * 60 * 1000),
        Validators.required
      ),
      priority: new FormControl('1', Validators.required),
      list: new FormControl(1, Validators.required),
      stat: new FormControl(false, Validators.required),
    });
  }

  saveForm() {
    let task = this.taskForm.getRawValue();
    let observable =
      this.dialogData.mode == 'post'
        ? this.todoService.create(task)
        : this.todoService.update(task);
    observable.subscribe((resp) => {
      if(this.dialogData.mode == 'post')task.id=resp.id;

      this.dialogRef.close(task);
    });
  }
  resetForm() {
    this.dialogRef.close(false);
  }
}
