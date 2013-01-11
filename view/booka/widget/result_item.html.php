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
    Base\Library\Text,
    Base\Model\Invest,
    Base\Model\Message,
    Base\Model\Image;

$booka = $this['booka'];
// diferente segun impulsado por usuario o resultado busqueda
// diferente si esta en campaña o si está financiado/producido
?>
<div class="booka-item activable">
    <a href="<?php echo SITE_URL ?>/booka/<?php echo $booka->id ?>" class="expand"></a>

    <div class="image">
        <?php if (!empty($booka->gallery) && (current($booka->gallery) instanceof Image)): ?>
        <img alt="<?php echo $booka->name ?>" src="<?php echo current($booka->gallery)->getLink(100, 140, true) ?>" />
        <?php endif ?>
    </div>
    
    <div class="content">
        <a class="name bloque ct1 fs-M" href="<?php echo SITE_URL ?>/booka/<?php echo $booka->id ?>"><?php echo $booka->name ?></a>
        <?php /* if (!empty($booka->subtitle)) : ?>
        <a class="name bloque ct1 fs-M" href="<?php echo SITE_URL ?>/booka/<?php echo $booka->id ?>"><?php echo $booka->subtitle ?></a>
        <?php endif; */ ?>
        <span class="author bloque ct2 fs-M"><?php echo $booka->author; ?></span>

        <?php if ($booka->status == 3) : 
            echo new View('view/booka/widget/meter.html.php', array('booka' => $booka, 'type'=>'small') ); ?>
        <span class="bloque fs-XS ft3 ct2 wshadow"><?php echo Text::get('booka-campaign_since', $booka->published); ?></span>
        <?php else : ?>
        <span class="bloque collection ct2 upcase fs-M" style="color:<?php echo '#'.$booka->collDaata->color; ?>;"><?php echo 'Colección ' . $booka->collData->name; ?></span>
        <span class="bloque fs-XS ft3 ct2 wshadow"><?php echo Text::get('booka-produced_date', $booka->success); ?></span>
        <?php endif; ?>
        <span class="bloque ct1 upcase  ft3 fs-XS"><?php echo $booka->num_investors; ?> <?php echo Text::get('booka-menu-investors'); ?></span>
    </div>
    
<?php    
switch ($this['show']) {
    case 'invested': 
        // sacar cuanto dinero ha puesto el usuario en este proyecto
        $invested = Invest::supported($this['user']->id, $booka->id);
        ?>
        <div class="status">
            <span class="fs-S ft2 ct1 bloque"><?php echo $this['user']->name ?></span>
            <span class="fs-XS ft3">Impulsa con:</span>
            <div class="amount ft3 fs-L ct3">
                <?php echo \amount_format($invested->total); ?><span class="euro fs-M ct3">&euro;</span>
            </div>
            
        </div>

        <div class="hr clear"><hr /></div>
        
        <div class="social">
            <span class="coments ft3 fs-XS ct2"><?php echo $booka->num_comments; ?> <?php echo Text::get('booka-menu-messages'); ?></span>
            <ul class="share line">
                <li class="embed"><a class="ct1 fs-XS ft3" href="#" onclick="alert('<?php echo SITE_URL.'/blog/'.$post->id ?>'); return false;">&LT; <?php echo Text::get('regular-embed') ?> &GT;</a><span class="vr ct1 fs-XS3">|</span></li>
                <li class="link"><a class="ct1 fs-XS ft3" href="#" onclick="alert('<?php echo SITE_URL.'/blog/'.$post->id ?>'); return false;"><?php echo Text::get('regular-link') ?></a></li>
                <li class="socials">
                    <span class="vr ct1 fs-XS">|</span><span class="ct1 fs-XS ft3" style="margin-right: 13px;"><?php echo Text::get('regular-share') ?>:</span>
                    <a class="social-button facebook" href="#facebook">&nbsp;</a>
                    <a class="social-button twitter" href="#twitter">&nbsp;</a>
                    <a class="social-button google" href="#google">&nbsp;</a>
                </li>
            </ul>
        </div>
    <?php
    break;

    case 'result': ?>
        <div class="status">
            <?php if ($booka->status == 3) : ?>
                <a href="<?php echo SITE_URL ?>/booka/<?php echo $booka->id ?>/invest" class="button std-btn tight"><?php echo Text::get('booka-invest_now') ?></a>
            <?php elseif ($booka->status == 4 || $booka->status == 5) : ?>
                <a href="<?php echo SITE_URL ?>/buy/<?php echo $booka->id ?>/booka" class="button std-btn tight"><?php echo Text::get('booka-buy_now') ?></a>
            <?php endif; ?>
        </div>
    <?php
    break;

}
?>
</div>

<?php if ($this['show'] == 'result' && !$this['noborder']) : ?><div class="clear bottom dashed" style="width: 615px;"></div><?php endif; ?>
