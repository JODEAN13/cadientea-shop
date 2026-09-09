<?php
require_once 'function.php';

logoutUser();

setFlash('success', 'You have been logged out. See you next time! 🌸');
redirect('login.php');