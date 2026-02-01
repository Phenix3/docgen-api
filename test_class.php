<?php
require 'vendor/autoload.php';

if (class_exists('App\Entity\Template')) {
    echo "Class App\Entity\Template exists.\n";
} else {
    echo "Class App\Entity\Template DOES NOT exist.\n";
}

if (class_exists('App\Entity\Document')) {
    echo "Class App\Entity\Document exists.\n";
} else {
    echo "Class App\Entity\Document DOES NOT exist.\n";
}
