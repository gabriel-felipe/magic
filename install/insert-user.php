<?php
    $pessoa = new dbUser('pessoas');
    $pessoa->nome = 'Gabriel';
    $pessoa->sobrenome = 'Felipe';
    $pessoa->sexo = 'Masculino';
    $pessoa->save();
    $pessoa->last();
    $user = new dbUser('admin');
    $salt = mt_rand();
    $user->username = 'admin';
    $user->password = crypt("password",$salt);
    $user->pessoa_id = $pessoa->pessoa_id;

?>