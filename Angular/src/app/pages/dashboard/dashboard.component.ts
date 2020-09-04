import {
  Component,
  OnInit,
  ChangeDetectionStrategy,
  ViewEncapsulation,
  Inject,
  ChangeDetectorRef,
} from '@angular/core';
import { Task } from 'src/app/shared';
import {
  HttpClient,
  HttpParams,
  HttpHeaders,
  HttpErrorResponse,
  HttpResponse,
} from '@angular/common/http';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'],
  encapsulation: ViewEncapsulation.None,
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class DashboardComponent implements OnInit {
  expense: Number;
  tasks: Array<Task>;
  events: Array<any>;
  date: Date;
  dashboardLoaded: boolean;

  constructor(
    private hc: HttpClient,

    private cdf: ChangeDetectorRef
  ) {
    this.date = new Date();
    this.expense=0;
    this.tasks=[];
    this.events =[];
    this.dashboardLoaded=false;

  }

  ngOnInit() {
    this.getInfo();
  }

  getInfo() {
    let options = {},
      headers = new HttpHeaders({
        'Content-Type': 'application/json',
      });
    options['headers'] = headers;
    this.hc
      .get(`http://localhost:3000/api/dashboard`, options)
      .subscribe((resp: any) => {
        Object.keys(resp).forEach((prop) => {

          if (this[prop] != undefined) this[prop] = resp[prop];
        });
        this.dashboardLoaded=true;
        this.cdf.detectChanges();
      });
  }
}
