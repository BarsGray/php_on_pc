<?php

require_once(__DIR__ . '/wp-load.php');

$user = get_user_by('login', 'admin'); // ВАЖНО: реальный логин

if ($user) {
    wp_set_password('newpassword123', $user->ID);
    echo "Пароль изменён";
} else {
    echo "Пользователь не найден";
}