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

namespace Base\Model {

    use Base\Core\ACL,
        Base\Library\Check,
        Base\Library\Advice,
        Base\Library\Text,
        Base\Model\User,
        Base\Model\Image,
        Base\Model\Message;

    class Booka extends \Base\Core\Model {

        public
                $id = null,
                $dontsave = false,
                $process_step, // para actualizar los datos solo del paso que se ha tocado
                $owner, // User who created it
                $status,
                $collection, // colección
                $amount, // Current donated amount
                $user, // author's user information
                // Edit booka description
                $name,
                $clr_name, // nombre sin saltos de linea
                $lang = 'es',
                $image, // para gestionar la subida de imagenes
                $image2, // para gestionar la subida de imagenes2
                $gallery = array(), // array de instancias image de booka_image
                $gallery2 = array(), // array de instancias image de booka2_image
                
                // contenidos
                $description, // resumen/descripcion
                $about,  // sobre contenidos
                $motivation, // sobre el autor
                $goal, // sobre la edicion coleccion
                $related, // ficha tecnica
                
                // objetivos
                $milestone1, // recorrido de la campaña
                $milestone2, // hitos marcados
                $milestone3, // Límite y garantías
                $milestone4, // ¿Y después qué?
                
                $categories = array(),
                $media, // video
                $keywords, // por ahora se guarda en texto tal cual
                $author, // Autor/es del libro
                $info, // Información adicional
                $caption, // Pie de foto
                $media_caption, // Pie de video
                // costs
                $costs = array(), // booka\cost instances with type
                // Rewards
                $rewards = array(), // instances of booka\reward
                // Comment
                $comment, // Comentario para los admin introducido por el usuario
                //Obtenido, Cofinanciadores
                $invested = 0, //cantidad de inversión
                $investors = array(), // usuarios que han invertido

                $errors = array(), // para los fallos en los datos
                $okeys = array(), // para los campos que estan ok

                $messages = array(), // mensajes de los usuarios

                $finishable = false, // llega al progresso mínimo para enviar a revision

                $stageData = null;  // estado de la campaña por etapas

        /**
         * Inserta un proyecto con los datos mínimos
         *
         * @param array $data
         * @return boolean
         */

        public function create($data, &$errors = array()) {

            // cojemos el número de proyecto de este usuario
            $query = self::query("SELECT COUNT(id) as num FROM booka WHERE owner = ?", array($user));
            if ($now = $query->fetchObject())
                $num = $now->num + 1;
            else
                $num = 1;

            if (empty($data['id'])) {
                $data['id'] = md5(uniqid());
                $data['name'] = "Booka $num de " . $_SESSION['user']->name;
            } else {
                $data['name'] = $data['id'];
            }

            $values = array(
                ':id' => $data['id'],
                ':name_es' => $data['name'],
                ':owner' => 'booka',
                ':created' => date('Y-m-d')
            );

            $campos = array();
            foreach (\array_keys($values) as $campo) {
                $campos[] = \str_replace(':', '', $campo);
            }

            $sql = "REPLACE INTO booka (" . implode(',', $campos) . ")
                 VALUES (" . implode(',', \array_keys($values)) . ")";
            try {
                self::query($sql, $values);

                foreach ($campos as $campo) {
                    $this->$campo = $values[":$campo"];
                }

                return $this->id;
            } catch (\PDOException $e) {
                $errors[] = "ERROR al crear un nuevo proyecto<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>";
                \trace($this);
                die($errors[0]);
                return false;
            }
        }

        /*
         *  Cargamos los datos del proyecto
         */

        public static function get($id) {

            try {
                // metemos los datos del proyecto en la instancia
                $sql = "SELECT
                            *,
                            IFNULL(name_" . \LANG . ", name_es) as name,
                            IFNULL(info_" . \LANG . ", info_es) as info,
                            IFNULL(caption_" . \LANG . ", caption_es) as caption,
                            IFNULL(description_" . \LANG . ", description_es) as description,
                            IFNULL(motivation_" . \LANG . ", motivation_es) as motivation,
                            IFNULL(about_" . \LANG . ", about_es) as about,
                            IFNULL(goal_" . \LANG . ", goal_es) as goal,
                            IFNULL(related_" . \LANG . ", related_es) as related,
                            IFNULL(keywords_" . \LANG . ", keywords_es) as keywords,
                            IFNULL(media_" . \LANG . ", media_es) as media,
                            IFNULL(milestone1_" . \LANG . ", milestone1_es) as milestone1,
                            IFNULL(milestone2_" . \LANG . ", milestone2_es) as milestone2,
                            IFNULL(milestone3_" . \LANG . ", milestone3_es) as milestone3,
                            IFNULL(milestone4_" . \LANG . ", milestone4_es) as milestone4
                        FROM booka
                        WHERE id = ?";


                $query = self::query($sql, array($id));
                $booka = $query->fetchObject(__CLASS__);

                if (!$booka instanceof \Base\Model\Booka) {
                    throw new \Base\Core\Error('404', Text::html('fatal-error-booka'));
                }

                // nombre sin (sobre)saltos :P
                $booka->clr_name = self::clearName($booka->name);
                
                // Video
                if (isset($booka->media)) {
                    $booka->media = new Booka\Media($booka->media);
                }
                if (isset($booka->media_es)) {
                    $booka->media_es = new Booka\Media($booka->media_es);
                }
                if (isset($booka->media_en)) {
                    $booka->media_en = new Booka\Media($booka->media_en);
                }

                // owner
                $booka->user = User::getMini($booka->owner);

                // galeria
                $booka->gallery = Booka\Image::getGallery($booka->id, 'booka');
                $booka->image = Booka\Image::getFirst($booka->id);

                // galeria2
                $booka->gallery2 = Booka\Image::getGallery($booka->id, 'booka2');

                // coleccion
                $booka->collData = Collection::get($booka->collection);

                // categorias
                $booka->categories = Booka\Category::get($id);

                // necesidades
                $booka->costs = Booka\Cost::getAll($id);

                // recompensas
                $booka->rewards = Booka\Reward::getAll($id);

                //-----------------------------------------------------------------
                // Diferentes verificaciones segun el estado del proyecto
                //-----------------------------------------------------------------
                $booka->investors = Invest::investors($id);

                $amount = Invest::invested($id);
                if ($booka->invested != $amount) {
                    self::query("UPDATE booka SET amount = '{$amount}' WHERE id = ?", array($id));
                }
                $booka->invested = $amount;

                // sacamos rapidamente el coste total
                $cost_query = self::query("SELECT
                            (SELECT  SUM(amount)
                            FROM    cost
                            WHERE   booka = booka.id
                            ) as `cost`
                    FROM booka
                    WHERE id =?", array($booka->id));
                $booka->cost = $cost_query->fetchColumn();

                $booka->percent = round($booka->invested / $booka->cost * 100);
                
                //mensajes y mensajeros
                $booka->messages = Message::getAll($id);

                $booka->stageData();
                //-----------------------------------------------------------------
                // Fin de verificaciones
                //-----------------------------------------------------------------

                return $booka;
            } catch (\PDOException $e) {
                throw \Base\Core\Exception($e->getMessage());
            } catch (\Base\Core\Error $e) {
                throw new \Base\Core\Error('404', Text::html('fatal-error-booka'));
            }
        }

        /*
         *  Cargamos los datos mínimos de un proyecto
         */

        public static function getMini($id) {

            try {
                // metemos los datos del proyecto en la instancia
                $query = self::query("SELECT id, name_es as name, owner, collection, status FROM booka WHERE id = ?", array($id));
                $booka = $query->fetchObject(); // stdClass para qno grabar accidentalmente y machacar todo
                // owner
                $booka->user = User::getMini($booka->owner);
                $booka->collection = Collection::get($booka->collection);

                // la primera imagen
                $booka->image = Image::getFirst($booka->id, 'booka');
                
                // nombre sin (sobre)saltos :P
                $booka->clr_name = self::clearName($booka->name);
                
                return $booka;
            } catch (\PDOException $e) {
                throw \Base\Core\Exception($e->getMessage());
            }
        }

        /*
         *  Cargamos los datos suficientes para pintar un widget de proyecto
         */

        public static function getMedium($id) {

            try {
                // metemos los datos del proyecto en la instancia
                $sql = "SELECT
                            id,
                            owner,
                            status,
                            IFNULL(name_" . \LANG . ", name_es) as name,
                            author,
                            IFNULL(about_" . \LANG . ", about_es) as about,
                            IFNULL(info_" . \LANG . ", info_es) as info,
                            published,
                            success,
                            updated,
                            collection
                        FROM booka
                        WHERE id = ?";
                $query = self::query($sql, array($id));
                $booka = $query->fetchObject(__CLASS__);

                // primero, que no lo grabe
                $booka->dontsave = true;

                // nombre sin (sobre)saltos :P
                $booka->clr_name = self::clearName($booka->name);
                
                // coleccion
                $booka->collData = Collection::get($booka->collection);

                // galeria (solo la de contenidos)
                $booka->gallery = Booka\Image::getGallery($booka->id, 'booka');
                $booka->image = Booka\Image::getFirst($booka->id);

                // temas
                $booka->categories = Booka\Category::get($id);

                $booka->num_investors = Invest::numInvestors($id);
                $booka->invested = Invest::invested($id);

                // costes para calcular etapa
                $booka->costs = Booka\Cost::getAll($id);
                
                // sacamos rapidamente el coste total
                $cost_query = self::query("SELECT
                            (SELECT  SUM(amount)
                            FROM    cost
                            WHERE   booka = booka.id
                            ) as `cost`
                    FROM booka
                    WHERE id =?", array($booka->id));
                $booka->cost = $cost_query->fetchColumn();

                $booka->percent = round($booka->invested / $booka->cost * 100);

                $booka->stageData();

                $booka->num_comments = Message::numMessages($id);

                return $booka;
            } catch (\PDOException $e) {
                throw \Base\Core\Exception($e->getMessage());
            }
        }

        /*
         * Listado simple de todos los Bookas
         */

        public static function getAll() {

            $list = array();

            $query = static::query("
                SELECT
                    booka.id as id,
                    booka.name_es as name
                FROM    booka
                ORDER BY name ASC
                ");

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }


        /*
         *  Para validar los campos del proyecto que son NOT NULL en la tabla
         */
        public function validate(&$errors = array()) {

            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'El proyecto no tiene id!!';

            if (empty($this->owner))
                $errors[] = 'El proyecto no tiene usuario creador!!';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

        /**
         * actualiza en la tabla los datos del proyecto
         * 
         * MODIFICACIÓN: Solo se hace update y se tocan los datos el paso que se haya procesado
         * 
            // para las tablas relacionadas
            // cada una con sus save, sus new y sus remove
            // quitar las que tiene y no vienen
            // añadir las que vienen y no tiene
         * 
         * @param array $booka->errors para guardar los errores de datos del formulario, los errores de proceso se guardan en $booka->errors['process']
         */
        public function save(&$errors = array()) {
            if ($this->dontsave) {
                $errors[] = 'No es una instancia completa!';
                return false;
            }

            if (empty($this->process_step)) {
                $errors[] = 'No se ha procesado ningún paso!';
                return false;
            }

            if (!$this->validate($errors)) {
                return false;
            }

            try {
                // fail para pasar por todo antes de devolver false
                $fail = false;

                // según el paso que se ha tocado
                switch ($this->process_step) {
                    case 'overview':
                        // contenidos, imágenes (2 secciones) y categorias
                        // Image
                        if (is_array($this->image) && !empty($this->image['name'])) {
                            $image = new Image($this->image);
                            if ($image->save($errors)) {
                                $this->gallery[] = $image;
                                $this->image = $image->id;
                                $order = Booka\Image::next($this->id);

                                // Guarda la relación NM en la tabla 'booka_image'.
                                if (!empty($image->id)) {
                                    self::query("INSERT booka_image (booka, image, `order`) VALUES (:booka, :image, :order)", array(':booka' => $this->id, ':image' => $image->id, ':order' => $order));
                                }
                            } else {
                                Advice::Error(Text::get('image-upload-fail') . '<br />' . implode(', ', $errors));
                                return false;
                            }
                        }

                        // Image2
                        if (is_array($this->image2) && !empty($this->image2['name'])) {
                            $image2 = new Image($this->image2);
                            if ($image2->save($errors)) {
                                $this->gallery2[] = $image2;
                                $this->image2 = $image2->id;
                                $order = Booka\Image::next2($this->id);

                                // Guarda la relación NM en la tabla 'booka2_image'.
                                if (!empty($image2->id)) {
                                    self::query("INSERT booka2_image (booka2, image, `order`) VALUES (:booka, :image, :order)", array(':booka' => $this->id, ':image' => $image2->id, ':order' => $order));
                                }
                            } else {
                                Advice::Error(Text::get('image-upload-fail') . '<br />' . implode(', ', $errors));
                                return false;
                            }
                        }

                        $fields = array(
                            'name_es',
                            'name_en',
                            'author',
                            'collection',
                            'description_es',
                            'description_en',
                            'motivation_es',
                            'motivation_en',
                            'about_es',
                            'about_en',
                            'goal_es',
                            'goal_en',
                            'related_es',
                            'related_en',
                            'keywords_es',
                            'keywords_en',
                            'info_es',
                            'info_en',
                            'caption_es',
                            'caption_en'
                        );

                        $set = '';
                        $values = array();

                        foreach ($fields as $field) {
                            if ($set != '')
                                $set .= ', ';
                            $set .= "$field = :$field";
                            $values[":$field"] = $this->$field;
                        }

                        // Solamente marcamos updated cuando se envia a revision desde el superform o el admin
                        $values[':id'] = $this->id;

                        $sql = "UPDATE booka SET " . $set . " WHERE id = :id";
                        if (!self::query($sql, $values)) {
                            $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                            $fail = true;
                        }


                        //categorias
                        $tiene = Booka\Category::get($this->id);
                        $viene = $this->categories;
                        $quita = array_diff_assoc($tiene, $viene);
                        $guarda = array_diff_assoc($viene, $tiene);
                        foreach ($quita as $key => $item) {
                            $category = new Booka\Category(
                                            array(
                                                'id' => $item,
                                                'booka' => $this->id)
                            );
                            if (!$category->remove($errors))
                                $fail = true;
                        }
                        foreach ($guarda as $key => $item) {
                            if (!$item->save($errors))
                                $fail = true;
                        }
                        // recuperamos las que le quedan si ha cambiado alguna
                        if (!empty($quita) || !empty($guarda))
                            $this->categories = Booka\Category::get($this->id);
                        
                        break;
                    case 'milestones':
                        // objetivos y video
                        $fields = array(
                            'milestone1_es',
                            'milestone1_en',
                            'milestone2_es',
                            'milestone2_en',
                            'milestone3_es',
                            'milestone3_en',
                            'milestone4_es',
                            'milestone4_en',
                            'media_es',
                            'media_en',
                            'media_usubs',
                            'media_caption_es',
                            'media_caption_en'
                        );

                        $set = '';
                        $values = array();

                        foreach ($fields as $field) {
                            if ($set != '')
                                $set .= ', ';
                            $set .= "$field = :$field";
                            $values[":$field"] = $this->$field;
                        }

                        // Solamente marcamos updated cuando se envia a revision desde el superform o el admin
                        $values[':id'] = $this->id;

                        $sql = "UPDATE booka SET " . $set . " WHERE id = :id";
                        if (!self::query($sql, $values)) {
                            $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                            $fail = true;
                        }
                        
                        
                        break;
                    case 'costs':
                        // solo registros de costes
                        $tiene = Booka\Cost::getAll($this->id);
                        $viene = $this->costs;
                        $quita = array_diff_key($tiene, $viene);
                        $guarda = array_diff_key($viene, $tiene);
                        foreach ($quita as $key => $item) {
                            if (!$item->remove($errors)) {
                                $fail = true;
                            } else {
                                unset($tiene[$key]);
                            }
                        }
                        foreach ($guarda as $key => $item) {
                            if (!$item->save($errors))
                                $fail = true;
                        }
                        /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo */
                        foreach ($tiene as $key => $row) {
                            // a ver la diferencia con el que viene
                            if ($row != $viene[$key]) {
                                if (!$viene[$key]->save($errors))
                                    $fail = true;
                            }
                        }

                        if (!empty($quita) || !empty($guarda))
                            $this->costs = Booka\Cost::getAll($this->id);
                        
                        break;
                    case 'rewards':
                        // solo registros de recompensas
                        $tiene = Booka\Reward::getAll($this->id);
                        $viene = $this->rewards;
                        $quita = array_diff_key($tiene, $viene);
                        $guarda = array_diff_key($viene, $tiene);
                        foreach ($quita as $key => $item) {
                            if (!$item->remove($errors)) {
                                $fail = true;
                            } else {
                                unset($tiene[$key]);
                            }
                        }
                        foreach ($guarda as $key => $item) {
                            if (!$item->save($errors))
                                $fail = true;
                        }
                        /* Ahora, los que tiene y vienen. Si el contenido es diferente, hay que guardarlo */
                        foreach ($tiene as $key => $row) {
                            // a ver la diferencia con el que viene
                            if ($row != $viene[$key]) {
                                if (!$viene[$key]->save($errors))
                                    $fail = true;
                            }
                        }

                        if (!empty($quita) || !empty($guarda))
                            $this->rewards = Booka\Reward::getAll($this->id);
                        
                        break;
                        
                    case 'review':
                        
                        $fields = array(
                            'comment'
                        );

                        $set = '';
                        $values = array();

                        foreach ($fields as $field) {
                            if ($set != '')
                                $set .= ', ';
                            $set .= "$field = :$field";
                            $values[":$field"] = $this->$field;
                        }

                        // Solamente marcamos updated cuando se envia a revision desde el superform o el admin
                        $values[':id'] = $this->id;

                        $sql = "UPDATE booka SET " . $set . " WHERE id = :id";
                        if (!self::query($sql, $values)) {
                            $errors[] = $sql . '<pre>' . print_r($values, 1) . '</pre>';
                            $fail = true;
                        }
                            
                        break;
                }

                //listo
                return !$fail;
            } catch (\PDOException $e) {
                $errors[] = 'Error sql al grabar el proyecto.' . $e->getMessage();
                return false;
            }
        }

        /*
         * comprueba errores de datos y actualiza la puntuación
         */

        public function check() {
            //primero resetea los errores y los okeys
            $this->errors = self::blankErrors();
            $this->okeys = self::blankErrors();

            $errors = &$this->errors;
            $okeys = &$this->okeys;

            // reseteamos la puntuación
            $this->setScore(0, 0, true);

            /*             * *************** Revisión de campos del paso 1, PERFIL *****************
              $score = 0;
              // obligatorios: nombre, email, ciudad
              if (empty($this->user->name)) {
              $errors['userProfile']['name'] = Text::get('validate-user-field-name');
              } else {
              $okeys['userProfile']['name'] = 'ok';
              ++$score;
              }

              // se supone que tiene email porque sino no puede tener usuario, no?
              if (!empty($this->user->email)) {
              ++$score;
              }

              if (empty($this->user->location)) {
              $errors['userProfile']['location'] = Text::get('validate-user-field-location');
              } else {
              $okeys['userProfile']['location'] = 'ok';
              ++$score;
              }

              if(!empty($this->user->avatar) && $this->user->avatar->id != 1) {
              $okeys['userProfile']['avatar'] = 'ok';
              $score+=2;
              }

              if (!empty($this->user->about)) {
              $okeys['userProfile']['about'] = 'ok';
              ++$score;
              // otro +1 si tiene más de 1000 caracteres
              if (\strlen($this->user->about) > 1000) {
              ++$score;
              }
              // además error si tiene más de 2000
              if (\strlen($this->user->about) > 2000) {
              $errors['userProfile']['about'] = Text::get('validate-user-field-about');
              unset($okeys['userProfile']['about']);
              }
              }

              if (!empty($this->user->keywords)) {
              $okeys['userProfile']['keywords'] = 'ok';
              ++$score;
              }

              if (empty($this->user->webs)) {
              $errors['userProfile']['webs'] = Text::get('validate-booka-userProfile-web');
              } else {
              $okeys['userProfile']['webs'] = 'ok';
              ++$score;
              if (count($this->user->webs) > 2) ++$score;

              $anyerror = false;
              foreach ($this->user->webs as $web) {
              if (trim(str_replace('http://','',$web->url)) == '') {
              $anyerror = !$anyerror ?: true;
              $errors['userProfile']['web-'.$web->id.'-url'] = Text::get('validate-user-field-web');
              } else {
              $okeys['userProfile']['web-'.$web->id.'-url'] = 'ok';
              }
              }

              if ($anyerror) {
              unset($okeys['userProfile']['webs']);
              $errors['userProfile']['webs'] = Text::get('validate-booka-userProfile-any_error');
              }
              }

              if (!empty($this->user->facebook)) {
              $okeys['userProfile']['facebook'] = 'ok';
              ++$score;
              // if amigos > 1000 ++$score;
              }

              if (!empty($this->user->twitter)) {
              $okeys['userProfile']['twitter'] = 'ok';
              ++$score;
              // if followers > 1000 ++$score;
              // if listed > 100 ++$score;
              }

              if (!empty($this->user->linkedin)) {
              $okeys['userProfile']['linkedin'] = 'ok';
              // if contacts > 250 $score+=2;
              }

              //puntos
              $this->setScore($score, 14);
             * **************** FIN Revisión del paso 1, PERFIL **************** */

            /*             * *************** Revisión de campos del paso 2,DATOS PERSONALES *****************
              $score = 0;
              // obligatorios: todos
              if (empty($this->real_name)) {
              $errors['userPersonal']['real_name'] = Text::get('mandatory-booka-field-real_name');
              } else {
              $okeys['userPersonal']['real_name'] = 'ok';
              ++$score;
              }

              if (empty($this->nif)) {
              $errors['userPersonal']['nif'] = Text::get('mandatory-booka-field-nif');
              } elseif (!Check::nif($this->nif) && !Check::vat($this->nif)) {
              $errors['userPersonal']['nif'] = Text::get('validate-booka-value-nif');
              } else {
              $okeys['userPersonal']['nif'] = 'ok';
              ++$score;
              }

              if (empty($this->contract_email)) {
              $errors['userPersonal']['contract_email'] = Text::get('mandatory-booka-field-contract_email');
              } elseif (!Check::mail($this->contract_email)) {
              $errors['userPersonal']['contract_email'] = Text::get('validate-booka-value-contract_email');
              } else {
              $okeys['userPersonal']['contract_email'] = 'ok';
              }

              // Segun persona física o jurídica
              if ($this->contract_entity) {  // JURIDICA
              if (empty($this->entity_office)) {
              $errors['userPersonal']['entity_office'] = Text::get('mandatory-booka-field-entity_office');
              } else {
              $okeys['userPersonal']['entity_office'] = 'ok';
              }

              if (empty($this->entity_name)) {
              $errors['userPersonal']['entity_name'] = Text::get('mandatory-booka-field-entity_name');
              } else {
              $okeys['userPersonal']['entity_name'] = 'ok';
              }

              if (empty($this->entity_cif)) {
              $errors['userPersonal']['entity_cif'] = Text::get('mandatory-booka-field-entity_cif');
              } elseif (!Check::nif($this->entity_cif)) {
              $errors['userPersonal']['entity_cif'] = Text::get('validate-booka-value-entity_cif');
              } else {
              $okeys['userPersonal']['entity_cif'] = 'ok';
              }

              } else { // FISICA
              if (empty($this->contract_birthdate)) {
              $errors['userPersonal']['contract_birthdate'] = Text::get('mandatory-booka-field-contract_birthdate');
              } else {
              $okeys['userPersonal']['contract_birthdate'] = 'ok';
              }
              }


              if (empty($this->phone)) {
              $errors['userPersonal']['phone'] = Text::get('mandatory-booka-field-phone');
              } elseif (!Check::phone($this->phone)) {
              $errors['userPersonal']['phone'] = Text::get('validate-booka-value-phone');
              } else {
              $okeys['userPersonal']['phone'] = 'ok';
              ++$score;
              }

              if (empty($this->address)) {
              $errors['userPersonal']['address'] = Text::get('mandatory-booka-field-address');
              } else {
              $okeys['userPersonal']['address'] = 'ok';
              ++$score;
              }

              if (empty($this->zipcode)) {
              $errors['userPersonal']['zipcode'] = Text::get('mandatory-booka-field-zipcode');
              } else {
              $okeys['userPersonal']['zipcode'] = 'ok';
              ++$score;
              }

              if (empty($this->location)) {
              $errors['userPersonal']['location'] = Text::get('mandatory-booka-field-residence');
              } else {
              $okeys['userPersonal']['location'] = 'ok';
              }

              if (empty($this->country)) {
              $errors['userPersonal']['country'] = Text::get('mandatory-booka-field-country');
              } else {
              $okeys['userPersonal']['country'] = 'ok';
              ++$score;
              }

              $this->setScore($score, 6);
             * **************** FIN Revisión del paso 2, DATOS PERSONALES **************** */

            /*             * *************** Revisión de campos del paso 3, DESCRIPCION **************** */
            $score = 0;
            // obligatorios: nombre, imagen, descripcion, about, motivation, categorias, video, localización
            if (empty($this->name_es)) {
                $errors['overview']['name'] = Text::get('mandatory-booka-field-name');
            } else {
                $okeys['overview']['name'] = 'ok';
                ++$score;
            }

            if (empty($this->gallery)) {
                $errors['overview']['image'] = Text::get('mandatory-booka-field-image');
            } else {
                $okeys['overview']['image'] = 'ok';
                ++$score;
                if (count($this->gallery) >= 2)
                    ++$score;
            }

            if (empty($this->description_es)) {
                $errors['overview']['description'] = Text::get('mandatory-booka-field-description');
            } elseif (!Check::words($this->description, 80)) {
                $errors['overview']['description'] = Text::get('validate-booka-field-description');
            } else {
                $okeys['overview']['description'] = 'ok';
                ++$score;
            }

            if (empty($this->about_es)) {
                $errors['overview']['about'] = Text::get('mandatory-booka-field-about');
            } else {
                $okeys['overview']['about'] = 'ok';
                ++$score;
            }

            /*
              if (empty($this->motivation_es)) {
              $errors['overview']['motivation'] = Text::get('mandatory-booka-field-motivation');
              } else {
              $okeys['overview']['motivation'] = 'ok';
              ++$score;
              }

              if (!empty($this->goal_es))  {
              $okeys['overview']['goal'] = 'ok';
              ++$score;
              }

              if (!empty($this->related_es)) {
              $okeys['overview']['related'] = 'ok';
              ++$score;
              }

             */

            if (empty($this->categories)) {
                $errors['overview']['categories'] = Text::get('mandatory-booka-field-category');
            } else {
                $okeys['overview']['categories'] = 'ok';
                ++$score;
            }

            if (!empty($this->keywords)) {
                $okeys['overview']['keywords'] = 'ok';
                ++$score;
            }

            /*
              if (empty($this->media)) {
              $errors['overview']['media'] = Text::get('mandatory-booka-field-media');
              } else {
              $okeys['overview']['media'] = 'ok';
              $score+=3;
              }
             * 
             */

            $this->setScore($score, 16);
            /*             * *************** FIN Revisión del paso 3, DESCRIPCION **************** */

            /*             * *************** Revisión de campos del paso 4, COSTES **************** */
            $score = 0;
            $scoreName = $scoreDesc = $scoreAmount = $scoreDate = 0;
            if (empty($this->costs)) {
                $errors['costs']['costs'] = Text::get('mandatory-booka-costs');
            } else {
                $okeys['costs']['costs'] = 'ok';
                ++$score;
            }

            $anyerror = false;
            foreach ($this->costs as $cost) {
                if (empty($cost->cost)) {
                    $errors['costs']['cost-' . $cost->id . '-cost'] = Text::get('mandatory-cost-field-name');
                    $anyerror = !$anyerror ? : true;
                } else {
                    $okeys['costs']['cost-' . $cost->id . '-cost'] = 'ok';
                    $scoreName = 1;
                }

                if (empty($cost->type)) {
                    $errors['costs']['cost-' . $cost->id . '-type'] = Text::get('mandatory-cost-field-type');
                    $anyerror = !$anyerror ? : true;
                } else {
                    $okeys['costs']['cost-' . $cost->id . '-type'] = 'ok';
                }

                if (empty($cost->description)) {
                    $errors['costs']['cost-' . $cost->id . '-description'] = Text::get('mandatory-cost-field-description');
                    $anyerror = !$anyerror ? : true;
                } else {
                    $okeys['costs']['cost-' . $cost->id . '-description'] = 'ok';
                    $scoreDesc = 1;
                }

                if (empty($cost->amount)) {
                    $errors['costs']['cost-' . $cost->id . '-amount'] = Text::get('mandatory-cost-field-amount');
                    $anyerror = !$anyerror ? : true;
                } else {
                    $okeys['costs']['cost-' . $cost->id . '-amount'] = 'ok';
                    $scoreAmount = 1;
                }

                /*
                  if ($cost->type == 'task' && (empty($cost->from) || empty($cost->until))) {
                  $errors['costs']['cost-'.$cost->id.'-dates'] = Text::get('mandatory-cost-field-task_dates');
                  $anyerror = !$anyerror ?: true;
                  } elseif ($cost->type == 'task') {
                  $okeys['costs']['cost-'.$cost->id.'-dates'] = 'ok';
                  $scoreDate = 1;
                  }
                 * 
                 */
            }

            if ($anyerror) {
                unset($okeys['costs']['costs']);
                $errors['costs']['costs'] = Text::get('validate-booka-costs-any_error');
            }

            $score = $score + $scoreName + $scoreDesc + $scoreAmount + $scoreDate;
            /*
              $costdif = $this->maxcost - $this->mincost;
              $maxdif = $this->mincost * 0.50;
              $scoredif = $this->mincost * 0.35;
              if ($this->mincost == 0) {
              $errors['costs']['total-costs'] = Text::get('mandatory-booka-total-costs');
              } elseif ($costdif > $maxdif ) {
              $errors['costs']['total-costs'] = Text::get('validate-booka-total-costs');
              } else {
              $okeys['costs']['total-costs'] = 'ok';
              }
              if ($costdif <= $scoredif ) {
              ++$score;
              }
             */
            $this->setScore($score, 6);
            /*             * *************** FIN Revisión del paso 4, COSTES **************** */

            /*             * *************** Revisión de campos del paso 5, RETORNOS **************** */
            $score = 0;
            $scoreName = $scoreDesc = $scoreAmount = $scoreLicense = 0;
            if (empty($this->rewards)) {
                $errors['rewards']['rewards'] = Text::get('validate-booka-rewards');
            } else {
                $okeys['rewards']['rewards'] = 'ok';
                if (count($this->rewards) >= 2) {
                    ++$score;
                }
            }

            $anyerror = false;
            foreach ($this->rewards as $reward) {
                if (empty($reward->reward)) {
                    $errors['rewards']['reward-' . $reward->id . 'reward'] = Text::get('mandatory-reward-field-name');
                    $anyerror = !$anyerror ? : true;
                } else {
                    $okeys['rewards']['reward-' . $reward->id . 'reward'] = 'ok';
                    $scoreName = 4;
                }

                if (empty($reward->description)) {
                    $errors['rewards']['reward-' . $reward->id . '-description'] = Text::get('mandatory-reward-field-description');
                    $anyerror = !$anyerror ? : true;
                } else {
                    $okeys['rewards']['reward-' . $reward->id . '-description'] = 'ok';
                    $scoreDesc = 1;
                }
            }

            if ($anyerror) {
                unset($okeys['rewards']['rewards']);
                $errors['rewards']['rewards'] = Text::get('validate-booka-rewards-any_error');
            }


            $score = $score + $scoreName + $scoreDesc + $scoreLicense;
            $scoreName = $scoreDesc = 0;

            $this->setScore($score, 5);
            /*             * *************** FIN Revisión del paso 5, RETORNOS **************** */

            //-------------- Calculo progreso ---------------------//
//            $this->setProgress();
            //-------------- Fin calculo progreso ---------------------//

            return true;
        }

        /*
         * reset de puntuación
         */

        public function setScore($score, $max, $reset = false) {
            if ($reset == true) {
                $this->score = $score;
                $this->max = $max;
            } else {
                $this->score += $score;
                $this->max += $max;
            }
        }

        /*
         * actualizar progreso segun score y max
         *
          public function setProgress () {
          // Cálculo del % de progreso
          $progress = 100 * $this->score / $this->max;
          $progress = round($progress, 0);

          if ($progress > 100) $progress = 100;
          if ($progress < 0)   $progress = 0;

          if ($this->status == 1 &&
          $progress > 80 &&
          \array_empty($this->errors)
          ) {
          $this->finishable = true;
          }
          $this->progress = $progress;
          // actualizar el registro
          self::query("UPDATE booka SET progress = :progress WHERE id = :id",
          array(':progress'=>$this->progress, ':id'=>$this->id));
          }
         * 
         */



        /*
         * Listo para revisión
         */

        public function ready(&$errors = array()) {
            try {
//				$this->rebase();  // no hay rebase aqui

                $sql = "UPDATE booka SET status = :status, updated = :updated WHERE id = :id";
                self::query($sql, array(':status' => 2, ':updated' => date('Y-m-d'), ':id' => $this->id));

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para revisión. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Devuelto al estado de edición
         */

        public function enable(&$errors = array()) {
            try {
                $sql = "UPDATE booka SET status = :status WHERE id = :id";
                self::query($sql, array(':status' => 1, ':id' => $this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al habilitar para edición. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado de publicación
         */

        public function publish(&$errors = array()) {
            try {
                $sql = "UPDATE booka SET status = :status, published = :published WHERE id = :id";
                self::query($sql, array(':status' => 3, ':published' => date('Y-m-d'), ':id' => $this->id));

                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al publicar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado canecelado
         */

        public function cancel(&$errors = array()) {
            try {
                $sql = "UPDATE booka SET status = :status, closed = :closed WHERE id = :id";
                self::query($sql, array(':status' => 0, ':closed' => date('Y-m-d'), ':id' => $this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado caducado
         */

        public function fail(&$errors = array()) {
            try {
                $sql = "UPDATE booka SET status = :status, closed = :closed WHERE id = :id";
                self::query($sql, array(':status' => 6, ':closed' => date('Y-m-d'), ':id' => $this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al cerrar el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado Financiado
         */

        public function succeed(&$errors = array()) {
            try {
                $sql = "UPDATE booka SET status = :status, success = :success WHERE id = :id";
                self::query($sql, array(':status' => 4, ':success' => date('Y-m-d'), ':id' => $this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar por financiado el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Cambio a estado "Libro publicado completamente!"
         */

        public function satisfied(&$errors = array()) {
            try {
                $sql = "UPDATE booka SET status = :status WHERE id = :id";
                self::query($sql, array(':status' => 5, ':id' => $this->id));
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'Fallo al dar el retorno por cunplido para el proyecto. ' . $e->getMessage();
                return false;
            }
        }

        /*
         * Si no se pueden borrar todos los registros, estado cero para que lo borre el cron
         */

        public function delete(&$errors = array()) {

            if ($this->status > 1) {
                $errors[] = "El proyecto no esta descartado ni en edicion";
                return false;
            }

            self::query("START TRANSACTION");
            try {
                //borrar todos los registros
                self::query("DELETE FROM booka_category WHERE booka = ?", array($this->id));
                self::query("DELETE FROM cost WHERE booka = ?", array($this->id));
                self::query("DELETE FROM reward WHERE booka = ?", array($this->id));
                self::query("DELETE FROM image WHERE id IN (SELECT image FROM booka_image WHERE booka = ?)", array($this->id));
                self::query("DELETE FROM image WHERE id IN (SELECT image FROM booka2_image WHERE booka2 = ?)", array($this->id));
                self::query("DELETE FROM booka_image WHERE booka = ?", array($this->id));
                self::query("DELETE FROM booka2_image WHERE booka2 = ?", array($this->id));
                self::query("DELETE FROM booka WHERE id = ?", array($this->id));
                // y los permisos
                self::query("DELETE FROM acl WHERE url like ?", array('%' . $this->id . '%'));
                // si todo va bien, commit y cambio el id de la instancia
                self::query("COMMIT");
                return true;
            } catch (\PDOException $e) {
                self::query("ROLLBACK");
                $sql = "UPDATE booka SET status = :status WHERE id = :id";
                self::query($sql, array(':status' => 0, ':id' => $this->id));
                $errors[] = "Fallo en la transaccion, el proyecto ha quedado como descartado";
                return false;
            }
        }

        /*
         * Para cambiar el id temporal a idealiza
         * solo si es md5
         *
          public function rebase() {
          try {
          if (preg_match('/^[A-Fa-f0-9]{32}$/',$this->id)) {
          // idealizar el nombre
          $newid = self::checkId(self::idealiza($this->name));
          if ($newid == false) return false;

          // actualizar las tablas relacionadas en una transacción
          $fail = false;
          if (self::query("START TRANSACTION")) {
          try {
          self::query("UPDATE booka_category SET booka = :newid WHERE booka = :id", array(':newid'=>$newid, ':id'=>$this->id));
          self::query("UPDATE cost SET booka = :newid WHERE booka = :id", array(':newid'=>$newid, ':id'=>$this->id));
          self::query("UPDATE reward SET booka = :newid WHERE booka = :id", array(':newid'=>$newid, ':id'=>$this->id));
          self::query("UPDATE booka_image SET booka = :newid WHERE booka = :id", array(':newid'=>$newid, ':id'=>$this->id));
          self::query("UPDATE invest SET booka = :newid WHERE booka = :id", array(':newid'=>$newid, ':id'=>$this->id));
          self::query("UPDATE booka SET id = :newid WHERE id = :id", array(':newid'=>$newid, ':id'=>$this->id));
          // borro los permisos, el dashboard los creará de nuevo
          self::query("DELETE FROM acl WHERE url like ?", array('%'.$this->id.'%'));

          // si todo va bien, commit y cambio el id de la instancia
          self::query("COMMIT");
          $this->id = $newid;
          return true;

          } catch (\PDOException $e) {
          self::query("ROLLBACK");
          return false;
          }
          } else {
          throw new Base\Core\Exception('Fallo al iniciar transaccion rebase. ' . \trace($e));
          }
          }

          return true;
          } catch (\PDOException $e) {
          throw new Base\Core\Exception('Fallo rebase id temporal. ' . \trace($e));
          }

          }
         * 
         */

        /*
         *  Para verificar id única
         */

        public static function checkId($id, $num = 1) {
            try {
                $query = self::query("SELECT id FROM booka WHERE id = :id", array(':id' => $id));
                $exist = $query->fetchObject();
                // si  ya existe, cambiar las últimas letras por un número
                if (!empty($exist->id)) {
                    $sufix = (string) $num;
                    if ((strlen($id) + strlen($sufix)) > 49)
                        $id = substr($id, 0, (strlen($id) - strlen($sufix))) . $sufix;
                    else
                        $id = $id . $sufix;
                    $num++;
                    $id = self::checkId($id, $num);
                }
                return $id;
            } catch (\PDOException $e) {
                throw new Base\Core\Exception('Fallo al verificar id única para el proyecto. ' . $e->getMessage());
            }
        }

        /*
         *  Para actualizar los grados de financiacion
         */
        private function stageData() {
/*
            $current = 1;
            $next = 2;
            $rest = $this->cost;
  **/          
            $stages = array(
                0 => Text::get('stage_data-ini'),
                1 => Text::get('stage_data-1'),
                2 => Text::get('stage_data-2'),
                3 => Text::get('stage_data-3'),
                4 => Text::get('stage_data-end')
            );
            
            // cantidad de coste de cada etapa
            $stageCost = array(1=>0, 2=>0, 3=>0, 4=>0);
            
            foreach ($this->costs as $id=>$item) {
                
                switch ($item->stage) {
                    case 1:
                        $stageCost[1] += $item->amount;
                        $stageCost[2] += $item->amount;
                        $stageCost[3] += $item->amount;
                        break;
                    case 2:
                        $stageCost[2] += $item->amount;
                        $stageCost[3] += $item->amount;
                        break;
                    case 3:
                        $stageCost[3] += $item->amount;
                        break;
                }
            }
            
            foreach ($stageCost as $key => $value) {
//                if ($this->invested >= $value) continue;
                
                if ($this->invested < $value || $key == 4) {
                    $current = $key-1;
                    $next = $key;
                    $rest = $value - $this->invested;
                    break;
                }
            }
            
            if ($rest < 0) $rest = 0;
            
            $this->stageData = (object) array(
                'costs' => $stageCost,
                'current' => $current,
                'currentName' => $stages[$current],
                'next' => $next,
                'nextName' => $stages[$next],
                'rest' => $rest
            );

        }

        /**
         * Metodo que devuelve los días que lleva de publicación
         *
         * @return numeric days active from published
         */
        public function daysActive() {
            // días desde el published
            $sql = "
                SELECT DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(published)), '%j') as days
                FROM booka
                WHERE id = ?";
            $query = self::query($sql, array($this->id));
            $past = $query->fetchObject();

            return $past->days - 1;
        }

        /*
         * Lista de Bookas de un usuario
         */

        public static function ofmine($owner, $published = false) {
            $bookas = array();

            $sql = "SELECT * FROM booka WHERE owner = ?";
            if ($published) {
                $sql .= " AND status > 2";
            }
            $sql .= " ORDER BY created DESC";
            $query = self::query($sql, array($owner));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $bookas[] = self::get($proj->id);
            }

            return $bookas;
        }

        /*
         * Lista de Bookas publicados
         */

        public static function published($type = 'all', $limit = null) {
            Advice::Info('Metodo Booka::published no esta completamente implementado');
            return array();

            // segun el tipo (ver controller/search.php)
            switch ($type) {
                case 'popular':
                    // de los que estan en campaña,
                    // los que tienen más usuarios (unicos) cofinanciadores y mensajeros
                    $sql = "SELECT COUNT(DISTINCT(user.id)) as people, booka.id as id
                            FROM booka
                            LEFT JOIN invest
                                ON invest.booka = booka.id
                                AND invest.status <> 2
                            LEFT JOIN message
                                ON message.booka = booka.id
                            LEFT JOIN user 
                                ON user.id = invest.user OR user.id = message.user
                            WHERE booka.status= 3 
                            AND (booka.id = invest.booka
                                OR booka.id = message.booka)
                            GROUP BY booka.id
                            ORDER BY people DESC";
                    break;
                case 'outdate':
                    // los que les quedan 15 dias o menos
                    $sql = "SELECT  id,
                                (SELECT  SUM(amount)
                                FROM    cost
                                WHERE   booka = booka.id
                                AND     required = 1
                                ) as `mincost`,
                                (SELECT  SUM(amount)
                                FROM    invest
                                WHERE   booka = booka.id
                                AND     (invest.status = 0
                                        OR invest.status = 1
                                        OR invest.status = 3
                                        OR invest.status = 4)
                                ) as `getamount`
                            FROM    booka
                            WHERE   days <= 15
                            AND     days > 0
                            AND     status = 3
                            HAVING getamount < mincost
                            ORDER BY days ASC";
                    break;
                case 'recent':
                    // los que llevan menos tiempo desde el published, hasta 15 dias
                    // Cambio de criterio: Los últimos 9
                    //,  DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(published)), '%e') as day
                    //        HAVING day <= 15 AND day IS NOT NULL
                    $sql = "SELECT 
                                booka.id as id
                            FROM booka
                            WHERE booka.status = 3
                            ORDER BY published DESC
                            LIMIT 9";
                    break;
                case 'success':
                    // los que han conseguido el mínimo
                    $sql = "SELECT
                                id,
                                (SELECT  SUM(amount)
                                FROM    cost
                                WHERE   booka = booka.id
                                AND     required = 1
                                ) as `mincost`,
                                (SELECT  SUM(amount)
                                FROM    invest
                                WHERE   booka = booka.id
                                AND     invest.status IN ('0', '1', '3', '4')
                                ) as `getamount`
                        FROM booka
                        HAVING getamount >= mincost
                        ORDER BY name ASC";
                    break;
                case 'available':
                    // ni edicion ni revision ni cancelados, estan disponibles para verse publicamente
                    $sql = "SELECT id FROM booka WHERE status > 2 AND status < 6 ORDER BY name ASC";
                    break;
                case 'archive':
                    // caducados, financiados o casos de exito
                    $sql = "SELECT id FROM booka WHERE status > 3 ORDER BY name ASC";
                    break;
                default:
                    // todos los que estan 'en campaña'
                    $sql = "SELECT id FROM booka WHERE status = 3 ORDER BY name ASC";
            }

            // Limite
            if (!empty($limit) && \is_numeric($limit)) {
                $sql .= " LIMIT $limit";
            }

            $bookas = array();
            $query = self::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $bookas[] = self::getMedium($proj['id']);
            }
            return $bookas;
        }

        /*
         * Lista de Bookas en campaña (para ser revisados por el cron / admin)
         */

        public static function active() {
            $bookas = array();
            $query = self::query("SELECT booka.id
                                  FROM  booka
                                  WHERE booka.status = 3
                                  GROUP BY booka.id
                                  ORDER BY name ASC");
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $proj) {
                $bookas[] = self::get($proj->id);
            }
            return $bookas;
        }

        /**
         * Saca una lista completa de Bookas
         *
         * @param string filters array
         * @return array of booka instances
         */
        public static function getList($filters = array()) {
            $bookas = array();

            $values = array();

            // los filtros
            $sqlFilter = "";
            if ($filters['status'] > -1) {
                $sqlFilter .= " AND status = :status";
                $values[':status'] = $filters['status'];
            } else {
                $sqlFilter .= " AND status > 0";
            }
            if (!empty($filters['owner'])) {
                $sqlFilter .= " AND owner = :owner";
                $values[':owner'] = $filters['owner'];
            }
            if (!empty($filters['collection'])) {
                $sqlFilter .= " AND collection = :collection";
                $values[':collection'] = $filters['collection'];
            }
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND name_es LIKE :name";
                $values[':name'] = "%{$filters['name']}%";
            }
            if (!empty($filters['category'])) {
                $sqlFilter .= " AND id IN (
                    SELECT booka
                    FROM booka_category
                    WHERE category = :category
                    )";
                $values[':category'] = $filters['category'];
            }

            //el Order
            if (!empty($filters['order'])) {
                switch ($filters['order']) {
                    case 'updated':
                        $sqlOrder .= " ORDER BY updated DESC";
                        break;
                    case 'name':
                        $sqlOrder .= " ORDER BY name_es ASC";
                        break;
                    default:
                        $sqlOrder .= " ORDER BY {$filters['order']}";
                        break;
                }
            }

            // la select
            $sql = "SELECT 
                        id
                    FROM booka
                    WHERE id IS NOT NULL
                        $sqlFilter
                        $sqlOrder
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $bookas[] = self::getMedium($proj['id']);
            }
            return $bookas;
        }

        /*
         * Estados de publicación de un proyecto
         */

        public static function status() {
            return array(
                0 => Text::get('form-booka_status-cancelled'),
                1 => Text::get('form-booka_status-edit'),
                2 => Text::get('form-booka_status-review'),
                3 => Text::get('form-booka_status-campaing'), // en campaña (se puede aportar)
                4 => Text::get('form-booka_status-success'), // financiado (publico pero no se puede aportar)
                5 => Text::get('form-booka_status-fulfilled'), // producido (no público, solo boockateca)
                6 => Text::get('form-booka_status-shared')); // disponible (no publico, bookateca y alacarte)
        }

        public static function blankErrors() {
            // para guardar los fallos en los datos
            $errors = array(
                'overview' => array(), // Errores en el paso 1 descripcion
                'milestones' => array(), // Errores en el paso 2 objetivos
                'costs' => array(), // Errores en el paso 3 presupuesto
                'rewards' => array(), // Errores en el paso 4 recompensas
                'translate' => array()  // Errores en el paso 5 traduccion 
            );

            return $errors;
        }

        public static function clearname($name) {
            //@TODO: expresion regular para esto
            $name = str_replace(
                        array(
                            '<br>',
                            '<br >',
                            '<br/>',
                            '<br />',
                            '</br>',
                            '</ br>',
                            '< br >',
                            '</ br />',
                            '</br/>'
                        ), ' ', $name);
            return $name;
        }

    }

}