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

use Base\Library\Page,
    Base\Library\Text;

$bodyClass = 'about';

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

    <div id="main">

        <div id="sub-header" class="sh-info">
            <h2><?php echo $this['text']; ?></h2>
            <?php echo $this['content']; ?>
        </div>

        <div class="widget contact-message">

            <h3 class="title"><?php echo Text::get('contact-send_message-header'); ?></h3>

            <form method="post" action="/contact">
                <div class="field-block">   
                    <label for="email"><?php echo Text::get('contact-email-field'); ?></label>
                    <input type="text" id="email" name="email" value="<?php echo $this['data']['email'] ?>"/>
                </div>

                <div class="field-block">
                    <label for="subject"><?php echo Text::get('contact-subject-field'); ?></label>
                    <input type="text" id="subject" name="subject" value="<?php echo $this['data']['subject'] ?>"/>
                </div>

                <div class="field-block">
                    <label for="message"><?php echo Text::get('contact-message-field'); ?></label>
                    <textarea id="message" name="message" cols="50" rows="5"><?php echo $this['data']['message'] ?></textarea>
                </div>

                <button class="std-btn wide" type="submit" name="send"><?php echo Text::get('contact-send_message-button'); ?></button>
            </form>

        </div>

    </div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>