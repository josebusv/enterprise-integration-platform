# Kafka Policies

Este documento define las políticas técnicas para el uso de Kafka dentro de la
Enterprise Integration Platform. Su objetivo es garantizar confiabilidad,
desacoplamiento y trazabilidad en los flujos de eventos.

---

## 1. Event Ownership

Cada evento es propiedad exclusiva del servicio que lo produce.

- Solo el productor puede definir el esquema del evento.
- Ningún otro servicio puede modificar ni volver a emitir el mismo tipo de evento.
- Los consumidores solo reaccionan al evento, no lo transforman ni lo re-publican.

---

## 2. Event Structure

Todos los eventos deben seguir una estructura común para garantizar
consistencia e idempotencia.

```json
{
  "event_id": "uuid",
  "event_type": "order.created",
  "aggregate_id": "ORD-123",
  "source": "order-service",
  "timestamp": "2026-01-05T10:30:00Z",
  "version": 1,
  "payload": {}
}
Campos obligatorios:

- event_id: Identificador único del evento.

- event_type: Tipo de evento de dominio.

- aggregate_id: Identificador de la entidad principal.

- source: Servicio productor.

- timestamp: Fecha y hora de emisión.

- version: Versión del esquema del evento.

---

## 3. Idempotency Policy

- Cada evento debe tener un event_id único.

- Los consumidores deben almacenar los event_id ya procesados.

- Si un evento duplicado es recibido, debe ser ignorado de forma segura.

Esto evita reprocesamientos indebidos ante retries o replays.

---

## 4. Retry Policy

- Los consumidores implementan reintentos controlados ante fallos.

- Se utiliza backoff exponencial entre intentos.

- El offset de Kafka solo se confirma cuando el evento es procesado exitosamente.

Número máximo de reintentos: configurable por servicio.

---

## 5. Dead Letter Topics (DLT)

Si un evento falla después del máximo de reintentos permitidos, debe enviarse a
un Dead Letter Topic (DLT).

Ejemplos:

- order.created.dlt

- payment.failed.dlt

Los eventos en DLT deben incluir información del error para su análisis posterior.

---

## 6. Event Versioning

- Los eventos son inmutables.

- Cualquier cambio estructural requiere una nueva versión.

- Los consumidores deben soportar versiones anteriores cuando sea posible.

Ejemplo:

- order.created.v1

- order.created.v2

## 7. Event Replay

Kafka permite reprocesar eventos mediante el reseteo de offsets del consumidor.

Casos de uso:

Recuperación ante fallos

Reconstrucción de estados

Auditoría y análisis

El replay debe realizarse de forma controlada.

## 8. Consistency Model

La plataforma sigue un modelo de consistencia eventual.

No se utilizan transacciones distribuidas.

Cada servicio mantiene su propio estado.

Kafka garantiza entrega confiable de eventos.

## 9. Observability

Todos los servicios consumidores deben:

- Registrar eventos procesados.

- Registrar errores y retries.

- Permitir trazabilidad por event_id.

Esto facilita monitoreo y debugging en entornos productivos.