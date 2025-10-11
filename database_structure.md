# 📊 Estructura de Base de Datos - Terkkos Billar

**Base de datos:** `barcode_terkkos`  
**Generado:** 2025-10-11 16:34:17  
**Total de tablas:** 21

---

## 📋 Índice de Tablas

- [`actividad_log`](#actividad_log) - 0 registros
- [`cache`](#cache) - 0 registros
- [`cache_locks`](#cache_locks) - 0 registros
- [`categorias`](#categorias) - 8 registros
- [`configuracion`](#configuracion) - 8 registros
- [`failed_jobs`](#failed_jobs) - 0 registros
- [`job_batches`](#job_batches) - 0 registros
- [`jobs`](#jobs) - 0 registros
- [`mesa_rondas`](#mesa_rondas) - 1 registros
- [`mesas`](#mesas) - 4 registros
- [`migrations`](#migrations) - 3 registros
- [`password_reset_tokens`](#password_reset_tokens) - 0 registros
- [`productos`](#productos) - 67 registros
- [`productos_mas_vendidos`](#productos_mas_vendidos) - 0 registros
- [`productos_stock_bajo`](#productos_stock_bajo) - 0 registros
- [`ronda_detalles`](#ronda_detalles) - 1 registros
- [`rondas`](#rondas) - 0 registros
- [`sessions`](#sessions) - 26 registros
- [`users`](#users) - 0 registros
- [`venta_detalles`](#venta_detalles) - 1 registros
- [`ventas`](#ventas) - 1 registros

---

## 📋 `actividad_log`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | PRI | - | auto_increment |
| `tabla` | varchar(50) | ✅ | - | - | - |
| `accion` | varchar(50) | ✅ | - | - | - |
| `registro_id` | int | ✅ | - | - | - |
| `created_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| `ronda_id` | int | ✅ | - | - | - |
| `tipo_actividad` | varchar(50) | ✅ | - | - | - |
| `descripcion` | text | ✅ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |

---

## 📋 `cache`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `key` | varchar(255) | ❌ | PRI | - | - |
| `value` | mediumtext | ❌ | - | - | - |
| `expiration` | int | ❌ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | key |

---

## 📋 `cache_locks`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `key` | varchar(255) | ❌ | PRI | - | - |
| `owner` | varchar(255) | ❌ | - | - | - |
| `expiration` | int | ❌ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | key |

---

## 📋 `categorias`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 8  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | PRI | - | auto_increment |
| `nombre` | varchar(100) | ❌ | - | - | - |
| `descripcion` | text | ✅ | - | - | - |
| `activo` | tinyint(1) | ✅ | - | 1 | - |
| `created_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| `updated_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |

---

## 📋 `configuracion`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 8  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | PRI | - | auto_increment |
| `clave` | varchar(100) | ❌ | UNI | - | - |
| `valor` | text | ❌ | - | - | - |
| `descripcion` | text | ✅ | - | - | - |
| `tipo_dato` | enum('string','number','boolean','json') | ✅ | - | string | - |
| `categoria` | varchar(50) | ✅ | MUL | general | - |
| `created_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| `updated_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `clave` | BTREE | ✅ | clave |
| `idx_categoria` | BTREE | ❌ | categoria |
| `idx_clave` | BTREE | ❌ | clave |

---

## 📋 `failed_jobs`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | bigint unsigned | ❌ | PRI | - | auto_increment |
| `uuid` | varchar(255) | ❌ | UNI | - | - |
| `connection` | text | ❌ | - | - | - |
| `queue` | text | ❌ | - | - | - |
| `payload` | longtext | ❌ | - | - | - |
| `exception` | longtext | ❌ | - | - | - |
| `failed_at` | timestamp | ❌ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `failed_jobs_uuid_unique` | BTREE | ✅ | uuid |

---

## 📋 `job_batches`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | varchar(255) | ❌ | PRI | - | - |
| `name` | varchar(255) | ❌ | - | - | - |
| `total_jobs` | int | ❌ | - | - | - |
| `pending_jobs` | int | ❌ | - | - | - |
| `failed_jobs` | int | ❌ | - | - | - |
| `failed_job_ids` | longtext | ❌ | - | - | - |
| `options` | mediumtext | ✅ | - | - | - |
| `cancelled_at` | int | ✅ | - | - | - |
| `created_at` | int | ❌ | - | - | - |
| `finished_at` | int | ✅ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |

---

## 📋 `jobs`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | bigint unsigned | ❌ | PRI | - | auto_increment |
| `queue` | varchar(255) | ❌ | MUL | - | - |
| `payload` | longtext | ❌ | - | - | - |
| `attempts` | tinyint unsigned | ❌ | - | - | - |
| `reserved_at` | int unsigned | ✅ | - | - | - |
| `available_at` | int unsigned | ❌ | - | - | - |
| `created_at` | int unsigned | ❌ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `jobs_queue_index` | BTREE | ❌ | queue |

---

## 📋 `mesa_rondas`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 1  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | bigint unsigned | ❌ | PRI | - | auto_increment |
| `ronda_id` | int | ❌ | MUL | - | - |
| `mesa_id` | int | ❌ | MUL | - | - |
| `inicio_tiempo` | datetime | ✅ | - | - | - |
| `fin_tiempo` | datetime | ✅ | - | - | - |
| `duracion_minutos` | int | ✅ | - | - | - |
| `costo_tiempo` | decimal(8,2) | ❌ | - | 0.00 | - |
| `estado` | enum('pendiente','activo','finalizado') | ❌ | MUL | pendiente | - |
| `observaciones` | text | ✅ | - | - | - |
| `created_at` | timestamp | ✅ | - | - | - |
| `updated_at` | timestamp | ✅ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `mesa_rondas_mesa_id_foreign` | BTREE | ❌ | mesa_id |
| `mesa_rondas_ronda_id_mesa_id_index` | BTREE | ❌ | ronda_id, mesa_id |
| `mesa_rondas_estado_mesa_id_index` | BTREE | ❌ | estado, mesa_id |

### Claves Foráneas

| Campo Local | Tabla Referencias | Campo Referencias | Constraint |
|-------------|-------------------|-------------------|------------|
| `mesa_id` | `mesas` | `id` | mesa_rondas_mesa_id_foreign |
| `ronda_id` | `rondas` | `id` | mesa_rondas_ronda_id_foreign |

---

## 📋 `mesas`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 4  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | PRI | - | auto_increment |
| `numero_mesa` | int | ❌ | UNI | - | - |
| `capacidad` | int | ✅ | - | 4 | - |
| `precio_hora` | decimal(10,2) | ✅ | - | 7000.00 | - |
| `activa` | tinyint(1) | ✅ | - | 1 | - |
| `descripcion` | varchar(200) | ✅ | - | - | - |
| `created_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `numero_mesa` | BTREE | ✅ | numero_mesa |

---

## 📋 `migrations`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 3  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int unsigned | ❌ | PRI | - | auto_increment |
| `migration` | varchar(255) | ❌ | - | - | - |
| `batch` | int | ❌ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |

---

## 📋 `password_reset_tokens`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `email` | varchar(255) | ❌ | PRI | - | - |
| `token` | varchar(255) | ❌ | - | - | - |
| `created_at` | timestamp | ✅ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | email |

---

## 📋 `productos`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 67  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | PRI | - | auto_increment |
| `codigo` | varchar(50) | ✅ | UNI | - | - |
| `nombre` | varchar(200) | ❌ | MUL | - | - |
| `descripcion` | text | ✅ | - | - | - |
| `categoria_id` | int | ✅ | MUL | - | - |
| `precio_venta` | decimal(10,2) | ❌ | - | - | - |
| `precio_costo` | decimal(10,2) | ✅ | - | - | - |
| `stock_actual` | int | ✅ | MUL | - | - |
| `stock_minimo` | int | ✅ | - | 5 | - |
| `unidad_medida` | varchar(20) | ✅ | - | unidad | - |
| `activo` | tinyint(1) | ✅ | MUL | 1 | - |
| `imagen_url` | varchar(500) | ✅ | - | - | - |
| `created_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| `updated_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `codigo` | BTREE | ✅ | codigo |
| `idx_codigo` | BTREE | ❌ | codigo |
| `idx_nombre` | BTREE | ❌ | nombre |
| `idx_categoria` | BTREE | ❌ | categoria_id |
| `idx_activo` | BTREE | ❌ | activo |
| `idx_productos_categoria_activo` | BTREE | ❌ | categoria_id, activo |
| `idx_stock_bajo` | BTREE | ❌ | stock_actual, stock_minimo |

### Claves Foráneas

| Campo Local | Tabla Referencias | Campo Referencias | Constraint |
|-------------|-------------------|-------------------|------------|
| `categoria_id` | `categorias` | `id` | productos_ibfk_1 |

---

## 📋 `productos_mas_vendidos`

**Motor:**   
**Collation:**   
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | - | - | - |
| `codigo` | varchar(50) | ✅ | - | - | - |
| `nombre_producto` | varchar(200) | ❌ | - | - | - |
| `categoria` | varchar(100) | ❌ | - | - | - |
| `veces_pedido` | bigint | ❌ | - | - | - |
| `cantidad_total_vendida` | decimal(32,0) | ✅ | - | - | - |
| `ingresos_totales` | decimal(32,2) | ✅ | - | - | - |
| `precio_promedio` | decimal(14,6) | ✅ | - | - | - |
| `stock_actual` | int | ✅ | - | - | - |
| `precio_actual` | decimal(10,2) | ❌ | - | - | - |

---

## 📋 `productos_stock_bajo`

**Motor:**   
**Collation:**   
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | - | - | - |
| `codigo` | varchar(50) | ✅ | - | - | - |
| `nombre` | varchar(200) | ❌ | - | - | - |
| `categoria` | varchar(100) | ✅ | - | - | - |
| `stock_actual` | int | ✅ | - | - | - |
| `stock_minimo` | int | ✅ | - | 5 | - |
| `precio_venta` | decimal(10,2) | ❌ | - | - | - |

---

## 📋 `ronda_detalles`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 1  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | PRI | - | auto_increment |
| `ronda_id` | int | ❌ | MUL | - | - |
| `producto_id` | int | ✅ | MUL | - | - |
| `nombre_producto` | varchar(300) | ❌ | - | - | - |
| `cantidad` | int | ❌ | - | - | - |
| `precio_unitario` | decimal(10,2) | ❌ | - | - | - |
| `subtotal` | decimal(10,2) | ❌ | - | - | - |
| `es_descuento` | tinyint(1) | ✅ | MUL | - | - |
| `es_producto_personalizado` | tinyint(1) | ✅ | MUL | - | - |
| `notas` | text | ✅ | - | - | - |
| `created_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| `updated_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `idx_ronda` | BTREE | ❌ | ronda_id |
| `idx_producto` | BTREE | ❌ | producto_id |
| `idx_descuento` | BTREE | ❌ | es_descuento |
| `idx_personalizado` | BTREE | ❌ | es_producto_personalizado |

### Claves Foráneas

| Campo Local | Tabla Referencias | Campo Referencias | Constraint |
|-------------|-------------------|-------------------|------------|
| `ronda_id` | `rondas` | `id` | ronda_detalles_ibfk_1 |
| `producto_id` | `productos` | `id` | ronda_detalles_ibfk_2 |

---

## 📋 `rondas`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | int | ❌ | PRI | - | auto_increment |
| `numero_ronda` | varchar(50) | ❌ | UNI | - | - |
| `cliente` | varchar(255) | ✅ | - | - | - |
| `total_ronda` | decimal(10,2) | ✅ | - | 0.00 | - |
| `responsable` | text | ✅ | - | - | - |
| `estado` | enum('activa','pagada') | ✅ | MUL | activa | - |
| `es_duplicada` | tinyint(1) | ✅ | MUL | - | - |
| `ronda_origen_id` | int | ✅ | MUL | - | - |
| `created_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| `updated_at` | timestamp | ✅ | - | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `unique_pedido_ronda` | BTREE | ✅ | numero_ronda |
| `ronda_origen_id` | BTREE | ❌ | ronda_origen_id |
| `idx_numero_ronda` | BTREE | ❌ | numero_ronda |
| `idx_estado` | BTREE | ❌ | estado |
| `idx_duplicada` | BTREE | ❌ | es_duplicada |
| `idx_rondas_pedido_estado` | BTREE | ❌ | estado |

### Claves Foráneas

| Campo Local | Tabla Referencias | Campo Referencias | Constraint |
|-------------|-------------------|-------------------|------------|
| `ronda_origen_id` | `rondas` | `id` | rondas_ibfk_2 |

---

## 📋 `sessions`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 26  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | varchar(255) | ❌ | PRI | - | - |
| `user_id` | bigint unsigned | ✅ | MUL | - | - |
| `ip_address` | varchar(45) | ✅ | - | - | - |
| `user_agent` | text | ✅ | - | - | - |
| `payload` | longtext | ❌ | - | - | - |
| `last_activity` | int | ❌ | MUL | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `sessions_user_id_index` | BTREE | ❌ | user_id |
| `sessions_last_activity_index` | BTREE | ❌ | last_activity |

---

## 📋 `users`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 0  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | bigint unsigned | ❌ | PRI | - | auto_increment |
| `name` | varchar(255) | ❌ | - | - | - |
| `email` | varchar(255) | ❌ | UNI | - | - |
| `email_verified_at` | timestamp | ✅ | - | - | - |
| `password` | varchar(255) | ❌ | - | - | - |
| `remember_token` | varchar(100) | ✅ | - | - | - |
| `created_at` | timestamp | ✅ | - | - | - |
| `updated_at` | timestamp | ✅ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `users_email_unique` | BTREE | ✅ | email |

---

## 📋 `venta_detalles`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 1  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | bigint unsigned | ❌ | PRI | - | auto_increment |
| `venta_id` | bigint unsigned | ❌ | MUL | - | - |
| `producto_id` | int unsigned | ✅ | - | - | - |
| `cantidad` | int | ❌ | - | - | - |
| `precio_unitario` | decimal(8,2) | ❌ | - | - | - |
| `subtotal` | decimal(10,2) | ❌ | - | - | - |
| `descripcion` | varchar(255) | ✅ | - | - | - |
| `created_at` | timestamp | ✅ | - | - | - |
| `updated_at` | timestamp | ✅ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `venta_detalles_venta_id_producto_id_index` | BTREE | ❌ | venta_id, producto_id |

### Claves Foráneas

| Campo Local | Tabla Referencias | Campo Referencias | Constraint |
|-------------|-------------------|-------------------|------------|
| `venta_id` | `ventas` | `id` | venta_detalles_venta_id_foreign |

---

## 📋 `ventas`

**Motor:** InnoDB  
**Collation:** utf8mb4_unicode_ci  
**Registros:** 1  

### Columnas

| Campo | Tipo | Nulo | Clave | Defecto | Extra |
|-------|------|------|-------|---------|-------|
| `id` | bigint unsigned | ❌ | PRI | - | auto_increment |
| `numero_venta` | varchar(255) | ❌ | UNI | - | - |
| `subtotal` | decimal(10,2) | ❌ | - | 0.00 | - |
| `descuento` | decimal(10,2) | ❌ | - | 0.00 | - |
| `total` | decimal(10,2) | ❌ | - | - | - |
| `estado` | enum('0','1') | ❌ | MUL | - | - |
| `tipo_pago` | varchar(255) | ❌ | - | efectivo | - |
| `observaciones` | text | ✅ | - | - | - |
| `created_at` | timestamp | ✅ | - | - | - |
| `updated_at` | timestamp | ✅ | - | - | - |

### Índices

| Nombre | Tipo | Único | Columnas |
|--------|------|-------|----------|
| `PRIMARY` | BTREE | ✅ | id |
| `ventas_numero_venta_unique` | BTREE | ✅ | numero_venta |
| `ventas_estado_created_at_index` | BTREE | ❌ | estado, created_at |
| `ventas_numero_venta_index` | BTREE | ❌ | numero_venta |

---

