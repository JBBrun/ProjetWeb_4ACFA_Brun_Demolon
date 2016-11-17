<?php

namespace AbsenceUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AbsenceUserBundle extends Bundle
{

    public function getParent()

    {

        return 'FOSUserBundle';

    }
}
