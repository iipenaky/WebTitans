<?php

$author = 'Gandalf the Gray';
$out = <<<_GAN
        They have taken the 
        bridge and the second hall.
        We have barred the gates
        but cannot hold them for long.
        The ground shakes, drums...
        drums in the deep. We cannot get out.
        A shadow lurks in the dark. We can not get out.
        They are coming.

        - $author
    _GAN;

printf(nl2br($out));
phpinfo();
