import { MediaMatcher } from "@angular/cdk/layout";
import { ChangeDetectorRef, Component, OnDestroy } from "@angular/core";
@Component({
  selector: "app-root",
  templateUrl: "./app.component.html",
  styleUrls: ["./app.component.scss"],
})
export class AppComponent {
  mobileQuery: MediaQueryList;

  menus: Array<any>;

  private _mobileQueryListener: () => void;

  constructor(
    private cdf: ChangeDetectorRef,
    private mediaMatcher: MediaMatcher
  ) {
    this.mobileQuery = mediaMatcher.matchMedia("(max-width: 600px)");
    this._mobileQueryListener = () => cdf.detectChanges();
    this.mobileQuery.addListener(this._mobileQueryListener);

    this.menus = [
      {
        text: "Home",
        icon: "home",
        url: "/",
      },
      {
        text: "Todo",
        icon: "edit",
        url: "/todo",
      },
    ];
  }

  ngOnInit() {
    this.cdf.detectChanges();
  }
  ngOnDestroy(): void {
    this.mobileQuery.removeListener(this._mobileQueryListener);
  }
}
