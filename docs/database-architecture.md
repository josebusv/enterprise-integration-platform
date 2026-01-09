## 1 Vista general

+-------------------+                 +-------------------+
|   Order Service   |                 |   Payment Servce  |
|   PostgreSQL DB   |                 |   PostgreSQL DB   |
+-------------------+                 +-------------------+
          |                                      |
          | Kafka Events                         | Kafka Events
          v                                      v
+----------------------------------------------------------+
|                   Audit Service                          |
|                PostgreSQL / OLAP-ready                   |
+----------------------------------------------------------+

- Cada servicio posee su base

- Ningún servicio accede a la DB de otro

- La auditoría consolida solo vía eventos

---

## 2 Order Service - Base de datos

Base: order_db

Tabla:

``` sql
CREATE TABLE orders (
    id UUID PRIMARY KEY,
    customer_id UUID NOT NULL,
    total_amount NUMERIC(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL,
    status VARCHAR(30) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);
```

Tabla: processed_events

``` sql
CREATE TABLE processed_events (
    event_id UUID PRIMARY KEY,
    processed_at TIMESTAMP NOT NULL DEFAULT NOW()
);
```

---

## 3 Payment Service - Base de Datos

Base: payment_db

Tabla: payments

``` sql
CREATE TABLE payments (
    id UUID PRIMARY KEY,
    order_id UUID NOT NULL,
    amount NUMERIC(12,2) NOT NULL,
    status VARCHAR(30) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);
```

Tabla: processed_events

``` sql
CREATE TABLE processed_events (
    event_id UUID PRIMARY KEY,
    processed_at TIMESTAMP NOT NULL DEFAULT NOW()
);
```

---

##  4 Audit ervice - Base de Datos
Base: audit_db
| Diseñada para lectura, trazabilidad y analisis, no transacciones.

``` sql
CREATE TABLE event_logs (
    id BIGSERIAL PRIMARY KEY,
    event_id UUID NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    aggregate_id UUID,
    source_service VARCHAR(100),
    is_dlt BOOLEAN DEFAULT FALSE,
    payload JSONB NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);
```

Índices

``` sql
CREATE INDEX idx_event_type ON event_logs(event_type);
CREATE INDEX idx_created_at ON event_logs(created_at);
CREATE INDEX idx_is_dlt ON event_logs(is_dlt);

```

Esto soporta:

- Auditorías
- Debug
- Reporting
- Futuro ETL