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
        Base\Core\Exception,
        Base\Library\Check;

	/*
	 * Clase para gestionar el contenido de las páginas institucionales
	 */
    class Page {

        public
            $id,
            $name,
            $text_es,
            $text_en,
            $url,
            $content_es,
            $content_en,
            $order;

        static public function get ($id) {

            // buscamos la página
			$sql = "SELECT  *,
                        IF(text_".\LANG." = '', text_es, text_".\LANG.") as text,
                        IF(content_".\LANG." = '', content_es, content_".\LANG.") as content
                     FROM page
                     WHERE page.id = :id
                ";

			$query = Model::query($sql, array(':id' => $id));
			$page = $query->fetchObject(__CLASS__);
            return $page;
		}

		/*
		 *  Metodo para la lista de páginas
		 */
		public static function getAll() {
            $pages = array();

            try {

                $sql = "SELECT  *,
                            IFNULL(text_".\LANG.", text_es) as text,
                            IFNULL(content_".\LANG.", content_es) as content
                        FROM page
                        ORDER BY `order` ASC
                        ";

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $page) {
                    $pages[] = $page;
                }
                return $pages;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

        public function validate(&$errors = array()) {

            $allok = true;

            if (empty($this->id)) {
                $errors[] = 'Falta identificador';
                $allok = false;
            }

            if (empty($this->name)) {
                $errors[] = 'Falta nombre';
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
                    ':id' => $this->id,
                    ':text_es' => $this->text_es,
                    ':text_en' => $this->text_en,
                    ':content_es' => $this->content_es,
                    ':content_en' => $this->content_en
                );

				$sql = "UPDATE page
                            SET text_es = :text_es,
                                text_en = :text_en,
                                content_es = :content_es,
                                content_en = :content_en
                            WHERE id = :id
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con " . \trace($values);
                    return false;
                }
                
			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la pagina. ' . $e->getMessage();
                return false;
			}

		}

		/*
		 *  Esto se usara para la gestión de contenido
		 */
		public function add(&$errors = array()) {
            if(!$this->validate($errors)) { return false; }

  			try {
                // primero verificar id unico
                $sql = "SELECT  id
                         FROM page
                         WHERE page.id = :id
                    ";

                $query = Model::query($sql, array(':id' => $this->id));
                $exist = $query->fetchColumn();
                if ($exist) {
                    $errors[] = "Ya existe una página con este identificador, ponle otro identificador (que sea reprresentativo del contenido de la página, por favor).";
                    return false;
                }
                
                $values = array(
                    ':id' => $this->id,
                    ':name' => $this->name,
                    ':url' => '/about/'.$this->id,
                    ':order' => $this->order
                );

				$sql = "INSERT INTO page
                            (id, name, url, `order`)
                        VALUES
                            (:id, :name, :url, :order)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    $errors[] = "Ha fallado $sql con " . \trace($values);
                    return false;
                }

			} catch(\PDOException $e) {
                $errors[] = 'Error sql al grabar el contenido de la pagina. ' . $e->getMessage();
                return false;
			}

		}
        
        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id) {
            return Check::reorder($id, 'up', 'page', 'id', 'order');
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id) {
            return Check::reorder($id, 'down', 'page', 'id', 'order');
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM page');
            $order = $query->fetchColumn(0);
            return ++$order;

        }

	}
}