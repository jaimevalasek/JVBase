<?php

namespace JVBase;

return array(
    'view_helpers' => array(
        'factories' => array(
            'router' => function ($sm) {
                return new \JVBase\View\Helper\Router($sm->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch());
            },
            'uri' => function ($sm) {
                return new \JVBase\View\Helper\Uri($sm->getServiceLocator()->get('application')->getRequest()->getQuery()->toArray());
            },
        ),
    )
);