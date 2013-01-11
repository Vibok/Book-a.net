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

    use Base\Library\Text;
    
	/*
	 * Clase para pintar una barra de navegación (completa o parcial) segun el array recibido
	 * también una función para pintar elementos de navegación de páginas
	 */
    class Navi {
		
        // estructura completa de ejemplo
        public static 
                $NaviBar = array(
                    'top' => array(
                        'link1' => array(
                            'label' => 'Enlace 1',
                            'url' => '/',
                            'disabled' => false
                        ),
                        'link2' => array(
                            'label' => 'Enlace 2',
                            'url' => '/',
                            'disabled' => false
                        ),
                        'link3' => array(
                            'label' => 'Enlace 3',
                            'url' => '/',
                            'disabled' => false
                        )
                    ),
                    'bottom' => array(
                        'bookallow' => array(
                            'label' => 'book-allow',
                            'url' => '/',
                            'disabled' => false
                        ),
                        'bookateca' => array(
                            'label' => 'book-ateca',
                            'url' => '/bookateca',
                            'disabled' => false
                        ),
                        'bookacross' => array(
                            'label' => 'book-across',
                            'url' => '/bookacross',
                            'disabled' => true
                        )
                    ),
                    'current' => array(
                        'top' => '',
                        'bottom' => ''
                    ),
                    'social' => true,
                    'collections'  => true
                );

        static public function html ($conf) {
            
            $code = '';
//            $code .= \trace($conf);
            
            $code .= '<div id="navi-bar">';
            
            // si tenemos top
            if (!empty($conf['top'])) {
                $code .= '<ul class="top line">';
                foreach ($conf['top'] as $key=>$element) {
                    
                    $classes = array();
                    if ($conf['current']['top'] == $key) $classes[] = 'current';
                    if ($element['disabled']) {
                        $classes[] = 'disabled';
                        $aclass = ' disabled a-null';
                    } else {
                        $aclass = '';
                    }
                    $class = (!empty($classes)) ? ' class="'.implode(' ', $classes).'"' : '';
                    $code .= '<li'.$class.'><a href="'.$element['url'].'" class="'.$aclass.'">'.$element['label'].'</a></li>';
                }
                
                if ($conf['social']) {
                    $code .= '<li class="right"><span class="ct1 ft3">'.Text::get('booka-social-header').'</span>';
                        $code .= '<a href="'.Text::get('social-account-facebook').'" class="social-icon facebook" target="_blank" title="'.Text::get('regular-facebook').'">f</a>';
                        $code .= '<a href="'.Text::get('social-account-flickr').'" class="social-icon flickr" target="_blank" title="'.Text::get('regular-flickr').'">&#8734;</a>';
                        $code .= '<a href="'.Text::get('social-account-vimeo').'" class="social-icon vimeo" target="_blank" title="'.Text::get('regular-vimeo').'">v</a>';
                        $code .= '<a href="'.Text::get('social-account-twitter').'" class="social-icon twitter" target="_blank" title="'.Text::get('regular-twitter').'">t</a>';
                    $code .= '</li>';
                }
                
                $code .= '</ul>';
            }
            
            // si tenemos bottom
            if (!empty($conf['bottom'])) {
                $code .= '<ul class="down line roundc cf4">';
                foreach ($conf['bottom'] as $key=>$element) {
                    
                    $classes = array();
                    if ($conf['current']['bottom'] == $key) $classes[] = 'current';
                    if ($element['disabled']) {
                        $classes[] = 'disabled';
                        $aclass = ' disabled a-null';
                    } else {
                        $aclass = '';
                    }
                    $class = (!empty($classes)) ? ' class="'.implode(' ', $classes).'"' : '';
                    $code .= '<li'.$class.'><a href="'.$element['url'].'" class="wshadow'.$aclass.'">'.$element['label'].'</a></li>';
                }
                
                /*
                if ($conf['collections']) {
                    $code .= '<li class="right"><a href="/collection" class="upcase wshadow">'.Text::get('booka-collections-header').'</a></li>';
                }
                 */
                
                $code .= '</ul>';
            }
            
            $code .= '</div>';
            
            return $code;
		}

        /*
         * Código para html de paginación (dentro de un results-bar que va en cada página o aquí...
         * 
         * en conf vienen los números apra "de TAL a CUAL de TANTOS
         * o si no tiene que pintarlo porque ese footer no lleva el texto
         * Si tiene que pintar el "anterior" y a la página que tiene que saltar
         * Si tiene que pintar el "siguiente" y a la página que tiene que saltar
         * 
         */
        public static function pageHtml($conf = array()) {
            
            $code = '';
            
            if ($conf['footer']) $code .= '<div class="hr clear" style="margin-bottom: 6px;"><hr /></div>';
            
            $fclass = $conf['footer'] ? ' footer' : '';
            $code .= '<div class="results-bar' . $fclass . '">';
            
            if (!empty($conf['span']))
                $code .= '<span class="wshadow fs-XS ft3 ct2 wshadow">'.$conf['span'].'</span>';
            
            // color differente para blog
            $color = $conf['white'] ? 'ct3' : 'ct2';
            
            $code .= '<ul class="page-control line">';
            if (!empty($conf['back']))
                $code .= '<li><a class="prev-page fs-XS ft3 '.$color.'" href="?page='.$conf['back'].'">< '.Text::get('regular-prev').'</a></li>';
            elseif ($conf['white'])
                $code .= '<li></li>';
            
            if (!empty($conf['go']))
                $code .= '<li><a class="next-page fs-XS ft3 '.$color.'" href="?page='.$conf['go'].'">'.Text::get('regular-next').' ></a></li>';
            
            $code .= '</ul>';
            
            $code .= '</div>';
            
            if (!$conf['footer'] && !$conf['white']) $code .= '<div class="hr clear" style="padding-top: 6px;"><hr /></div>';
            
            return $code;
            
        }

//            $Total = $Tabla->cuentaFilas($Filtros);	

        public static function calcPages($Total, $Pagina = 1, $Mostrar = 10) {
            $Paginas = $Mostrar > 0 ? ceil($Total / $Mostrar) : 1;

            if ($Pagina > $Paginas) {
                $Pagina = $Paginas;
            } elseif ($Pagina <= 1) {
                $Pagina = 1;
            }
            
            $Next = ($Pagina+1) > $Paginas ? null : ($Pagina+1);
            $Prev = $Pagina == 1 ? null : ($Pagina-1);

            $Offset = ($Pagina - 1) * $Mostrar;
            $Limit = $Mostrar > 0 ? $Mostrar : null;

            if ($Offset >= $Total) {
                $Offset = $Total - $Mostrar;
            } elseif ($Offset <= 0) {
                $Offset = null;
            }
            
            $To = ($Offset+$Limit > $Total) ? $Total : $Offset+$Limit;
            
            // se usa:
            //  el from - to - of  para el caption de Mostrando ...
            //  el offset limit para sacar resultados de la BD
            //  el next y prev para  botones de anterior siguiente
            return array(
                'from' => $Offset+1,
                'to' => $To,
                'of' => $Total,
                'offset' => $Offset,
                'limit' => $Limit,
                'next' => $Next,
                'prev' => $Prev
            );
        }
        
        
	}
	
}
