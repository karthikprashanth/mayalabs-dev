<?php
    include "phmagick/phmagick.php";
    
    $p = new phmagick('sample.jpg','spork.jpg');
    $p->resize(300); 
    echo "hey";
?>
