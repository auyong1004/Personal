export class TaskList {
  constructor(
    public id: string = '',
    public name: string = '',
    public color: string = ''
  ) {}
}

export class Task {
  constructor(
    public id: string = '',
    public subject: string = '',
    public description: string = '',
    public dueDate: Date = new Date(),
    public createDate: Date = new Date(),
    public priority: string = '0',
    public stat: boolean = false,
    //public taskList: TaskList,
    public list: string,
    public listName: string,
    public listColor: string
  ) {}
  /*
  set props(task: Object) {
    Object.keys(task).forEach((prop) => {
      if (this[prop] != undefined) this[prop] = task[prop];
    });
  }
  */
}
