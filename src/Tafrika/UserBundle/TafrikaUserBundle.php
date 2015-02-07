<?php

namespace Tafrika\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TafrikaUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
