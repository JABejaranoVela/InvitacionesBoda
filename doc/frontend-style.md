# Frontend: Estética, Criterios y Aplicación

## 1) Objetivo del diseño
Construir una estética editorial y elegante para invitaciones de boda, con tipografía distintiva, contraste impecable y una composición cuidada que mantenga personalidad en cualquier tamaño de pantalla.

## 2) Directrices obligatorias (resumen)
- **Tipografía:** evitar fuentes genéricas (Inter/Roboto/Arial). Combinar una serif con carácter para títulos y una sans refinada para texto.
- **Color & tema:** paleta cohesionada, dominantes claras y acentos definidos. Variables CSS obligatorias para consistencia.
- **Movimiento:** usar animaciones con intención (entrada y hover), preferentemente CSS. Menos micro-animaciones dispersas, más momentos clave.
- **Composición:** layouts con intención, posibilidad de asimetría, solapes o breaks de grid cuando tenga sentido.
- **Fondos y atmósfera:** evitar fondos planos por defecto. Preferir gradientes, textura sutil o capas para profundidad.
- **Prohibido:** estética genérica, fuentes trilladas, gradientes morados cliché, componentes “plantilla”.

## 3) Aplicación en este repositorio
### Tipografía
- **Display:** Cormorant Garamond (`font-display`).
- **Cuerpo:** Alegreya Sans (`font-body`).
- **Resultado:** títulos con presencia clásica y cuerpo legible sin verse genérico.

### Color y contraste
- **Overlay hero:** `bg-paper/80` para permitir texto oscuro.
- **Caja de hero:** fondo blanco sólido con borde y sombra para asegurar legibilidad en cualquier foto.
- **Texto hero:** `text-ink` (no blanco) para contraste real.
- **Botón blanco:** texto en negro (`text-ink`).
- **Paleta:** variables CSS en `:root` con tonos cálidos y oliva.

### Fondo y atmósfera
- Fondo general con **gradiente cálido** (no plano), evitando clichés.

### Responsive
- Cards con `flex-wrap` y miniaturas más pequeñas para evitar desbordes en <375px.

## 4) Archivos clave
- `src/styles/global.css` (fuentes, variables, fondo)
- `src/components/Hero.astro` (contraste del hero)
- `src/components/TemplateCard.astro` (cards responsive)
- `src/pages/index.astro` (CTA y copy)
- `src/data/templates.ts` (precios y etiquetas)

## 5) Checklist de cumplimiento
- [x] Tipografías distintivas (no Inter/Roboto/Arial).
- [x] Texto del hero visible (color oscuro + overlay claro).
- [x] Botón blanco con texto oscuro.
- [x] Cards sin overflow en móviles pequeños.
- [x] Paleta cohesionada con variables CSS.

## 6) Si algo no cumple
- Ajustar contraste (color/overlay) antes de tocar copy.
- Cambiar fuente si cae en el grupo “genérico”.
- Revisar spacing en mobile si aparece desborde.
- Evitar diseños comunes o neutros sin personalidad.

## 7) Texto base de referencia (entregado)
Las directrices completas se conservaron en `doc/frontend-aesthetics-guidelines.md`.
