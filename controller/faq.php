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

namespace Base\Controller {

    use Base\Core\View,
        Base\Model;

    class Faq extends \Base\Core\Controller {

        public function index ($current = 'main') {

            $faqs = array();

            $sections = Model\Faq::sections();

            foreach ($sections as $id=>$name) {
                $qs = Model\Faq::getAll($id);
                
                if (empty($qs)) {
                    if ($id == $current && $current != 'main') {
                        throw new \Base\Core\Redirection('/faq');
                    }
                    unset($sections[$id]);
                    continue;
                }

                $faqs[$id] = $qs;
                foreach ($faqs[$id] as &$question) {
                    $question->description = nl2br(str_replace(array('%SITE_URL%'), array(SITE_URL), $question->description));
                    if (isset($show) && $show == $question->id) {
                        $current = $id;
                    }
                }
            }

            return new View(
                'view/faq/index.html.php',
                array(
                    'faqs'     => $faqs,
                    'current'  => $current,
                    'sections' => $sections,
                    'show'     => $show
                )
             );

        }

    }

}