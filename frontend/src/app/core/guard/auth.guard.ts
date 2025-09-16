import {CanActivateFn, Router} from '@angular/router';
import {inject} from "@angular/core";

export const authGuard: CanActivateFn = (route,state) => {
    const router = inject(Router)
    const isLogin = localStorage.getItem('logIn') ?? '0';

    if(parseInt(isLogin) === 0 ){
        inject(Router).navigate(['login']).then();
        localStorage.clear();
        return true
    }

    let fechaExp: any = localStorage.getItem('fechaExpiracion');
    fechaExp = new Date(fechaExp);
    if(new Date() > fechaExp){ // Redireccionar a Login
        inject(Router).navigate(['login']).then();
        localStorage.clear();
        return true
    }
    
// Verificación de acceso a rutas específicas solo si está autenticado 
const menu = JSON.parse(localStorage.getItem('menu') || '[]'); 
let currentPath = state.url.split('?')[0];
currentPath = currentPath.startsWith('/') ? currentPath.substring(1) : currentPath; 
// Obtén la ruta sin parámetros de consulta
 console.log('Verificando acceso a la ruta:', currentPath); 
 console.log('Menú disponible:', menu); 
 const hasAccess = menu.some((item: any) => item.RUTA === currentPath);
  if (!hasAccess) { router.navigate(['inicio']).then();
 return false; }// Se niega el acceso porque no tiene permisos para esta ruta } return true;
     return true;
};
