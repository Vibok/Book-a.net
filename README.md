El código licenciado aquí bajo la GNU Affero General Public License, versión 3 [AGPL-3.0](http://www.gnu.org/licenses/agpl-3.0.html) ha sido desarrollado por el equipo de VibokWorks en base al código liberado de Goteo.org (https://github.com/Goteo/Goteo). Un resumen de las modificaciones realizadas se puede encontrar en el archivo /doc/plataforma_book-a.doc 

Se trata de una herramienta web que permite la gestión de campañas de micromecenazgo de proyectos literarios propios. Mediante el sistema también se permite gestionar la comunicación segura y distribuida con los usuarios y entre estos, administración de proyectos destacados en portada y creación de publicaciones periódiocas tipo blog, sección de FAQs y páginas estáticas. 

Es una versión modificada de Goteo, exceptuando los módulos propios de pasarela de pago por TPV y PayPal, cuyo desarrollo y adaptación deben llevarse a cabo por parte de quien lo implemente, en correspondencia con la licencia especificada y sin responsabilidad de mantenimiento, jurídica o de ningún otro tipo por parte de VibokWorks. 

Esta primera versión se facilita según es accesible desde este repositorio sin documentación adicional más allá de los requerimientos técnicos, sin posibilidad actualmente de asesoramiento en su instalación o personalización ni dedicación a la resolución de incidencias técnicas por parte del equipo desarrollador.

Instrucciones para la implementación:
- Subir al alojamiento los archivos del repositorio (excepto .sql y .doc)
- Crear una base de datos y ejecutar en ella el script /db/goteo.sql
- Especificar los credenciales de conexión a la base de datos en el archivo /config.php (contantes GOTEO_DB_*)

Hay una guía de instalación más detallada en /dov/installation_guide.txt
Los detalles técnicos se encuentran en el archivo /doc/plataforma_book-a.doc

La propiedad intelectual de la plataforma Book-a.net pertenece a 03INNOVA24H SLU y ha sido gestionada por http://www.safecreative.org/

CREDITOS
Desarrollo herramienta (conceptualización, arquitectura de la información, textos, programación y diseño de interface):
Paula Alvarez, Francisco Cruz, Julián Cánaves

Traducción de interface y textos: Crystal Weber

Asesoría legal y privacidad de datos: S.A.G. MEN

Other code writers: Miguel Angel Sanchez
  
Developed with usage of:
	html, css, xml, javascript
	php, php PEAR packages, various licensed php classes,
	jquery and licensed jquery plugins (SlideJS, CKeditor, Tipsy, MouseWheel, jScrollPane, FancyBox, DatePicker )

