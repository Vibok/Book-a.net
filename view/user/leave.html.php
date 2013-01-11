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
    Base\Library\Text;

$bodyClass = 'login';

include 'view/prologue.html.php';
include 'view/header.html.php';

extract($_POST);
if (!isset($_POST['email']) && isset($_GET['email'])) {
    $email = $_GET['email'];
}
?>
    <div id="main">

        <div id="sub-header" class="sh-info">
            <h2><?php echo $this['text']; ?></h2>
            <?php echo $this['content']; ?>
        </div>
        
        <div class="three-cols">
            <form action="/user/leave" method="post">
                <div class="field-block">
                    <label for="RegisterEmail"><?php echo Text::get('login-register-email-field'); ?></label>
                    <input type="text" id="RegisterEmail" name="email" value="<?php echo htmlspecialchars($email) ?>"/>
                </div>

                <div class="field-block">
                    <label for="leave-message"><?php echo Text::get('login-leave-message'); ?></label>
                    <textarea id="leave-message" name="reason" cols="37" rows="5"><?php echo $reason ?></textarea>
                </div>

                <br />
                <button type="submit" name="leaving" class="std-btn centered" value="leaving"><?php echo Text::get('login-leave-button'); ?></button>

            </form>
            
        </div>
            
    </div>

<?php 
include 'view/footer.html.php'; 
include 'view/epilogue.html.php'; 
