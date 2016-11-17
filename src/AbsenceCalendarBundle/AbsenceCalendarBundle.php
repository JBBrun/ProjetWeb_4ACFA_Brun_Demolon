<?php

namespace AbsenceCalendarBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AbsenceCalendarBundle extends Bundle
{
    public function getParent()
    {
        return 'BladeTesterCalendarBundle';
    }
}
