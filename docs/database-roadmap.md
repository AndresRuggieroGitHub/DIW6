# Lexi - Roadmap de base de datos

## Estado actual

Lexi ya cubre bien el nucleo del producto actual:

- usuarios, roles e idiomas
- catalogo de palabras, categorias y traducciones
- progreso del usuario sobre palabras
- colecciones por idioma
- ejercicios e intentos
- estructura normalizada base para plantillas, items, opciones, instancias y respuestas
- relacion futura profesor-alumno
- suscripciones y planes basicos
- capas representativas para features de plan, pagos y uso por periodo
- log editorial de generaciones IA

Tablas reales ya presentes:

- `users`
- `roles`
- `user_roles`
- `languages`
- `user_languages`
- `categories`
- `words`
- `translations`
- `user_words`
- `collections`
- `collection_words`
- `exercises`
- `exercise_attempts`
- `exercise_templates`
- `exercise_items`
- `exercise_options`
- `exercise_instances`
- `attempt_answers`
- `teacher_student`
- `plans`
- `plan_features`
- `subscriptions`
- `payments`
- `user_usage`
- `ai_generations`
- tablas internas de Laravel: `migrations`, `sessions`, `jobs`, `cache`, etc.

## Nota de producto

En Lexi, el bloque `free`/`premium` no debe leerse como una regla funcional ya conectada a IA ni como una politica cerrada del producto real.

Ahora mismo esas tablas sirven sobre todo para:

- representar modelo de negocio de forma coherente en el proyecto
- dejar preparado el dominio comercial
- evitar rediseñar la base cuando entre cobro real o reglas de plan mas concretas

## Estado de la ampliacion

Ya se ha implementado una ampliacion coherente del esquema en estas zonas:

1. Ejercicios

Se añadieron:

- `exercise_templates`
- `exercise_items`
- `exercise_options`
- `exercise_instances`
- `attempt_answers`

Esto no sustituye todavia al flujo compacto actual de `exercises` + `exercise_attempts`, pero deja preparada la normalizacion para crecer sin rehacer la base.

2. Relacion docente

Se añadió:

- `teacher_student`

La tabla existe, aunque el producto todavia no expone un modo profesor real.

3. Capa comercial y uso

Se añadieron:

- `plan_features`
- `payments`
- `user_usage`

Estas tablas representan bien el dominio comercial y de uso, pero no implican que la app ya funcione con restricciones premium reales.

## Semilla representativa

`DatabaseSeeder` ya deja datos minimos en las tablas ampliadas para que no queden vacias del todo:

- features de planes
- un pago de ejemplo
- un registro de uso por periodo
- una plantilla de ejercicio con items, opciones, instancia y respuestas

## Lo que sigue siendo futuro funcional

Aunque el esquema se ha ampliado, estas capacidades siguen sin estar conectadas como flujo de producto completo:

- editor completo de plantillas y items en admin
- asignacion real teacher-student
- cobro con pasarela real
- enforcement real de limites por plan
- analitica avanzada apoyada en `attempt_answers` y `user_usage`

## Recomendacion practica

La ampliacion correcta ya no es seguir creando tablas por inercia. Ahora el orden razonable seria conectar funcionalidad real sobre lo que ya existe:

1. usar `attempt_answers` en el guardado real de ejercicios
2. exponer `exercise_templates` e `exercise_items` en un flujo admin/editorial
3. usar `user_usage` para reporting o limites si en algun momento hace falta
4. usar `plan_features` como fuente de verdad cuando quieras dejar atras el JSON de `plans.features`

## Criterio de diseño

Para Lexi conviene mantener esta regla:

- anadir tablas cuando una capacidad del producto lo exija de forma estable
- no adelantar normalizacion compleja si aun no hay authoring, cobro real o modo profesor
- priorizar tablas que mejoren control de negocio y analitica sin complicar todo el modelo
