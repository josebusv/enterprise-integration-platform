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
