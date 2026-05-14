# Lexi - Roadmap de base de datos

## Estado actual

Lexi ya cubre bien el nucleo del producto actual:

- usuarios, roles e idiomas
- catalogo de palabras, categorias y traducciones
- progreso del usuario sobre palabras
- colecciones por idioma
- ejercicios e intentos
- suscripciones y planes basicos
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
- `plans`
- `subscriptions`
- `ai_generations`
- tablas internas de Laravel: `migrations`, `sessions`, `jobs`, `cache`, etc.

## Diferencias frente al diseño ampliado

El modelo guardado por el proyecto es mas ambicioso que el esquema actual en tres zonas:

1. Ejercicios

Ahora mismo `exercises` + `exercise_attempts` resuelven el MVP, pero siguen siendo un modelo compacto.

Faltaria normalizar si Lexi va a tener:

- plantillas reutilizables
- preguntas multiples por ejercicio
- opciones por pregunta
- instancias personalizadas por usuario
- respuestas detalladas por intento

2. Billing freemium

Ahora mismo `plans` y `subscriptions` cubren el estado comercial minimo.

Faltaria separar si se quiere monetizacion real:

- `plan_features`
- `payments`
- `user_usage`

3. Roles de profesor

El esquema contempla `teacher`, pero aun no existe una funcionalidad real de profesor/alumno.

Faltaria:

- `teacher_student`

## Extensiones recomendadas por orden

### 1. `user_usage`

Es la extension con mejor relacion valor/riesgo para el estado actual.

Tiene sentido porque Lexi ya tiene:

- IA (`ai_generations`)
- planes (`plans`)
- suscripciones (`subscriptions`)

Y permite controlar bien:

- limites de generaciones IA por periodo
- limites de ejercicios generados
- futura logica free vs premium sin inferir todo desde logs pesados

Campos recomendados:

- `id`
- `user_id`
- `period_start`
- `period_end`
- `ai_generations_count`
- `exercises_generated_count`
- `created_at`
- `updated_at`
- indice unico por `user_id`, `period_start`, `period_end`

### 2. `plan_features`

Tiene sentido cuando el plan deje de depender de un JSON en `plans.features` y se quiera gobernar el producto por capacidades concretas.

Ejemplos de `feature_key`:

- `ai.daily_limit`
- `ai.models.advanced`
- `stats.advanced`
- `collections.max`

Recomendacion:

- no mantener a la vez `plans.features` y `plan_features` mucho tiempo
- cuando se migre, dejar `plan_features` como fuente de verdad

### 3. `attempt_answers`

Es la mejor extension si el foco del producto pasa a calidad pedagogica y analitica.

Aporta:

- correccion por item
- feedback detallado
- errores frecuentes
- analitica real por tipo de pregunta
- base para revision espaciada mas inteligente

Tiene sentido incluso antes de separar por completo `exercise_templates`, si cada intento necesita guardar respuestas granulares.

## Extensiones recomendadas solo cuando exista la funcion

### `exercise_templates`, `exercise_items`, `exercise_options`, `exercise_instances`

Muy coherente si Lexi va hacia:

- authoring editorial serio
- ejercicios IA personalizados por usuario
- tipos complejos como matching, fill blank, choice, speaking, writing

No merece la pena meterlas aun si el producto sigue en un flujo compacto de ejercicio + intento.

La señal para introducirlas es clara:

- cuando un ejercicio deje de ser un bloque JSON simple
- cuando haya que versionar plantillas
- cuando admin o teacher creen ejercicios reutilizables

### `payments`

Tiene sentido solo cuando entre una pasarela real.

Mientras `subscriptions` sea manual o semilla de demo, `payments` solo anade ruido.

La señal correcta es:

- Stripe, PayPal o proveedor real
- reconciliacion de cobros
- estados `paid`, `failed`, `refunded`

### `teacher_student`

Solo cuando exista de verdad el rol `teacher` en producto.

Antes de eso, seria una tabla huerfana.

La señal correcta es:

- dashboard docente
- alumnos asignados
- seguimiento compartido
- ejercicios asignados por profesor

## Recomendacion practica para Lexi ahora

Si hubiera que ampliar el esquema ya, sin inflarlo, el orden correcto seria:

1. `user_usage`
2. `attempt_answers`
3. `plan_features`

Y dejaria para despues:

1. `exercise_templates`
2. `exercise_items`
3. `exercise_options`
4. `exercise_instances`
5. `payments`
6. `teacher_student`

## Criterio de diseño

Para Lexi conviene mantener esta regla:

- anadir tablas cuando una capacidad del producto lo exija de forma estable
- no adelantar normalizacion compleja si aun no hay authoring, cobro real o modo profesor
- priorizar tablas que mejoren control de negocio y analitica sin complicar todo el modelo
