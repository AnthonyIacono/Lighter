<?php

Lib::Import('number_route');

Router::Add('/', array('controller' => 'home', 'action' => 'index'));
Router::Add('/:number', array('controller' => 'home', 'action' => 'number', 'class' => 'NumberRoute'));