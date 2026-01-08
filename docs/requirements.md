# 📋 Requerimientos de Desarrollo

## Enterprise Integration Platform

### 1️⃣ API Gateway
#### 🎯 Responsabilidad

Punto único de entrada para sistemas externos.  
No contiene lógica de negocio.

#### 🔹 Requerimientos funcionales

- **RF-AG-01**: El API Gateway debe exponer endpoints REST para la creación de órdenes.
- **RF-AG-02**: Debe validar:  
  - Autenticación (API Key o JWT)  
  - Esquema del payload  
  - Headers obligatorios (X-Request-Id, Source-System)
- **RF-AG-03**: Debe enrutar solicitudes válidas al Order Service vía HTTP.
- **RF-AG-04**: Debe retornar respuestas síncronas claras (202 Accepted para procesos async).
- **RF-AG-05**: Debe registrar logs de entrada y salida sin exponer datos sensibles.

#### 🔹 Requerimientos no funcionales

- **RNF-AG-01**: Debe ser stateless.
- **RNF-AG-02**: Debe soportar rate limiting por cliente.
- **RNF-AG-03**: Debe fallar de forma controlada (timeouts, circuit breaker básico).
- **RNF-AG-04**: Debe estar preparado para escalar horizontalmente.

### 2️⃣ Order Service
#### 🎯 Responsabilidad

Gestión del dominio de órdenes y publicación de eventos.

#### 🔹 Requerimientos funcionales

- **RF-OS-01**: Debe recibir solicitudes de creación de órdenes desde el API Gateway.
- **RF-OS-02**: Debe validar reglas básicas de negocio:  
  - Cliente válido  
  - Total mayor a cero  
  - Ítems no vacíos
- **RF-OS-03**: Debe persistir la orden en su propia base de datos.
- **RF-OS-04**: Debe publicar el evento `order.created` en Kafka.
- **RF-OS-05**: Debe garantizar idempotencia basada en `event_id`.

#### 🔹 Requerimientos no funcionales

- **RNF-OS-01**: Debe realizar commit explícito de transacciones antes de publicar eventos.
- **RNF-OS-02**: Debe publicar eventos con headers estándar:  
  - `event_id`  
  - `event_type`  
  - `timestamp`  
  - `retry_count`
- **RNF-OS-03**: Debe manejar fallos de Kafka sin perder la orden.

### 3️⃣ Payment Service
#### 🎯 Responsabilidad

Procesar pagos de forma asíncrona a partir de eventos.

#### 🔹 Requerimientos funcionales

- **RF-PS-01**: Debe consumir eventos del topic `order.created`.
- **RF-PS-02**: Debe simular una integración bancaria externa.
- **RF-PS-03**: Debe emitir:  
  - `payment.confirmed` en caso de éxito  
  - `payment.failed` en caso de error
- **RF-PS-04**: Debe implementar retries con backoff exponencial.
- **RF-PS-05**: Debe enviar eventos irrecuperables a DLT.

#### 🔹 Requerimientos no funcionales

- **RNF-PS-01**: Debe garantizar idempotencia por evento.
- **RNF-PS-02**: Debe manejar commits manuales de offset.
- **RNF-PS-03**: Debe permitir configuración de `max_retries` por entorno.

### 4️⃣ Audit Service
#### 🎯 Responsabilidad

Trazabilidad y auditoría de eventos.

#### 🔹 Requerimientos funcionales

- **RF-AS-01**: Debe consumir todos los topics productivos y `.dlt`.
- **RF-AS-02**: Debe persistir cada evento recibido.
- **RF-AS-03**: Debe marcar eventos provenientes de DLT.
- **RF-AS-04**: Debe permitir consultas por:  
  - Tipo de evento  
  - Rango de fechas  
  - ID de agregado

#### 🔹 Requerimientos no funcionales

- **RNF-AS-01**: Debe ser tolerante a alto volumen de eventos.
- **RNF-AS-02**: Debe desacoplar almacenamiento y consumo.

### 5️⃣ Requerimientos transversales (OBLIGATORIOS)
#### 🔐 Seguridad

- Variables sensibles vía `.env`
- No secretos en código
- Logs sin datos personales

#### 📊 Observabilidad

- Logs estructurados (JSON)
- Correlation ID (`X-Request-Id`)
- Métricas básicas (opcional)

#### 🧪 Testing

- Unit tests por servicio
- Pruebas de contrato de eventos (estructura JSON)

#### 🐳 Contenedores

- Dockerfiles livianos
- Multi-stage build
- Healthchecks definidos