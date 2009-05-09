<?php

$tpl->addFile('_begin','_header.tpl.phtml')
	->addFile('_end','_footer.tpl.phtml')
	->addFile('dashboard','admin-dashboard.tpl.phtml');

$tpl->render('dashboard');