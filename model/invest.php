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

    use Base\Library\Text,
        Base\Model\Image,
        Base\Model\User,
        Base\Model\Booka;

    class Invest extends \Base\Core\Model {

        public
            $id,
            $user,
            $booka,
            $account, // PayerID de expresscheckout o email si aporte manual
            $amount, //cantidad monetaria del aporte
            $preapproval, //clave del preapproval
            $payment, //clave del cargo
            $transaction, // id de la transacción
            $method, // metodo de pago paypal/tpv
            $status, //estado en el que se encuentra esta aportación:
                    // -1 en proceso, 0 pendiente, 1 cobrado (charged), 2 devuelto (returned)
            $issue, // aporte con incidencia
            $anonymous, //no debe aparecer su careto ni su nombre, nivel, etc... pero si aparece en la cuenta de cofinanciadores y de aportes
            $resign, //renuncia a cualquier recompensa
            $invested, //fecha en la que se ha iniciado el aporte
            $charged, //fecha en la que se ha cargado el importe del aporte a la cuenta del usuario
            $returned, //fecha en la que se ha devuelto el importe al usurio por cancelación bancaria
            $rewards = array(), //datos de las recompensas que le corresponden
            $address = array(
                'name'     => '',
                'nif'      => '',
                'address'  => '',
                'zipcode'  => '',
                'location' => '',
                'country'  => '');  // dirección de envio del retorno

        // añadir los datos del cargo


        /*
         *  Devuelve datos de una inversión
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT  *
                    FROM    invest
                    WHERE   id = :id
                    ", array(':id' => $id));
                $invest = $query->fetchObject(__CLASS__);

				$query = static::query("
                    SELECT  *
                    FROM  invest_reward
                    INNER JOIN reward
                        ON invest_reward.reward = reward.id
                    INNER JOIN reward_type
                        ON reward_type.id = reward.type
                    WHERE   invest_reward.invest = ?
                    ", array($id));
				$invest->rewards = $query->fetchAll(\PDO::FETCH_OBJ);

				$query = static::query("
                    SELECT  address, city, location, zipcode, country, name, nif
                    FROM  invest_address
                    WHERE   invest_address.invest = ?
                    ", array($id));
				$invest->address = $query->fetchObject();

                // si no tiene dirección, sacamos la dirección del usuario
                if (empty($invest->address)) {
                    $invest->address = User::getPersonal($invest->user);
                }
                
                return $invest;
        }

        /*
         * Lista de inversiones (individuales) de un proyecto
         *
         * el parametro filter es para la gestion de recompensas (no es un autentico filtro, hay ordenaciones y hay filtros)
         */
        public static function getAll ($booka, $filter = null) {

            /*
             * Estos son los filtros
             */
            $filters = array(
                'date'      => 'Fecha',
                'user'      => 'Usuario',
                'reward'    => 'Recompensa',
                'pending'   => 'Pendientes',
                'fulfilled' => 'Cumplidos'
            );


            $invests = array();

            $query = static::query("
                SELECT  *
                FROM  invest
                WHERE   invest.booka = ?
                AND invest.status IN ('0', '1', '3', '4')
                ", array($booka));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $invest) {
                // datos del usuario
                $invest->user = User::get($invest->user);

				$query = static::query("
                    SELECT  *
                    FROM  invest_reward
                    INNER JOIN reward
                        ON invest_reward.reward = reward.id
                    WHERE   invest_reward.invest = ?
                    ", array($invest->id));
				$invest->rewards = $query->fetchAll(\PDO::FETCH_CLASS);

				$query = static::query("
                    SELECT  address, zipcode, location, country
                    FROM  invest_address
                    WHERE   invest_address.invest = ?
                    ", array($invest->id));
				$invest->address = $query->fetchObject();

                // si no tiene dirección, sacamos la dirección del usuario
                if (empty($invest->address)) {
                    $usr_address = User::getPersonal($invest->user->id);

                    $invest->address = $usr_address;
                }

                $invests[$invest->id] = $invest;
            }

            return $invests;
        }


        /*
         * Lista de aportes individuales
         *
         * Los filtros vienen de la gestión de aportes
         * Los datos que sacamos: usuario, proyecto, cantidad, estado de proyecto, estado de aporte, fecha de aporte, tipo de aporte, campaña
         * .... anonimo, resign, etc...
         */
        public static function getList ($filters = array()) {

            $values = array();
            /*
             * Estos son los filtros
            $fields = array('method', 'status', 'investStatus', 'booka', 'user', 'campaign', 'types');
             */

            $list = array();

            $sqlFilter = "";
            if (!empty($filters['methods'])) {
                $sqlFilter .= " AND invest.method = '{$filters['methods']}'";
            }
            if (is_numeric($filters['status'])) {
                $sqlFilter .= " AND booka.status = '{$filters['status']}'";
            }
            if (is_numeric($filters['investStatus'])) {
                $sqlFilter .= " AND invest.status = '{$filters['investStatus']}'";
            }
            if (!empty($filters['bookas'])) {
                $sqlFilter .= " AND invest.booka = '{$filters['bookas']}'";
            }
            if (!empty($filters['users'])) {
                $sqlFilter .= " AND invest.user = '{$filters['users']}'";
            }
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND invest.user IN (SELECT id FROM user WHERE (name LIKE :name OR email LIKE :name))";
                $values[':name'] = "%{$filters['name']}%";
            }
            if (!empty($filters['collections'])) {
                $sqlFilter .= " AND booka.collection = :collection";
                $values[':collection'] = $filters['collections'];
            }
            if (!empty($filters['types'])) {
                switch ($filters['types']) {
                    case 'donative':
                        $sqlFilter .= " AND invest.resign = 1";
                        break;
                    case 'anonymous':
                        $sqlFilter .= " AND invest.anonymous = 1";
                        break;
                    case 'manual':
                        $sqlFilter .= " AND invest.admin IS NOT NULL";
                        break;
                }
            }

            if (!empty($filters['review'])) {
                switch ($filters['review']) {
                    case 'collect': //  Recaudado: tpv cargado o paypal pendiente
                        $sqlFilter .= " AND ((invest.method = 'tpv' AND invest.status = 1)
                                        OR (invest.method = 'paypal' AND invest.status = 0))";
                        break;
                    case 'online': // Solo pagos online
                        $sqlFilter .= " AND (invest.method = 'tpv' OR invest.method = 'paypal')";
                        break;
                    case 'paypal': // Paypal pendientes o ok
                        $sqlFilter .= " AND (invest.method = 'paypal' AND (invest.status = -1 OR invest.status = 0))";
                        break;
                    case 'tpv': // Tpv pendientes o ok
                        $sqlFilter .= " AND (invest.method = 'tpv' AND (invest.status = -1 OR invest.status = 1))";
                        break;
                }
            }

            if (!empty($filters['date_from'])) {
                $sqlFilter .= " AND invest.invested >= '{$filters['date_from']}'";
            }
            if (!empty($filters['date_until'])) {
                $sqlFilter .= " AND invest.invested <= '{$filters['date_until']}'";
            }

            $sql = "SELECT
                        invest.id as id,
                        invest.user as user,
                        invest.booka as booka,
                        invest.method as method,
                        invest.status as investStatus,
                        invest.amount as amount,
                        invest.anonymous as anonymous,
                        invest.resign as resign,
                        DATE_FORMAT(invest.invested, '%d | %m | %Y') as invested,
                        DATE_FORMAT(invest.charged , '%d | %m | %Y') as charged,
                        DATE_FORMAT(invest.returned, '%d | %m | %Y') as returned,
                        invest.admin as admin
                    FROM invest
                    INNER JOIN booka
                        ON invest.booka = booka.id
                    WHERE invest.booka IS NOT NULL
                        $sqlFilter
                    ORDER BY invest.id DESC
                    ";

            $query = self::query($sql, $values  );
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $item->booka = Booka::getMini($item->booka);
                $item->user = User::getMini($item->user);
                $list[$item->id] = $item;
            }
            return $list;
        }




        public function validate (&$errors = array()) { 
            if (!is_numeric($this->amount))
                $errors[] = 'La cantidad no es correcta';

            if (empty($this->method))
                $errors[] = 'Falta metodo de pago';

            if (empty($this->user))
                $errors[] = 'Falta usuario';

            if (empty($this->booka))
                $errors[] = 'Falta proyecto';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'user',
                'booka',
                'amount',
                'preapproval',
                'payment',
                'transaction',
                'method',
                'status',
                'anonymous',
                'resign',
                'invested',
                'charged',
                'returned',
                'admin'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if (!empty($this->$field)) {
                    if ($set != '') $set .= ", ";
                    $set .= "`$field` = :$field ";
                    $values[":$field"] = $this->$field;
                }
            }

            try {
                $sql = "REPLACE INTO invest SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                // y las recompensas
                foreach ($this->rewards as $reward) {
                    $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
                    self::query($sql, array(':invest'=>$this->id, ':reward'=>$reward));
                }

                // dirección
                if (!empty($this->address)) {
                    $sql = "REPLACE INTO invest_address (invest, user, address, zipcode, location, city, country, name, nif)
                        VALUES (:invest, :user, :address, :zipcode, :location, :city, :country, :name, :nif)";
                    self::query($sql, array(
                        ':invest'=>$this->id,
                        ':user'=>$this->user,
                        ':address'=>$this->address->address,
                        ':zipcode'=>$this->address->zipcode, 
                        ':location'=>$this->address->location, 
                        ':city'=>$this->address->city, 
                        ':country'=>$this->address->country,
                        ':name'=>$this->address->name,
                        ':nif'=>$this->address->nif
                        )
                    );
                }

                return true;
            } catch(\PDOException $e) {
                $errors[] = "El aporte no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }
        }

        /*
         * Para actualizar recompensa (o renuncia) y dirección
         */
        public function update (&$errors = array()) {

            self::query("START TRANSACTION");

            try {
                // si renuncia
                $sql = "UPDATE invest SET resign = :resign, amount = :amount, anonymous = :anonymous WHERE id = :id";
                self::query($sql, array(':id'=>$this->id, ':resign'=>$this->resign, ':amount'=>$this->amount, ':anonymous'=>$this->anonymous));

                // borramos als recompensas
                $sql = "DELETE FROM invest_reward WHERE invest = :invest";
                self::query($sql, array(':invest'=>$this->id));

                // y grabamos las nuevas
                foreach ($this->rewards as $reward) {
                    $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
                    self::query($sql, array(':invest'=>$this->id, ':reward'=>$reward));
                }

                // dirección
                if (!empty($this->address)) {
                    $sql = "REPLACE INTO invest_address (invest, user, address, zipcode, location, city, country, name, nif)
                        VALUES (:invest, :user, :address, :zipcode, :location, :city, :country, :name, :nif)";
                    self::query($sql, array(
                        ':invest'=>$this->id,
                        ':user'=>$this->user,
                        ':address'=>$this->address->address,
                        ':zipcode'=>$this->address->zipcode,
                        ':location'=>$this->address->location,
                        ':city'=>$this->address->city,
                        ':country'=>$this->address->country,
                        ':name'=>$this->address->name,
                        ':nif'=>$this->address->nif
                        )
                    );
                }

                self::query("COMMIT");

                return true;

            } catch (\PDOException $e) {
                self::query("ROLLBACK");
                $errors[] = "Envíanos esto: <br />" . $e->getMessage();
                return false;
            }
        }

        /*
         * Para pasar un aporte con incidencia a resuelta, cash y cobrado
         */
        public function solve (&$errors = array()) {

            self::query("START TRANSACTION");

            try {
                // si renuncia
                $sql = "UPDATE invest SET  method = 'cash', status = 1, issue = 0 WHERE id = :id";
                self::query($sql, array(':id'=>$this->id));

                // añadir detalle
                $sql = "INSERT INTO invest_detail (invest, type, log, date)
                    VALUES (:id, 'solved', :log, NOW())";

                self::query($sql, array(':id'=>$this->id, ':log'=>'Incidencia resuelta por el admin '.$_SESSION['user']->name.', aporte pasado a cash y cobrado'));


                self::query("COMMIT");

                return true;

            } catch (\PDOException $e) {
                self::query("ROLLBACK");
                $errors[] = $e->getMessage();
                return false;
            }
        }


        /* Lista de Bookas con aportes
         *
         * @param bool success solo los prroyectos en campaña, financiados o exitosos
         */
        public static function bookas ($success = false) {

            $list = array();

            $sql = "
                SELECT
                    booka.id as id,
                    booka.name_es as name
                FROM    booka
                INNER JOIN invest
                    ON booka.id = invest.booka
                    ";

            if ($success) {
                $sql .= " WHERE booka.status >= 3 AND booka.status <= 5 ";
            }
            $sql .= " ORDER BY name ASC";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         * Lista de usuarios que han aportado a algo
         */
        public static function users ($all = false) {

            $list = array();

            $sql = "
                SELECT
                    user.id as id,
                    user.name as name
                FROM    user
                INNER JOIN invest
                    ON user.id = invest.user
                ";
            
            if (!$all) {
                $sql .= "WHERE (user.hide = 0 OR user.hide IS NULL)
                    ";
            }
                $sql .= "ORDER BY user.name ASC
                ";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         * Lista de emails de usuarios que han aportado a algo
         */
        public static function emails ($all = false) {

            $list = array();

            $sql = "
                SELECT
                    user.id as id,
                    user.email as email
                FROM    user
                INNER JOIN invest
                    ON user.id = invest.user
                ";

            if (!$all) {
                $sql .= "WHERE (user.hide = 0 OR user.hide IS NULL)
                    ";
            }
                $sql .= "ORDER BY user.id ASC
                ";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[$item->id] = $item->email;
            }

            return $list;
        }


        /*
         * Lista de usuarios que aportan en el mismo proyecto que cierto usuario
         */
        public static function getCoinvestors ($user, $limit = null) {

             $array = array ();
            try {

                $values = array(':me'=>$user);

               $sql = "SELECT 
                            invest.user as id, 
                            user.name as name,
                            user.avatar as avatar,
                            DATE_FORMAT(invest.invested, '%d | %m | %Y') as date
                        FROM invest
                        INNER JOIN invest as mine
                            ON invest.booka = mine.booka
                            AND mine.user = :me
                            AND mine.status IN ('0', '1', '3')
                        INNER JOIN user
                            ON  user.id = invest.user
                            AND (user.hide = 0 OR user.hide IS NULL)
                        WHERE invest.user != :me
                        AND invest.status IN ('0', '1', '3')
                        AND (invest.anonymous = 0 OR invest.anonymous IS NULL)
                        GROUP BY invest.user
                        ORDER BY RAND()
                    ";
               if (!empty($limit)) {
                   $sql .= " LIMIT $limit";
               }
               
                $query = static::query($sql, $values);
                $shares = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($shares as $share) {

                    // nombre i avatar vienen en la sentencia, hay que sacar la imagen
                    $share['user'] = $share['id'];
                    $queryI = static::query("SELECT COUNT(DISTINCT(booka)) FROM invest WHERE user = ? AND status IN ('0', '1', '3')", array($share['id']));
                    $share['bookas'] = $queryI->fetchColumn(0);
                    $queryP = static::query("SELECT SUM(amount) FROM invest WHERE user = ? AND status IN ('0', '1', '3')", array($share['id']));
                    $share['amount'] = $queryP->fetchColumn(0);
                    $share['avatar'] = (empty($share['avatar'])) ? Image::get(1) : Image::get($share['avatar']);
                    if (!$share['avatar'] instanceof Image) {
                        $share['avatar'] = Image::get(1);
                    }
                    
                    $array[] = (object) $share;
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Base\Core\Exception($e->getMessage());
            }
            
        }

        /*
         * Obtenido por un proyecto
         */
        public static function invested ($booka) {
            $query = static::query("
                SELECT  SUM(amount) as much
                FROM    invest
                WHERE   booka = :booka
                AND     status IN ('0', '1', '3', '4')
                ", array(':booka' => $booka));
            $got = $query->fetchObject();
            return (int) $got->much;
        }

        /*
         * Usuarios que han aportado aun proyecto
         */
        public static function investors ($booka) {
            $investors = array();

            $sql = "
                SELECT
                    invest.user as user,
                    user.name as name,
                    user.avatar as avatar,
                    invest.amount as amount,
                    DATE_FORMAT(invest.invested, '%d | %m | %Y') as date,
                    user.hide as hide,
                    invest.anonymous as anonymous,
                    (
                        SELECT  COUNT(DISTINCT(ivb.booka)) 
                        FROM invest as ivb 
                        WHERE ivb.user = user.id        
                        AND ivb.status IN ('0', '1', '3', '4')
                    ) as bookas
                FROM    invest
                INNER JOIN user
                    ON  user.id = invest.user
                WHERE   booka = ?
                AND     invest.status IN ('0', '1', '3', '4')
                ORDER BY invest.invested DESC
                ";

            $query = self::query($sql, array($booka));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $investor) {

                $investor->avatar = Image::get($investor->avatar);
                if (empty($investor->avatar->id) || !$investor->avatar instanceof Image) {
                    $investor->avatar = Image::get(1);
                }
                
                // si el usuario es hide o el aporte es anonymo, lo ponemos como el usuario anonymous (avatar 1)
                if ($investor->hide == 1 || $investor->anonymous == 1) {

                    $investors[] = (object) array(
                        'user' => 'anonymous',
                        'name' => Text::get('regular-anonymous'),
                        'bookas' => null,
                        'avatar' => Image::get(1),
                        'amount' => $investor->amount,
                        'date' => $investor->date
                    );

                } else {
//$investor->user
                    $investors[] = (object) array(
                        'user' => $investor->user,
                        'name' => $investor->name,
                        'bookas' => $investor->bookas,
                        'avatar' => $investor->avatar,
                        'amount' => ($investors[$investor->user]->amount + $investor->amount),
                        'date' => $investor->date
                    );

                }

            }
            
            return $investors;
        }

        /* nuemro de cofinanciadores individuales, simpemente */
        public static function numInvestors ($booka) {
            $values = array(':booka' => $booka);

            $sql = "SELECT  COUNT(DISTINCT(user)) as investors
                FROM    invest
                WHERE   booka = :booka
                AND     invest.status IN ('0', '1', '3', '4')
                ";

            $query = static::query($sql, $values);
            $got = $query->fetchObject();
            return (int) $got->investors;
        }

        public static function my_numInvestors ($owner) {
            $values = array(':owner' => $owner);

            $sql = "SELECT  COUNT(DISTINCT(user)) as investors
                FROM    invest
                WHERE   project IN (SELECT id FROM project WHERE owner = :owner)
                AND     invest.status IN ('0', '1', '3', '4')
                ";

            $query = static::query($sql, $values);
            $got = $query->fetchObject();
            return (int) $got->investors;
        }
        /*
         *  Aportaciones realizadas por un usaurio
         *  devuelve total y fecha de la última
         */
        public static function supported ($user, $booka) {

            $sql = "
                SELECT  SUM(amount) as total, DATE_FORMAT(invested, '%d | %m | %Y') as date
                FROM    invest
                WHERE   user = :user
                AND     booka = :booka
                AND     invest.status IN ('0', '1', '3', '4')
                AND     (anonymous = 0 OR anonymous IS NULL)
                ";

            $query = self::query($sql, array(':user' => $user, ':booka' => $booka));
            return $query->fetchObject();
        }

        /*
         * Numero de cofinanciadores que han optado por cierta recompensa
         */
        public static function choosed ($reward) {

            $users = array();

            $sql = "
                SELECT  DISTINCT(user) as user
                FROM    invest
                INNER JOIN invest_reward
                    ON invest_reward.invest = invest.id
                    AND invest_reward.reward = ?
                INNER JOIN user
                    ON  user.id = invest.user
                    AND (user.hide = 0 OR user.hide IS NULL)
                WHERE   invest.status IN ('0', '1', '3', '4')
                ";

            $query = self::query($sql, array($reward));
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $investor) {
                $users[] = $investor['user'];
            }

            return $users;
        }


        /*
         * Asignar a la aportación una recompensas
         */
        public function setReward ($reward) {

            $values = array(
                ':invest' => $this->id,
                ':reward' => $reward
            );

            $sql = "REPLACE INTO invest_reward (invest, reward) VALUES (:invest, :reward)";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }
        }

        /*
         *  Actualiza el mail de la cuenta utilizada al registro del aporte
         */
        public function setAccount ($account) {

            $values = array(
                ':id' => $this->id,
                ':account' => $account
            );

            $sql = "UPDATE invest SET account = :account WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Marcar una recompensa como cumplida (o desmarcarla)
         */
        public static function setFulfilled ($invest, $reward, $value = '1') {

            $values = array(
                ':value' => $value,
                ':invest' => $invest,
                ':reward' => $reward
            );

            $sql = "UPDATE invest_reward SET fulfilled = :value WHERE invest=:invest AND reward=:reward";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }
        }

        /*
         *  Cambia el estado de un aporte
         */
        public function setStatus ($status) {

            if (!in_array($status, array('-1', '0', '1', '2', '3', '4', '5'))) {
                return false;
            }

            $values = array(
                ':id' => $this->id,
                ':status' => $status
            );

            $sql = "UPDATE invest SET status = :status WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         *  Pone el pay key al registro del aporte y la fecha de cargo
         */
        public function setPayment ($key) {

            $values = array(
                ':id' => $this->id,
                ':payment' => $key,
                ':charged' => date('Y-m-d')
            );

            $sql = "UPDATE  invest
                    SET
                        payment = :payment,
                        charged = :charged, 
                        status = 1
                    WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         *  Pone el codigo de la transaccion al registro del aporte
         */
        public function setTransaction ($code) {

            $values = array(
                ':id' => $this->id,
                ':transaction' => $code
            );

            $sql = "UPDATE invest SET transaction = :transaction WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         *  marca un aporte como devuelto (devuelto el dinero despues de haber sido cargado)
         */
        public function returnPayment () {

            $values = array(
                ':id' => $this->id,
                ':returned' => date('Y-m-d')
            );

            $sql = "UPDATE  invest
                    SET
                        returned = :returned,
                        status = 2
                    WHERE id = :id";
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Marcar esta aportación como cancelada
         */
        public function cancel () {

            $values = array(
                ':id' => $this->id,
                ':returned' => date('Y-m-d')
            );

            $sql = "UPDATE invest SET
                        returned = :returned,
                        status = 2
                    WHERE id = :id";
            
            if (self::query($sql, $values)) {
                return true;
            } else {
                return false;
            }

        }

        /* Para marcar que es una incidencia */
        public static function setIssue($id) {
           self::query("UPDATE invest SET issue = 1 WHERE id = :id", array(':id' => $id));
        }

        /* Para desmarcar incidencia */
        public static function unsetIssue($id) {
           self::query("UPDATE invest SET issue = 0 WHERE id = :id", array(':id' => $id));
        }

        /*
         * Estados del aporte
         */
        public static function status ($id = null) {
            $array = array (
                -1 => 'En proceso',
                0  => 'Posible incidencia',
                1  => 'Cobrado',
                2  => 'Cancelado',
                3  => 'Pagado al publisher',
                4  => 'Irrecuperable',
                5  => 'Reubicado'
            );

            if (isset($id)) {
                return $array[$id];
            } else {
                return $array;
            }

        }

        /*
         * Métodos de pago
         */
        public static function methods () {
            return array (
                'paypal' => 'Paypal',
                'tpv'    => 'Tarjeta',
                'cash'   => 'Manual'
            );
        }


        /*
         * Metodo para obtener datos para el informe completo (con incidencias y netos)
         */
         public static function getReportData($booka, $status) {
             $Data = array('Data');

             return $Data;
         }

         public static function getReportIssues($id) {

             $status = self::status();

             $list = array();

             $values = array(':id' => $id);

             $sql = "SELECT
                        invest.id as invest,
                        user.id as user,
                        user.name as userName,
                        user.email as userEmail,
                        invest.amount as amount,
                        invest.status as status
                    FROM invest
                    INNER JOIN user
                        ON user.id = invest.user
                    WHERE invest.project = :id
                    AND invest.issue = 1
                    ORDER BY user.name DESC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $item->statusName = $status[$item->status];
                $list[] = $item;
            }
            return $list;


         }

         public static function setDetail($id, $type, $log) {
             $values = array(
                ':id' => $id,
                ':type' => $type,
                ':log' => $log
            );

            $sql = "REPLACE INTO invest_detail (invest, type, log, date)
                VALUES (:id, :type, :log, NOW())";

            self::query($sql, $values);


         }

         public static function getDetails($id) {

             $list = array();

             $values = array(':id' => $id);

             $sql = "SELECT
                        type,
                        log,
                        DATE_FORMAT(invest_detail.date, '%d/%m/%Y %H:%i:%s') as date
                    FROM invest_detail
                    WHERE invest = :id
                    ORDER BY invest_detail.date DESC
                    ";

            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
                $list[] = $item;
            }
            return $list;


         }

    }
    
}