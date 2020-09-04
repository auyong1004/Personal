import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardModule } from './pages/dashboard/dashboard.module';
import { TodoModule } from './pages/todo/todo.module';

const routes: Routes = [
  { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
  {
    path: 'dashboard',
    loadChildren:()=>DashboardModule
    //loadChildren: 'app/pages/dashboard/dashboard.module#DashboardModule',
    //loadChildren:()=>import('./pages/pr-home/pr-home.module').then((m:any) => m.LazyModule),
    //data: new AppPageData(true, "Dashboard"),
  },

  {
    path: 'todo',
    loadChildren:()=>TodoModule
    //loadChildren:()=>import('./pages/pr-home/pr-home.module').then((m:any) => m.LazyModule),
    //data: new AppPageData(true, "Dashboard"),
    //resolve: {
    //   config: AppResolverService
    //},
    //canActivate: [AuthGuard]
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
