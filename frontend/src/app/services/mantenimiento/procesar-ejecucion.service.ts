import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable, shareReplay} from "rxjs";
import {HttpResponseApi} from "@interfaces/http.interface";

@Injectable({
  providedIn: 'root'
})
export class ProcesarEjecucionService {

  constructor(private http: HttpClient) { }

  Bloquear(periodo:any,fecha:any,year:any,tipo:any,actividad:any){
    return this.http.post<HttpResponseApi>(
      '/api/mantenimiento/bloquear-ejecucion',
      { periodo,fecha,year,tipo,actividad},
      { responseType: 'json' }
  );
  }

  ProcesarEjecucion(año:any,mes:any,tipo:any,ppr:any) {
    return this.http.post<HttpResponseApi>(
        '/api/mantenimiento/procesar-ejecucion',
        { año,mes,tipo,ppr },
        { responseType: 'json' }
    );
}
 reporteExcel(periodo:string,year:string,tipo:string){
  return this.http.get('/api/mantenimiento/reporte-ppr-invalidados', { 
    params:{
      periodo,
      year,
      tipo
  }, responseType: 'blob' });
 }
 reporteCierre(periodo:string,year:string,tipo:string){
  return this.http.get('/api/mantenimiento/reporte-cierre', { 
    params:{
      periodo,
      year,
      tipo
  }, responseType: 'blob' });
 }
 reporteResumenMetas(year:string,tipo:string){
  return this.http.get('/api/mantenimiento/reporte-resumen-metas', { 
    params:{
      year,
      tipo
  }, responseType: 'blob' });
 }
listarHistorial(datos: any) {
  return this.http
      .get<HttpResponseApi>('/api/mantenimiento/listar-historial', {
          params: {
              usuario: datos.usuario,
              nombre: datos.nombre,
              perfil: datos.perfil,
              equipo: datos.equipo,
              ppr: datos.ppr,
              fecha: datos.fecha,
              pagina: datos.pagina,
              longitud: datos.longitud,
          },
          responseType: 'json',
      })
      .pipe(shareReplay(1));
}

reporteExcelConsolidado(periodo:any,year:any,tipo:any){
  return this.http.get('/api/mantenimiento/reporte-ppr-consolidado', { 
    params:{
      periodo,
      year,
      tipo
    }, responseType: 'blob' });
 }
 reporteConsolidadoDetallado(periodo:any,year:any,tipo:any){
  return this.http.get('/api/mantenimiento/reporte-consolidado-detallado', { 
    params:{
      periodo,
      year,
      tipo
    }, responseType: 'blob' });
 }
 reporteLogros(trimestre:any,year:any,tipo:any){
  return this.http.get('/api/mantenimiento/reporte-logros', { 
    params:{
      trimestre,
      year,
      tipo
    }, responseType: 'blob' });
 }
 listarBloqueo(){
  return this.http.post<HttpResponseApi>(
    '/api/mantenimiento/listar-info-bloqueos',
    { },
    { responseType: 'json' }
);
 }
 reporteCentroCostos(periodo:any,year:any,tipo:any){
  return this.http.get('/api/mantenimiento/reporte-centro-costos', { 
    params:{
      periodo,
      year,
      tipo
    }, responseType: 'blob' });
 }
}
