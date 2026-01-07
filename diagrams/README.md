### Arquitectura

La plataforma utiliza una arquitectura orientada a eventos basada en Kafka.
El API Gateway actúa como punto único de entrada, mientras que los microservicios
se comunican de forma asíncrona mediante eventos de dominio.

Cada servicio es dueño de su base de datos y no existe comunicación directa
entre servicios, reduciendo el acoplamiento y mejorando la resiliencia.
