# Repositorio MIIDT
**Repositorio digital de tesis** de la Maestría en Ingeniería para la Innovación y Desarrollo Tecnológico (MIIDT) de la Universidad Autónoma de Guerrero (UAGro).

🌐 **Sitio en producción:** [https://www.desa-posgradoingenieria.uagro.mx/](https://www.desa-posgradoingenieria.uagro.mx/)

### Descripción del Proyecto

El **Repositorio MIIDT** es una aplicación web que funciona como un catálogo digital para las tesis del posgrado de la Maestría en Ingeniería para la Innovación y Desarrollo Tecnológico. Permite a estudiantes, docentes e investigadores:

- **Consultar** tesis registradas por Línea de Investigación (LIES).
- **Buscar** tesis por título, autor, director o línea de investigación.
- **Filtrar** resultados por estado, director de tesis y año de publicación.
- **Visualizar** la portada de cada tesis y acceder a la vista previa del PDF (requiere conexión a la red institucional).
- **Administrar** el contenido del repositorio a través de un panel de administración (CRUD).


## Líneas de Investigación (LIES)

| Siglas | Nombre completo | Descripción |
|--------|----------------|-------------|
| **CSR** | Construcción Sismo-Resistente | Estudio de la sismo-resistencia y mitigación del riesgo sísmico en edificaciones. |
| **Geomática** | Geomática | Tecnologías aplicadas al modelado espacial y manejo de información geográfica. |
| **TICs** | Tecnologías de la Información y Comunicación | Programación de aplicaciones informáticas, bases de datos y tecnologías computacionales. |


## Restricción de Acceso a Documentos PDF

> ⚠️ **Nota Importante:** La visualización de los archivos PDF de las tesis **únicamente está disponible para maestros y estudiantes que se encuentren conectados a la red interna de la institución** (red UAGro). Esta restricción aplica al botón de "Vista previa PDF" disponible en las tarjetas de cada tesis. Fuera de la red institucional, solo es posible consultar la información bibliográfica y la portada de cada tesis.


## Tecnologías Utilizadas

| Tecnología | Uso |
|-----------|-----|
| **PHP** | Lenguaje del lado del servidor para la lógica de negocio, controladores y modelos. |
| **HTML5** | Estructura y maquetación de las vistas del sitio web. |
| **CSS3** | Estilos personalizados y diseño visual. |
| **JavaScript** | Interactividad del lado del cliente (filtros dinámicos, modales, búsqueda). |
| **Bootstrap 5** | Framework CSS para diseño responsivo y componentes UI prediseñados. |
| **PHPStorm** | IDE utilizado para el desarrollo y despliegue inmediato de cambios al servidor. |

##  Arquitectura del Proyecto

El proyecto sigue el patrón de arquitectura **MVC (Modelo - Vista - Controlador)** dentro del panel de administración (`admin/`), y una estructura modular para el sitio público.

### Estructura de Carpetas

```
REPOSITORIO_MIIDT/
│
├── assets/                  
│   ├── css/                  #    Hojas de estilo
│   ├── img/                  #    Archivos de imagen (logo, grupo)
│   │   ├── imagene_inicio_index/ 
│   │   ├── imagenes_tarjetas/    
│   │   └── popups/           #    Portadas oficiales para popup
│   ├── js/                   #    Scripts JS
│   └── vendor/               #    Dependencias y librerías externas
│       └── bootstrap/        #    Framework CSS/JS (bootstrap.min.css, bootstrap.bundle.min.js)
├── config/                   #    Archivos de configuración
├── partials/                 #    Componentes reutilizables (header, footer, tesis_view_content)
├── src/                      #    Código fuente auxiliar 
│   └── modules/              #    DownloadModule.php (red) y TesisModule.php (DB)
├── tesis-miidt/              #    Páginas públicas de consulta de tesis por LIES
│   ├── busqueda_ajax.php     #    Lógica de filtros y búsqueda
│   ├── descargar_tesis.php   #    Lógica de la vista/descarga
│   └── lies/                 #    Archivos ensambladores de las líneas (crs.php, etc.)
├── uploads/portadas/         #    Imágenes miniatura de tesis (.webp) para tarjetas
│   ├── Linea_CSR/            #    Miniaturas de tesis CSR
│   ├── Linea_Geomatica/      #    Miniaturas de tesis Geomática
│   └── Linea_TICs/           #    Miniaturas de tesis TICs
│
├── admin/                    #    Panel de administración (CRUD)
│   ├── assets/               #    Recursos estáticos del panel admin
│   ├── controllers/          #    Controladores (lógica de negocio)
│   ├── models/               #    Modelos (interacción con la base de datos)
│   ├── views/                #    Vistas del panel de administración
│   └── index.php             #    Punto de entrada del panel admin
│
├── .app.env                  #    Variables de entorno (conexión BD, configuraciones)
├── .gitignore                #    Archivos y carpetas excluidos del control de versiones
└── index.php                 #    Punto de entrada principal del sitio público
```

## 📂 Descripción Detallada de Cada Carpeta y Archivo

###  `assets/` — Recursos Estáticos Públicos

Contiene todos los recursos estáticos que se utilizan en la parte pública del sitio web:

- **CSS/** — Hojas de estilo personalizadas para el diseño visual del sitio.
- **JS/** — Scripts de JavaScript para funcionalidades interactivas (búsqueda en tiempo real, filtros dinámicos, modales de visualización de portadas).
- **img/** — Conjunto principal de imágenes. Incluye archivos sueltos como logotipos y fotos grupales (`logo.webp`, `grupo-interno.webp`, etc.) y está sub-dividido en:
  - **imagene_inicio_index/** — Imágenes utilizadas en la vista principal (carrusel o banners de inicio).
  - **imagenes_tarjetas/** — Imágenes empleadas en la estructura de las tarjetas u otras secciones visuales.
  - **popups/** — Contiene las imágenes a tamaño completo de las portadas oficiales, divididas por línea de investigación, que se muestran en el popup de "Visualizar Portada".
- **vendor/** — Dependencias o librerías de terceros (reemplaza a la habitual carpeta de fuentes). Principalmente almacena **Bootstrap** (`vendor/bootstrap/`), que incluye sus hojas de estilo base (`css/bootstrap.min.css`) y los scripts necesarios para su interactividad (`js/bootstrap.bundle.min.js`).

###  `config/` — Configuración

Archivos de configuración del sistema:

- Conexión a la base de datos (credenciales, host, nombre de la BD).
- Constantes y parámetros globales del proyecto.
- Configuraciones de rutas o ambientes de desarrollo/producción.

###  `partials/` — Componentes Reutilizables

Fragmentos de código HTML/PHP que se reutilizan en múltiples páginas para mantener la consistencia del diseño y evitar duplicación de código:

- **header.php** — Encabezado del sitio (logotipo UAGro, metadatos, enlaces a CSS) y **barra de navegación** principal.
- **footer.php** — Pie de página (información de contacto, enlaces, redes sociales).
- **tesis_view_content.php** — Estructura principal de la vista de tesis, que incluye el diseño de las tarjetas, la barra de búsqueda y los filtros laterales.

###  `src/` — Código Fuente Auxiliar

Carpeta que contiene código fuente auxiliar y la estructura principal del backend modular de la aplicación pública.
Destaca la subcarpeta **`modules/`**, encargada de operaciones críticas:
- **`DownloadModule.php`** — Maneja la parte de la validación de red para garantizar que las tesis en PDF solo puedan ser visualizadas si el usuario está conectado a la red institucional de la UAGro.
- **`TesisModule.php`** — Se encarga de las consultas a la base de datos (DB) y el manejo de datos primarios para obtener y filtrar información de las tesis.

###  `tesis-miidt/` — Páginas Públicas de Tesis

Contiene la lógica y las páginas PHP dedicadas a la consulta pública de tesis, integrando backend y frontend.
Archivos principales en la raíz de la carpeta:
- **`busqueda_ajax.php`** — Archivo encargado de procesar y devolver los resultados de las búsquedas y de los distintos filtros de forma dinámica (Ajax).
- **`descargar_tesis.php`** — Ejecuta la operación pública correspondiente a la visualización de la tesis, conectando la petición de la web con el módulo de descarga.

Subcarpeta **`lies/`**:
Directorio donde residen los puntos de entrada para cada Línea de Investigación:
- **`crs.php`** — Construcción Sismo-Resistente.
- **`geomatica.php`** — Geomática.
- **`tics.php`** — Tecnologías de la Información y Comunicación.

Estos tres archivos funcionan como "ensambladores": no contienen lógica pesada, sino que llaman a los componentes necesarios (`header`, `footer`, carruseles, etc.) y solicitan mostrar la estructura correspondiente con las tesis filtradas para su respectiva LIES.

Todas estas vistas integran las funcionalidades de:
- Barra de búsqueda y filtros laterales (Estado, Director de tesis, Año).
- Tarjetas informativas de cada tesis.
- Botones de "Visualizar Portada" y "Vista previa PDF".

### Portadas de Tesis (Miniaturas y Popups)

Cada tesis registrada en el sistema cuenta con **dos imágenes** que se almacenan en diferentes rutas dentro del proyecto, dependiendo de su función en la interfaz:

| Imagen | Ubicación | Formato | Descripción |
|--------|-----------|---------|-------------|
| **Portada en tarjeta** | `uploads/portadas/Linea_XXX/` | `.webp` | Imagen miniatura que se muestra directamente en la tarjeta de cada tesis dentro de la página de consulta. |
| **Portada oficial (popup)** | `assets/img/popups/linea_XXX/` | `.png` | Imagen a tamaño completo de la portada oficial de la tesis, que se despliega al hacer clic en el botón **"Visualizar Portada"**. El nombre del archivo sigue el formato `Nombre_resultado.png`. |

###  `admin/` — Panel de Administración

El panel de administración permite gestionar el contenido del repositorio (agregar, editar, eliminar y consultar tesis). Está estructurado bajo el patrón **MVC**:

| Carpeta/Archivo | Capa MVC | Descripción |
|----------------|----------|-------------|
| `admin/controllers/` | **Controlador** | Contiene los archivos PHP que reciben las solicitudes del usuario, procesan la lógica de negocio y coordinan la comunicación entre los modelos y las vistas. Ejemplo: procesar el formulario de registro de una nueva tesis. |
| `admin/models/` | **Modelo** | Contiene los archivos PHP encargados de la interacción directa con la base de datos. Aquí se realizan las operaciones CRUD (Create, Read, Update, Delete) mediante consultas SQL. Ejemplo: insertar una nueva tesis, obtener la lista de tesis filtradas. |
| `admin/views/` | **Vista** | Contiene los archivos PHP/HTML que generan la interfaz de usuario del panel de administración. Son las páginas que el administrador visualiza e interactúa. Ejemplo: formulario de registro de tesis, tabla de listado de tesis. |
| `admin/assets/` | **Recursos** | Archivos estáticos exclusivos del panel admin: hojas de estilo CSS, scripts JavaScript e imágenes utilizadas en la interfaz de administración. |
| `admin/index.php` | **Punto de entrada** | Archivo principal del panel admin. Funciona como el enrutador o punto de acceso inicial que carga los controladores y vistas correspondientes. |

### Archivos Raíz

| Archivo | Descripción |
|---------|-------------|
| `index.php` | Punto de entrada principal del sitio público. Presenta la página de inicio con la descripción de la Maestría y las tres líneas de investigación con sus respectivas tarjetas interactivas. |
| `.gitignore` | Define los archivos y carpetas que Git debe ignorar (como `.app.env`, carpetas temporales, dependencias, etc.). |

## 🖼️ Capturas de Pantalla

### Página de Inicio

Vista principal del repositorio mostrando la descripción de la Maestría y las tres líneas de investigación (CSR, Geomática, TICs) con tarjetas interactivas.

<img width="1919" height="981" alt="image" src="https://github.com/user-attachments/assets/9ca55cb1-8bbf-477c-b443-474828acb846" />


### Menú de Navegación — LIES

Menú desplegable que permite acceder a las diferentes líneas de investigación disponibles.

<img width="1919" height="968" alt="image" src="https://github.com/user-attachments/assets/4bf19639-5322-483a-b7e5-0b6230aec0cb" />


### Vista de Tesis — Construcción Sismo-Resistente (CSR)

Página de consulta de tesis de la línea CSR, con barra de búsqueda, tarjetas de tesis y filtros laterales.

<img width="1908" height="870" alt="image" src="https://github.com/user-attachments/assets/c906ed78-41f8-410b-b316-53f5ff56c1a6" />

### Vista de Tesis — Geomática

Página de consulta de tesis de la línea Geomática, con las mismas funcionalidades de búsqueda y filtrado.

<img width="1919" height="866" alt="image" src="https://github.com/user-attachments/assets/29f432a8-b313-4b49-a946-6a5d6c0d5889" />


### Vista de Tesis — Tecnologías de la Información y Comunicación (TICs)

Página de consulta de tesis de la línea TICs, con las mismas funcionalidades de búsqueda y filtrado.

<img width="1919" height="875" alt="image" src="https://github.com/user-attachments/assets/d0e6da30-0c4f-4d8d-8e2f-f8816a5d0046" />


## Licencia

Este proyecto es desarrollado y mantenido por la **Maestría en Ingeniería para la Innovación y Desarrollo Tecnológico (MIIDT)** de la **Universidad Autónoma de Guerrero (UAGro)**.

**© 2025 Universidad Autónoma de Guerrero — MIIDT. Todos los derechos reservados.**

Este software es de uso exclusivo para fines académicos e institucionales de la Universidad Autónoma de Guerrero. Queda prohibida su reproducción, distribución o uso comercial sin autorización previa por escrito del coordinador del programa.

Para solicitudes de uso o colaboración, contactar al coordinador de la MIIDT.

## Contacto

| | |
|---|---|
| **Coordinador** | Dr. René Edmundo Cuevas Valencia |
| **Programa** | Maestría en Ingeniería para la Innovación y Desarrollo Tecnológico |
| **Dirección** | Av. Lázaro Cárdenas S/N, Ciudad Universitaria, Chilpancingo, Gro., CP: 39090 |
| **Teléfono** | (747) 4719310 ext. 4153 |
| **Correo** | [miidt@uagro.mx](mailto:miidt@uagro.mx) |
| **Sitio web** | [https://www.miidt.uagro.mx](https://www.miidt.uagro.mx/index.php) |

## Créditos

Desarrollado por el equipo de desarrollo web de la MIIDT — UAGro.

<p align="center">
  <strong>Universidad Autónoma de Guerrero</strong><br>
  Maestría en Ingeniería para la Innovación y Desarrollo Tecnológico<br>
  <em>Repositorio MIIDT</em>
</p>
