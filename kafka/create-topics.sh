#!/bin/bash

KAFKA_BROKER="kafka:9092"

echo "Waiting for Kafka to be ready..."
sleep 10

echo "Creating Kafka topics..."

kafka-topics \
  --bootstrap-server $KAFKA_BROKER \
  --create \
  --if-not-exists \
  --topic order.created \
  --replication-factor 1 \
  --partitions 3

kafka-topics \
  --bootstrap-server $KAFKA_BROKER \
  --create \
  --if-not-exists \
  --topic payment.confirmed \
  --replication-factor 1 \
  --partitions 3

kafka-topics \
  --bootstrap-server $KAFKA_BROKER \
  --create \
  --if-not-exists \
  --topic payment.failed \
  --replication-factor 1 \
  --partitions 3

kafka-topics \
  --bootstrap-server $KAFKA_BROKER \
  --create \
  --if-not-exists \
  --topic order.created.dlt \
  --replication-factor 1 \
  --partitions 1

kafka-topics \
  --bootstrap-server $KAFKA_BROKER \
  --create \
  --if-not-exists \
  --topic payment.failed.dlt \
  --replication-factor 1 \
  --partitions 1

echo "Kafka topics created successfully."
