# Kafka Topics

## order.created
Produced by: Order Service  
Consumed by: Payment Service, Audit Service

## payment.confirmed
Produced by: Payment Service  
Consumed by: Audit Service

## payment.failed
Produced by: Payment Service  
Consumed by: Audit Service
