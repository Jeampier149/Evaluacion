import {NgModule, isDevMode} from '@angular/core';
import {AppComponent} from './app.component';
import {AppRoutingModule} from "./app-routing.module";
import {CoreModule} from "./core/core.module";
import {BrowserModule} from "@angular/platform-browser";
import {NotFoundComponent} from '@layout/not-found/not-found.component';
import {StoreModule} from '@ngrx/store';
import {sessionReducer} from "@store/session.reducer";
import {RouterLink, RouterOutlet} from "@angular/router";
import {HTTP_INTERCEPTORS, HttpClientModule} from "@angular/common/http";
import {StoreDevtoolsModule} from '@ngrx/store-devtools';
import {InterceptorService} from "@services/interceptor.service";

@NgModule({
    declarations: [
        AppComponent,
        NotFoundComponent
    ],
    imports: [CoreModule, BrowserModule, HttpClientModule,
        StoreModule.forRoot({sessionState: sessionReducer}),
        RouterOutlet,
        AppRoutingModule,
        StoreDevtoolsModule.instrument({
            maxAge: 25,
            logOnly: !isDevMode()
        , connectInZone: true}), RouterLink],
    providers: [{provide: HTTP_INTERCEPTORS, useClass: InterceptorService, multi: true}],
    exports: [],
    bootstrap: [AppComponent]
})
export class AppModule {

}
