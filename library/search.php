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

    use Base\Model\Booka;

	/*
	 * Clase para realizar búsquedas de Bookas
	 *
	 */
    class Search {

        /**
         * Metodo para buscar un textxto entre todos los contenidos de texto de un proyecto
         * @param string $value
         * @return array results
         */
		public static function text ($value) {

            $results = array();

            $values = array(':text'=>"%$value%");

            $sql = "SELECT id
                    FROM booka
                    WHERE status > 2
                    AND (name_es LIKE :text
                        OR description_es LIKE :text
                        OR motivation_es LIKE :text
                        OR about_es LIKE :text
                        OR goal_es LIKE :text
                        OR related_es LIKE :text
                        OR keywords_es LIKE :text
                        )
                    ORDER BY name_es ASC";

            try {
                $query = Booka::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $match) {
                    $results[] = Booka::getMedium($match->id);
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception('Fallo la sentencia de busqueda');
            }
		}

        /**
         * Metodo para realizar una busqueda por parametros
         * @param array multiple $params 'category', 'location', 'reward'
         * @param bool showall si true, muestra tambien Bookas en estado de edicion y revision
         * @return array results
         */
		public static function params ($params, $showall = false, $limit = null) {

            $results = array();
            $where   = array();
            $values  = array();

            if (!empty($params['category'])) {
                $where[] = 'AND id IN (
                                    SELECT distinct(booka)
                                    FROM booka_category
                                    WHERE category IN ('. implode(', ', $params['category']) . ')
                                )';
            }

            if (!empty($params['location'])) {
                $where[] = 'AND MD5(booka_location) IN ('. implode(', ', $params['location']) .')';
            }

            if (!empty($params['reward'])) {
                $where[] = 'AND id IN (
                                    SELECT DISTINCT(booka)
                                    FROM reward
                                    WHERE icon IN ('. implode(', ', $params['reward']) . ')
                                    )';
            }

            if (!empty($params['query'])) {
                $where[] = ' AND (name LIKE :text
                                OR description LIKE :text
                                OR motivation LIKE :text
                                OR about LIKE :text
                                OR goal LIKE :text
                                OR related LIKE :text
                                OR keywords LIKE :text
                                OR location LIKE :text
                            )';
                $values[':text'] = "%{$params['query']}%";
            }

            
            $minstatus = ($showall) ? '1' : '2';
            $maxstatus = ($showall) ? '4' : '7';

            $sql = "SELECT id
                    FROM booka
                    WHERE status > $minstatus
                    AND status < $maxstatus
                    ";
            
            if (!empty($where)) {
                $sql .= implode (' ', $where);
            }

            $sql .= " ORDER BY name ASC";
            // Limite
            if (!empty($limit) && \is_numeric($limit)) {
                $sql .= " LIMIT $limit";
            }

            try {
                $query = Booka::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $match) {
                    $results[] = Booka::getMedium($match->id);
                }
                return $results;
            } catch (\PDOException $e) {
                throw new Exception('Fallo la sentencia de busqueda');
            }
		}

	}

}