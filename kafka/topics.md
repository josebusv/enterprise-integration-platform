# Kafka Topics

## order.created
- Producer: Order Service
- Consumers: Payment Service, Audit Service
- Partitions: 3
- Purpose: Notify creation of a new order

## payment.confirmed
- Producer: Payment Service
- Consumers: Audit Service
- Partitions: 3
- Purpose: Notify successful payment processing

## payment.failed
- Producer: Payment Service
- Consumers: Audit Service
- Partitions: 3
- Purpose: Notify failed payment processing

## Dead Letter Topics

### order.created.dlt
- Purpose: Store events that failed after retries

### payment.failed.dlt
- Purpose: Store failed payment events after retries
