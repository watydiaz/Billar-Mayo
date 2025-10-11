# Botón Flotante de Acciones Rápidas - Terkkos Billar

## 📋 Descripción
Botón flotante ubicado en la esquina inferior derecha de todas las vistas que permite acceso rápido a las funciones más utilizadas del sistema.

## ✨ Características

### 🎯 Ubicación
- **Posición**: Esquina inferior derecha (fixed)
- **Responsive**: Se adapta a dispositivos móviles
- **Z-index**: 1050 (siempre visible)

### 🎨 Diseño
- **Color principal**: Azul Bootstrap con gradiente
- **Tamaño**: 60px x 60px (55px en móviles)
- **Forma**: Circular con sombra elegante
- **Badge**: Indicador amarillo Terkkos con el número de opciones (2)

### 🔧 Funcionalidades

#### 1. **Crear Pedido**
- **Ícono**: `bi-file-earmark-plus`
- **Comportamiento**:
  - Si está en `/pedidos`: Abre modal `nuevoPedidoModal`
  - Si está en otra vista: Redirige a `/pedidos?nuevo=1`
  - Auto-enfoque en el primer campo del formulario
  - Animación suave de transición

#### 2. **Venta Rápida**
- **Ícono**: `bi-lightning-charge`
- **Opciones predefinidas**:
  - Mesa por 1 hora: $15,000
  - Mesa por 2 horas: $28,000  
  - Mesa por 3 horas: $40,000
  - Venta personalizada (abre crear pedido)
- **Modal dinámico**: Se crea y elimina automáticamente

### 🎭 Animaciones

#### Botón Principal
- **Hover**: Escala 1.1 con sombra expandida
- **Click**: Escala 0.95 
- **Rotación**: Ícono + gira 45° cuando está abierto
- **Pulso**: Efecto de onda en hover

#### Badge
- **Animación**: Pulso suave cada 2 segundos
- **Color**: Amarillo Terkkos con borde blanco

#### Dropdown
- **Entrada**: Desde abajo hacia arriba
- **Items**: Translación horizontal en hover
- **Salida**: Auto-cierre inteligente

## 📱 Responsive Design

### Desktop
- Botón: 60px x 60px
- Posición: 30px desde esquinas
- Dropdown: 200px mínimo

### Mobile (< 768px)
- Botón: 55px x 55px  
- Posición: 20px desde esquinas
- Dropdown: 180px mínimo

## 🛠️ Implementación Técnica

### Archivos Modificados
1. `resources/views/layouts/app.blade.php` - HTML + JavaScript
2. `resources/css/app.css` - Estilos CSS
3. Compilación automática con Vite

### JavaScript Functions
```javascript
crearNuevoPedido()    // Gestión inteligente de nuevo pedido
ventaRapida()         // Modal dinámico de venta rápida  
procesarVentaRapida() // Procesamiento de opciones
```

### CSS Classes
```css
.floating-quick-actions    // Container principal
.btn-floating-main        // Botón principal
.floating-menu           // Dropdown menu
.badge-notification      // Badge indicador
```

## 🚀 Uso

### Para el Usuario
1. **Ver opciones**: Click en botón flotante
2. **Crear pedido**: Click en "Crear Pedido"
3. **Venta rápida**: Click en "Venta Rápida" → Seleccionar opción

### Para el Desarrollador
- **Agregar opción**: Modificar array `ventaOptions` en JavaScript
- **Cambiar estilos**: Editar variables CSS en `:root`
- **Extender funcionalidad**: Añadir funciones al objeto `window`

## 🎯 Próximas Mejoras
- [ ] Integración completa de venta rápida con base de datos
- [ ] Notificaciones push en el badge
- [ ] Más opciones de acciones rápidas
- [ ] Configuración personalizable por usuario
- [ ] Analytics de uso de botones

---
**Desarrollado para Terkkos Billiards Club** 🎱