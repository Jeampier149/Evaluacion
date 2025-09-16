import {createFeatureSelector, createSelector} from "@ngrx/store";
import {Session} from "./session.actions";

const sessionState = createFeatureSelector<Session>('sessionState');

export const session = createSelector(
    sessionState,
    (sessionState) => sessionState
)

export const accesos = createSelector(
    sessionState,
    (sessionState) => sessionState.accesos
)
