El c�digo licenciado aqu� bajo la GNU Affero General Public License, versi�n 3 [AGPL-3.0](http://www.gnu.org/licenses/agpl-3.0.html) ha sido desarrollado por el equipo de VibokWorks en base al c�digo liberado de Goteo.org (https://github.com/Goteo/Goteo). Un resumen de las modificaciones realizadas se puede encontrar en el archivo /doc/plataforma_book-a.doc 

Se trata de una herramienta web que permite la gesti�n de campa�as de micromecenazgo de proyectos literarios propios. Mediante el sistema tambi�n se permite gestionar la comunicaci�n segura y distribuida con los usuarios y entre estos, administraci�n de proyectos destacados en portada y creaci�n de publicaciones peri�diocas tipo blog, secci�n de FAQs y p�ginas est�ticas. 

Es una versi�n modificada de Goteo, exceptuando los m�dulos propios de pasarela de pago por TPV y PayPal, cuyo desarrollo y adaptaci�n deben llevarse a cabo por parte de quien lo implemente, en correspondencia con la licencia especificada y sin responsabilidad de mantenimiento, jur�dica o de ning�n otro tipo por parte de VibokWorks. 

Esta primera versi�n se facilita seg�n es accesible desde este repositorio sin documentaci�n adicional m�s all� de los requerimientos t�cnicos, sin posibilidad actualmente de asesoramiento en su instalaci�n o personalizaci�n ni dedicaci�n a la resoluci�n de incidencias t�cnicas por parte del equipo desarrollador.

Instrucciones para la implementaci�n:
- Subir al alojamiento los archivos del repositorio (excepto .sql y .doc)
- Crear una base de datos y ejecutar en ella el script /db/goteo.sql
- Especificar los credenciales de conexi�n a la base de datos en el archivo /config.php (contantes GOTEO_DB_*)

Hay una gu�a de instalaci�n m�s detallada en /dov/installation_guide.txt
Los detalles t�cnicos se encuentran en el archivo /doc/plataforma_book-a.doc

La propiedad intelectual de la plataforma Book-a.net pertenece a 03INNOVA24H SLU y ha sido gestionada por http://www.safecreative.org/

CREDITOS
Desarrollo herramienta (conceptualizaci�n, arquitectura de la informaci�n, textos, programaci�n y dise�o de interface):
Paula Alvarez, Francisco Cruz, Juli�n C�naves

Traducci�n de interface y textos: Crystal Weber

Asesor�a legal y privacidad de datos: S.A.G. MEN

Other code writers: Miguel Angel Sanchez
  
Developed with usage of:
	html, css, xml, javascript
	php, php PEAR packages, various licensed php classes,
	jquery and licensed jquery plugins (SlideJS, CKeditor, Tipsy, MouseWheel, jScrollPane, FancyBox, DatePicker )

