<?php

require_once('MeerkatMovies.php');


$mm = new MeerkatMovies\Code('code', 'film edi');


if(!$mm->check()) {
	echo "Code is invalid\n";
	die;
}

echo "Co"

if(!$mm->lock()) {
	echo "meerkat movie code is invalid\n";
} else {
	echo "meerkat movie code is invalid\n";
}

if($mm->commit()) {
	echo "meerkat movie code is commit\n";
} else {
	echo "meerkat movie code is invalid\n";
}