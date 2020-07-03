<?php

defined('INACTIVE') OR define('INACTIVE', 0);

defined('WAITING') OR define('WAITING', 0);

defined('CLOSE') OR define('CLOSE', 0);

defined('OPEN') OR define('OPEN', 1);

defined('ACTIVE') OR define('ACTIVE', 1);

defined('AVAILABLE') OR define('AVAILABLE', 1);

defined('PAYMENT_APP') OR define('PAYMENT_APP', 1);

defined('PAYMENT_DELIVERY') OR define('PAYMENT_DELIVERY', 2);

defined('PAYMENT_DESCRIPTION') OR define('PAYMENT_DESCRIPTION', 'Meu Pedido');

// Order status

defined('WAITING_CONFIRMATION') OR define('WAITING_CONFIRMATION', 0);

defined('PREPARING') OR define('PREPARING', 1);

defined('ON_THE_WAY') OR define('ON_THE_WAY', 2);

defined('DELIVERED') OR define('DELIVERED', 3);

defined('REFUSED') OR define('REFUSED', 4);