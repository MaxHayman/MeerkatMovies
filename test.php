<?php

/*
 *	Meerkat Movies Rest API Client
 *  Author: Max Hayman <maxhayman@maxhayman.co.uk>
 */

require_once('MeerkatMovies.php');

$mm = new MeerkatMovies\Code('90178046', 'film edi');

if(!$mm->check()) {
	echo "Code is invalid\n";
	die;
}

echo "Code is valid\n";

if(!$mm->lock()) {
	echo "meerkat movie code is invalid\n";
	die;
}

echo "Code is locked\n";

if(!$mm->commit()) {
	echo "meerkat movie code is invalid\n";
	die;
}

echo "Code is committed\n";

if(!$mm->release()) {
	echo "meerkat movie code is invalid\n";
	die;
}

echo "Code is released\n";