import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  constructor() {}

  isAuthenticated(): boolean {
    const user = localStorage.getItem('user');
    return !!user;
  }

  hasAccess(route: string): boolean {
    const menu = JSON.parse(localStorage.getItem('menu') || '[]');
    return menu.some((item: any) => item.path === route);
  }

  logout() {
    localStorage.removeItem('user');
    localStorage.removeItem('menu');
  }
}
