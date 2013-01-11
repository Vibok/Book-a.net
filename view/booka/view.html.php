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

use Base\Core\View,
    Base\Model\Image,
    Base\Library\Text;

$invest  = ($this['booka']->status == 3) ? $this['invest'] : null;
$booka = $this['booka'];

$bodyClass = 'booka'; 

// metas og: para que al compartir en facebook coja bien el nombre y la imagen (todas las de proyecto y las novedades
$ogmeta = array(
    'title' => $booka->clr_name,
    'description' => Text::get('regular-by').' '.$booka->author,
    'url' => SITE_URL . '/booka/'.$booka->id
);

// todas las imagenes del booka
if (!empty($booka->gallery)) {
    foreach ($booka->gallery as $pgimg) {
        if ($pgimg instanceof Image) {
            $ogmeta['image'][] = $pgimg->getLink(580, 580);
        }
    }
}

// tambien las de contenido
if (!empty($booka->gallery2)) {
    foreach ($booka->gallery2 as $pgimg) {
        if ($pgimg instanceof Image) {
            $ogmeta['image'][] = $pgimg->getLink(580, 580);
        }
    }
}

include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>
<script type="text/javascript">
    $(function(){
        $('#sub-header').slides({
            container: 'sh-booka-images',
            paginationClass: 'slide-ctrl',
            generatePagination: true,
            effect: 'fade',
            fadeSpeed: 200,
            play: 7000
        });
    });
</script>

    <div id="main" class="sided">
        <?php if ($this['show'] != 'invest') echo new View('view/booka/widget/name.html.php', $this); ?>
        <?php echo new View('view/booka/widget/navi.html.php', $this); ?>
        
        <div class="center">
            <?php // los modulos centrales son diferentes segun el show

            switch ($this['show']) {
                case 'needs':
                    echo
                        new View('view/booka/widget/needs.html.php', $this);
                    break;

                case 'milestones':
                    echo
                        '<div class="center-widget">',
                        new View('view/booka/widget/milestones.html.php', $this),
                        new View('view/booka/widget/media.html.php', $this),
                        '</div>';
                    break;

                case 'investors':
                    echo new View('view/booka/widget/investors.html.php', $this);
                    break;
                
                case 'invest':
                    // segun el paso de aporte
                    if (!empty($invest) && in_array($invest, array('start', 'ok', 'fail'))) {

                        switch ($invest) {
                            case 'start':
                                echo
                                    new View('view/booka/widget/investMsg.html.php', array('message' => Text::get('booka-invest-start'))),
                                    new View('view/booka/widget/invest.html.php', $this);
                                break;

                            case 'ok':
                                echo
                                    new View('view/booka/widget/spread.html.php', $this);
                                break;

                            case 'fail':
                                echo
                                    new View('view/booka/widget/investMsg.html.php', array('message' => Text::get('booka-invest-fail'))),
                                    new View('view/booka/widget/invest.html.php', $this);
                                break;
                        }
                    } else {
                        new View('view/booka/widget/investors.html.php', $this);
                    }
                    break;

                case 'messages':
                    echo
                        new View('view/booka/widget/sendMsg.html.php', $this),
                        new View('view/booka/widget/messages.html.php', $this);
                    break;

                case 'home':
                default:
                    echo
                        '<div class="center-widget">',
                        new View('view/booka/widget/share.html.php', $this),
                        new View('view/booka/widget/summary.html.php', $this),
                        '</div>';
                    break;
            }
            ?>
         </div>

        <div class="side">
        <?php // el lateral por ahora siempre es financiaciÃ³n y recompensas
            echo new View('view/booka/widget/support.html.php', $this);
            echo new View('view/booka/widget/rewards.html.php', $this);
        ?>
        </div>

    </div>

<?php 
include 'view/footer.html.php'; 
include 'view/epilogue.html.php';
?>
