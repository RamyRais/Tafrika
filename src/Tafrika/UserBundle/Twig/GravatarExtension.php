<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 28/02/15
 * Time: 17:39
 */

namespace Tafrika\UserBundle\Twig;

class GravatarExtension extends \Twig_Extension {

    // the magic function that makes this easy
    public function getFilters()
    {
        return array(
            'getGravatarImage'    => new \Twig_Filter_Method($this, 'getGravatarImage'),
        );
    }

    // get gravatar image
    public function getGravatarImage($email, $size = 80, $defaultImage = 'mm', $rating = 'G')
    {
        return  $grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $defaultImage )
            . "&s=" . $size . '&r=' . $rating;
    }

    // for a service we need a name
    public function getName()
    {
        return 'gravatar';
    }

}