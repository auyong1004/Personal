import { Injectable } from '@angular/core';
import {
  HttpClient,
  HttpParams,
  HttpHeaders,
  HttpErrorResponse,
  HttpResponse,
} from '@angular/common/http';

// Import rxjs required methods
import { Observable } from 'rxjs';
import { retry, catchError } from 'rxjs/operators';
import { Task } from '../../class';
@Injectable({
  providedIn: 'root',
})
export class TodoService {
  protected ENDPOINT: string = 'http://34.72.125.134/personal/public/api/task';

  //_tasks: Array<Task>;
  constructor(private hc: HttpClient) {}

  get tasks(): Observable<any> {
    return this.hc.get(this.ENDPOINT);
  }

  create(task: Task): Observable<any> {
    ///this._tasks.push(task);
    let options = {},
      headers = new HttpHeaders({
        'Content-Type': 'application/json',
      });
    options['headers'] = headers;
    return this.hc.post(this.ENDPOINT, JSON.stringify(task), options);
  }
  delete(task: Task) {
    let options = {},
      headers = new HttpHeaders({});
    options['headers'] = headers;

    return this.hc.delete(`${this.ENDPOINT}/${task.id}`, options);
  }
  update(task: Task): Observable<any> {
    /*
    let selectedTask = this._tasks.find((task: Task) => task.id == task.id);
    if (selectedTask == undefined) return false;
    this._tasks[this._tasks.indexOf(selectedTask)] = selectedTask;
    */
    //this._tasks.push(task);
    let options = {},
      headers = new HttpHeaders({
        'Content-Type': 'application/json',
      });
    options['headers'] = headers;
    return this.hc.put(
      `${this.ENDPOINT}/${task.id}`,
      JSON.stringify(task),
      options
    );
  }
}
