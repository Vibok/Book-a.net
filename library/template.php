<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */
namespace Base\Library {

	use Base\Core\Model,
        Base\Core\Exception;

	/*
	 * Clase para gestionar las plantillas de los emails automáticos
	 */
    class Template {

        public
            $id,
            $lang,
            $name,
            $purpose,
            $title,
            $text;

        static public function get ($id, $lang = \LANG) {

			$sql = "SELECT  
                        *,
                        IFNULL(title_".$lang.", title_es) as title,
                        IFNULL(text_".$lang.", text_es) as text
                     FROM template
                     WHERE template.id = :id
                ";

			$query = Model::query($sql, array(':id' => $id));
			$template = $query->fetchObject(__CLASS__);
            return $template;
		}

		/*
		 *  Metodo para la lista completa de plantillas
		 */
		public static function getAll() {
            $templates = array();

            try {

                $sql = "SELECT
                            *,
                            IFNULL(title_".\LANG.", title_es) as title,
                            IFNULL(text_".\LANG.", text_es) as text
                        FROM template
                        ORDER BY name ASC
                        ";

                $query = Model::query($sql);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $template) {
                    $templates[] = $template;
                }
                return $templates;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

		/*
		 *  Lista de plantillas para filtro
		 */
		public static function getAllMini() {
            $templates = array();

            try {
                $sql = "SELECT
                            template.id as id,
                            template.name as name
                        FROM template
                        ORDER BY name ASC
                        ";

                $query = Model::query($sql);
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $template) {
                    $templates[$template->id] = $template->name;
                }
                return $templates;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

        public function validate(&$errors = array()) {

            $allok = true;

            if (empty($this->id)) {
                $errors[] = 'Registro sin id';
                $allok = false;
            }

            if (empty($this->title_es)) {
                $errors[] = 'Registro sin asunto';
                $allok = false;
            }

            if (empty($this->text_es)) {
                $errors[] = 'Registro sin contenido';
                $allok = false;
            }

            return $allok;
        }

		/*
		 *  Esto se usara para la gestión de contenido
		 */
		public function save(&$errors = array()) {
            if(!$this->validate($errors)) { return false; }

  			try {
                $values = array(
                    ':template' => $this->id,
                    ':name' => $this->name,
                    ':purpose' => $this->purpose,
                    ':title_es' => $this->title_es,
                    ':title_en' => $this->title_en,
                    ':text_es' => $this->text_es,
                    ':text_en' => $this->text_en
                );

				$sql = "REPLACE INTO template
                            (id, name, purpose, title_es, title_en, text_es, text_en)
                        VALUES
                            (:template, :name, :purpose, :title_es, :title_en, :text_es, :text_en)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con <pre>" . print_r($values, 1) . "</pre>";
                    return false;
                }
                
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la palntilla. ' . $e->getMessage();
                return false;
			}

		}


	}
}