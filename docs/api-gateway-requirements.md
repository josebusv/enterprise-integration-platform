## Requerimientos del API Gateway

Enterprise Integration Platform

### 1. Propósito del componente

El API Gateway es el punto único de entrada para sistemas externos (ERP, e-commerce, banca).
Su función es proteger, validar y enrutar solicitudes, sin contener lógica de negocio.

---

## 2. Alcance

Incluido

- Exposición de APIs HTTP

- Autenticación y control de acceso

- Validación de payloads

- Rate limiting

- Logging y trazabilidad

- Enrutamiento al Order Service

Excluido

- Persistencia de datos

- Reglas de negocio

- Integración directa con Kafka

- Orquestación de procesos

---

## 3. Stakeholders

| Rol               | Responsabilidad               |
| ----------------- | ----------------------------- |
| Sistemas externos | Consumir la API               |
| Equipo Backend    | Implementar el Gateway        |
| Arquitectura      | Definir contratos y políticas |
| Seguridad         | Validar controles de acceso   |

---

## 4. Casos de Uso

### CU-AG-01 — Crear Orden

Actor: Sistema externo
Descripción: Enviar una solicitud para crear una orden de forma asíncrona.

Flujo principal:

1. El cliente envía una solicitud HTTP POST /api/orders

2. El API Gateway:

    - Autentica la solicitud

    - Valida headers obligatorios

    - Valida estructura del payload

3. El API Gateway reenvía la solicitud al Order Service

4. Retorna 202 Accepted

Flujos alternos:

  - Autenticación inválida → 401 Unauthorized

  - Payload inválido → 400 Bad Request

  - Rate limit excedido → 429 Too Many Requests

  - Order Service no disponible → 503 Service Unavailable

---

## 5. Contrato de la API

Endpoint

```bash
POST /api/orders
```
Headers obligatorios

| Header        | Descripción                  |
| ------------- | ---------------------------- |
| Authorization | API Key o JWT                |
| X-Request-Id  | Identificador de correlación |
| Content-Type  | application/json             |

---
```json
{
  "order_id": "uuid",
  "customer_id": "uuid",
  "total_amount": 150.50,
  "currency": "USD",
  "items": [
    {
      "sku": "ABC-123",
      "quantity": 2,
      "price": 75.25
    }
  ]
}
```
---
Response (éxito)

202 Accepted

```json
{
  "request_id": "uuid",
  "status": "accepted"
}
```
---
Response (error de validación)

400 Bad Request

```json
{
  "request_id": "uuid",
  "error": "Invalid request payload"
}
```

---

## 6. Requerimientos Funcionales
RF-AG-01

El sistema debe exponer el endpoint POST /api/orders.

RF-AG-02

El sistema debe validar autenticación mediante API Key o JWT.

RF-AG-03

El sistema debe rechazar solicitudes sin X-Request-Id.

RF-AG-04

El sistema debe validar el esquema del payload (tipos y campos obligatorios).

RF-AG-05

El sistema debe reenviar solicitudes válidas al Order Service vía HTTP.

RF-AG-06

El sistema debe retornar 202 Accepted para solicitudes válidas.

---

## 7. Requerimientos No Funcionales
Seguridad

- RNF-AG-01: El sistema no debe registrar datos sensibles en logs.

- RNF-AG-02: Las credenciales deben configurarse por variables de entorno.

Performance

- RNF-AG-03: Timeout máximo hacia Order Service: 3 segundos.

- RNF-AG-04: El sistema debe soportar al menos 100 RPS.

Escalabilidad

- RNF-AG-05: El sistema debe ser stateless.

- RNF-AG-06: El sistema debe permitir escalado horizontal.

Observabilidad

- RNF-AG-07: El sistema debe registrar logs estructurados (JSON).

- RNF-AG-08: Cada log debe incluir X-Request-Id.

---

## 8. Reglas de Negocio (explícitamente excluidas)

El API Gateway NO debe:

 - Validar existencia de clientes

 - Calcular montos

 - Evaluar stock

 - Tomar decisiones de negocio

 ---
  
## 9. Arquitectura interna requerida

```bash
Controller
 ├── FormRequest (validación)
 ├── HttpClientService (forwarding)
 └── ResponseMapper
```

Restricciones:

 - No usar base de datos
 - No usar modelos Eloquent
 - No usar lógica de dominio

---

## 10. Manejo de Errores

| Escenario          | Código |
| ------------------ | ------ |
| Auth inválida      | 401    |
| Payload inválido   | 400    |
| Rate limit         | 429    |
| Timeout downstream | 503    |
| Error inesperado   | 500    |

---

## 11. Definition of Done (DoD)

El desarrollo se considera completo cuando:

- El endpoint está documentado
- Todos los middleware están implementados
- Las validaciones están cubiertas
- El Order Service recibe correctamente las solicitudes
- Logs incluyen Request ID
- No existe lógica de negocio en el Gateway

---

## 12. Suposiciones y Dependencias

- El Order Service expone un endpoint HTTP interno
- Kafka no es accedido directamente por el Gateway
- El sistema se ejecuta en contenedores Docker

---

## 13. Entregables esperados

- Código fuente del API Gateway
- Tests básicos de validación
- Documentación de despliegue
- Variables de entorno documentadas

---

Nota de Arquitectura (importante)

El API Gateway es un componente de protección y desacoplamiento,
no un orquestador ni un motor de negocio.